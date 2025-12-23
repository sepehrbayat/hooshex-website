<?php

declare(strict_types=1);

namespace App\Livewire\Interactions;

use App\Interactions\Comment;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\Attributes\Validate;

class CommentSection extends Component
{
    use AuthorizesRequests;

    public $model; // Post or Lesson (polymorphic)

    #[Validate('required|string|max:2000')]
    public string $body = '';

    public function mount($model): void
    {
        $this->model = $model;
    }

    public function submit(): void
    {
        if (!auth()->check()) {
            session()->flash('error', 'برای ارسال نظر باید وارد شوید.');
            return;
        }

        $this->validate();

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'commentable_type' => get_class($this->model),
            'commentable_id' => $this->model->id,
            'body' => $this->body,
            'status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Send database notification to admins
        $admins = \App\Domains\Auth\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            Notification::make()
                ->title('نظر جدید')
                ->body('یک نظر جدید دریافت شده که نیاز به تایید دارد.')
                ->success()
                ->sendToDatabase($admin);
        }

        $this->body = '';
        session()->flash('success', 'نظر شما با موفقیت ارسال شد و پس از تایید نمایش داده می‌شود.');
        $this->dispatch('comment-added');
    }

    public function render()
    {
        $comments = Comment::where('commentable_type', get_class($this->model))
            ->where('commentable_id', $this->model->id)
            ->where('status', 'approved')
            ->whereNull('parent_id')
            ->with(['user', 'replies' => function ($query) {
                $query->where('status', 'approved')->with('user');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.interactions.comment-section', [
            'comments' => $comments,
        ]);
    }
}
