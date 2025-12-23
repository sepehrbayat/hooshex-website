<?php

declare(strict_types=1);

namespace App\Livewire\Interactions;

use App\Interactions\Review;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\Attributes\Validate;

class ReviewForm extends Component
{
    use AuthorizesRequests;

    public $model; // Course or AiTool (polymorphic)

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 5;

    #[Validate('nullable|string|max:255')]
    public ?string $title = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $body = null;

    public bool $hasReviewed = false;

    public function mount($model): void
    {
        $this->model = $model;

        // Check if user has already reviewed
        if (auth()->check()) {
            $this->hasReviewed = Review::where('user_id', auth()->id())
                ->where('reviewable_type', get_class($this->model))
                ->where('reviewable_id', $this->model->id)
                ->exists();
        }
    }

    public function submit(): void
    {
        if (!auth()->check()) {
            session()->flash('error', 'برای ارسال نقد باید وارد شوید.');
            return;
        }

        if ($this->hasReviewed) {
            session()->flash('error', 'شما قبلاً برای این مورد نقد ارسال کرده‌اید.');
            return;
        }

        $this->validate();

        $review = Review::create([
            'user_id' => auth()->id(),
            'reviewable_type' => get_class($this->model),
            'reviewable_id' => $this->model->id,
            'rating' => $this->rating,
            'title' => $this->title,
            'body' => $this->body,
            'status' => 'pending',
        ]);

        // Send database notification to admins
        $admins = \App\Domains\Auth\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::make()
                ->title('نقد جدید')
                ->body('یک نقد جدید دریافت شده که نیاز به تایید دارد.')
                ->success()
                ->sendToDatabase($admin);
        }

        $this->hasReviewed = true;
        $this->reset(['rating', 'title', 'body']);
        session()->flash('success', 'نقد شما با موفقیت ارسال شد و پس از تایید نمایش داده می‌شود.');
        $this->dispatch('review-added');
    }

    public function render()
    {
        $reviews = Review::where('reviewable_type', get_class($this->model))
            ->where('reviewable_id', $this->model->id)
            ->where('status', 'approved')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->count();

        return view('livewire.interactions.review-form', [
            'reviews' => $reviews,
            'averageRating' => round($averageRating, 1),
            'totalReviews' => $totalReviews,
        ]);
    }
}
