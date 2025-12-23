<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Services;

use App\Domains\Courses\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Arr;

class Cart
{
    private const SESSION_KEY = 'cart.items';

    public function items(): Collection
    {
        return collect(Session::get(self::SESSION_KEY, []));
    }

    public function addCourse(Course $course, int $quantity = 1): void
    {
        $items = $this->items();

        $items->put($this->key($course), [
            'type' => Course::class,
            'id' => $course->id,
            'title' => $course->title,
            'price' => (int) ($course->sale_price ?? $course->price),
            'quantity' => $quantity,
        ]);

        Session::put(self::SESSION_KEY, $items->all());
    }

    public function remove(string $key): void
    {
        $items = $this->items();
        $items->forget($key);
        Session::put(self::SESSION_KEY, $items->all());
    }

    public function total(): int
    {
        return $this->items()->sum(fn ($item) => ($item['price'] ?? 0) * ($item['quantity'] ?? 1));
    }

    public function count(): int
    {
        return $this->items()->sum(fn ($item) => (int) ($item['quantity'] ?? 0));
    }

    public function toArray(): array
    {
        return $this->items()->values()->all();
    }

    public function syncFromLocalStorage(array $items): void
    {
        $normalized = collect($items)
            ->map(fn ($item) => $this->normalizeItem($item))
            ->filter()
            ->keyBy(fn ($item) => $item['key'])
            ->map(fn ($item) => Arr::except($item, 'key'));

        Session::put(self::SESSION_KEY, $normalized->all());
    }

    private function normalizeItem(array $item): ?array
    {
        $type = $item['type'] ?? null;
        $id = $item['id'] ?? null;
        $quantity = max(1, (int) ($item['quantity'] ?? 1));

        if ($type === Course::class || $type === 'course') {
            $course = Course::find($id);
            if (! $course) {
                return null;
            }

            return [
                'key' => $this->key($course),
                'type' => Course::class,
                'id' => $course->id,
                'title' => $course->title,
                'price' => (int) ($course->sale_price ?? $course->price),
                'quantity' => $quantity,
            ];
        }

        return null;
    }

    private function key(Course $course): string
    {
        return 'course_'.$course->id;
    }
}

