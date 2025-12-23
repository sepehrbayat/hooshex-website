<?php

declare(strict_types=1);

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    public function aiTools(): MorphToMany
    {
        return $this->morphedByMany(
            \App\Domains\AiTools\Models\AiTool::class,
            'categorizable'
        );
    }

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(
            \App\Domains\Blog\Models\Post::class,
            'categorizable'
        );
    }
}

