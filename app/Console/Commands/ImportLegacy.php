<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\AiTools\Models\AiTool;
use App\Domains\Blog\Models\Post;
use App\Domains\Auth\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportLegacy extends Command
{
    protected $signature = 'import:legacy';

    protected $description = 'Import legacy WordPress/WooCommerce data (users, AI tools, posts, forms).';

    public function handle(): int
    {
        $legacy = DB::connection('legacy');

        $this->importUsers($legacy);
        $this->importAiTools($legacy);
        $this->importPosts($legacy);
        $this->importForms($legacy);
        $this->importOrders($legacy);

        $this->info('Legacy import completed.');

        return self::SUCCESS;
    }

    private function importUsers($legacy): void
    {
        $this->info('Importing users...');
        $legacy->table('wp_users')->orderBy('ID')->chunk(200, function ($chunk) use ($legacy) {
            $userIds = $chunk->pluck('ID');
            $usermeta = $legacy->table('wp_usermeta')
                ->whereIn('user_id', $userIds)
                ->get()
                ->groupBy('user_id');

            foreach ($chunk as $row) {
                $meta = $usermeta->get($row->ID, collect());
                $mobile = $meta->firstWhere('meta_key', 'billing_phone')->meta_value ?? null;
                $bio = $meta->firstWhere('meta_key', 'description')->meta_value ?? null;

                User::updateOrCreate(
                    ['email' => $row->user_email],
                    [
                        'username' => $row->user_login,
                        'name' => $row->display_name,
                        'mobile' => $mobile,
                        'bio' => $bio,
                        'legacy_password' => $row->user_pass,
                        'password' => Str::password(),
                    ]
                );
            }
        });
    }

    private function importAiTools($legacy): void
    {
        $this->info('Importing AI Tools...');
        $legacy->table('wp_posts')
            ->where('post_type', 'ai_tool')
            ->where('post_status', 'publish')
            ->orderBy('ID')
            ->chunk(200, function ($chunk) use ($legacy) {
                $postIds = $chunk->pluck('ID');
                $meta = $legacy->table('wp_postmeta')
                    ->whereIn('post_id', $postIds)
                    ->get()
                    ->groupBy('post_id');

                foreach ($chunk as $post) {
                    $m = $meta->get($post->ID, collect());
                    AiTool::updateOrCreate(
                        ['slug' => $post->post_name],
                        [
                            'name' => $post->post_title,
                            'short_description' => $post->post_excerpt,
                            'content' => $post->post_content,
                            'website_url' => $m->firstWhere('meta_key', '_ai_tool_url')->meta_value ?? null,
                            'demo_url' => $m->firstWhere('meta_key', '_ai_tool_demo_url')->meta_value ?? null,
                            'pricing_type' => $m->firstWhere('meta_key', '_ai_tool_price') ? 'paid' : 'free',
                            'price' => $m->firstWhere('meta_key', '_ai_tool_price')->meta_value ?? null,
                            'rating' => $m->firstWhere('meta_key', '_ai_tool_rating')->meta_value ?? 0,
                            'users_count' => (int) ($m->firstWhere('meta_key', '_ai_tool_users_count')->meta_value ?? 0),
                            'success_rate' => $m->firstWhere('meta_key', '_ai_tool_success_rate')->meta_value ?? null,
                            'response_time' => $m->firstWhere('meta_key', '_ai_tool_response_time')->meta_value ?? null,
                            'features' => $this->explodeMeta($m->firstWhere('meta_key', '_ai_tool_features')->meta_value ?? ''),
                            'languages' => $this->explodeMeta($m->firstWhere('meta_key', '_ai_tool_languages')->meta_value ?? ''),
                            'company' => $m->firstWhere('meta_key', '_ai_tool_company')->meta_value ?? null,
                            'published_at' => $post->post_date,
                        ]
                    );
                }
            });
    }

    private function importPosts($legacy): void
    {
        $this->info('Importing blog posts...');
        $legacy->table('wp_posts')
            ->whereIn('post_type', ['post', 'news'])
            ->where('post_status', 'publish')
            ->orderBy('ID')
            ->chunk(200, function ($chunk) {
                foreach ($chunk as $post) {
                    Post::updateOrCreate(
                        ['slug' => $post->post_name],
                        [
                            'author_id' => 1,
                            'type' => $post->post_type === 'news' ? 'news' : 'article',
                            'title' => $post->post_title,
                            'excerpt' => $post->post_excerpt,
                            'content' => $post->post_content,
                            'status' => 'published',
                            'published_at' => $post->post_date,
                        ]
                    );
                }
            });
    }

    private function importOrders($legacy): void
    {
        if (! $legacy->getSchemaBuilder()->hasTable('wp_wc_orders')) {
            $this->warn('wp_wc_orders not found. Skipping orders.');
            return;
        }

        $this->info('Importing orders...');

        $legacy->table('wp_wc_orders')
            ->orderBy('id')
            ->chunk(200, function ($orders) use ($legacy) {
                $orderIds = $orders->pluck('id');
                $addresses = $legacy->table('wp_wc_order_addresses')
                    ->whereIn('order_id', $orderIds)
                    ->get()
                    ->groupBy('order_id');
                $items = $legacy->table('wp_wc_order_product_lookup')
                    ->whereIn('order_id', $orderIds)
                    ->get()
                    ->groupBy('order_id');

                foreach ($orders as $legacyOrder) {
                    $billing = $addresses->get($legacyOrder->id, collect())
                        ->firstWhere('address_type', 'billing');

                    $order = \App\Domains\Commerce\Models\Order::create([
                        'user_id' => $legacyOrder->customer_id ?: null,
                        'status' => $this->mapOrderStatus($legacyOrder->status),
                        'total_amount' => $legacyOrder->total_amount ?? 0,
                        'gateway' => $legacyOrder->payment_method ?? 'zarinpal',
                        'gateway_ref_id' => $legacyOrder->transaction_id,
                        'billing_address' => $billing ? [
                            'first_name' => $billing->first_name,
                            'last_name' => $billing->last_name,
                            'email' => $billing->email,
                            'phone' => $billing->phone,
                            'city' => $billing->city,
                            'address_1' => $billing->address_1,
                        ] : null,
                        'ip_address' => $legacyOrder->ip_address,
                        'created_at' => $legacyOrder->date_created_gmt,
                        'updated_at' => $legacyOrder->date_updated_gmt,
                    ]);

                    $orderItems = $items->get($legacyOrder->id, collect());
                    if ($orderItems->isEmpty()) {
                        \App\Domains\Commerce\Models\OrderItem::create([
                            'order_id' => $order->id,
                            'orderable_type' => null,
                            'orderable_id' => null,
                            'price' => $legacyOrder->total_amount ?? 0,
                            'quantity' => 1,
                        ]);
                    } else {
                        foreach ($orderItems as $it) {
                            \App\Domains\Commerce\Models\OrderItem::create([
                                'order_id' => $order->id,
                                'orderable_type' => null,
                                'orderable_id' => $it->product_id,
                                'price' => $it->product_gross_revenue ?? 0,
                                'quantity' => $it->product_qty ?? 1,
                            ]);
                        }
                    }
                }
            });
    }

    private function mapOrderStatus(?string $status): string
    {
        return match ($status) {
            'wc-completed' => \App\Enums\OrderStatus::Paid->value,
            'wc-failed' => \App\Enums\OrderStatus::Failed->value,
            'wc-refunded' => \App\Enums\OrderStatus::Refunded->value,
            default => \App\Enums\OrderStatus::Pending->value,
        };
    }

    private function importForms($legacy): void
    {
        $this->info('Archiving forms...');
        if (! $legacy->getSchemaBuilder()->hasTable('wp_e_submissions')) {
            $this->warn('wp_e_submissions table not found. Skipping.');
            return;
        }

        $legacy->table('wp_e_submissions')->orderBy('id')->chunk(200, function ($chunk) use ($legacy) {
            $submissionIds = $chunk->pluck('id');
            $values = $legacy->table('wp_e_submissions_values')
                ->whereIn('submission_id', $submissionIds)
                ->get()
                ->groupBy('submission_id');

            foreach ($chunk as $row) {
                DB::table('form_archives')->insert([
                    'type' => $row->type,
                    'source_table' => 'wp_e_submissions',
                    'original_id' => $row->id,
                    'payload' => json_encode([
                        'meta' => $row,
                        'values' => $values->get($row->id, collect()),
                    ]),
                    'created_at' => $row->created_at,
                ]);
            }
        });
    }

    private function explodeMeta(string $value): array
    {
        return collect(preg_split('/[,\\n]/', $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}

