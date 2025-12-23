<?php

declare(strict_types=1);

namespace App\Http\Livewire\Courses;

use App\Domains\Courses\Models\Lesson;
use Livewire\Component;

class VideoPlayer extends Component
{
    public Lesson $lesson;

    public function mount(Lesson $lesson): void
    {
        $this->lesson = $lesson;
    }

    public function render()
    {
        return view('livewire.courses.video-player');
    }
}

