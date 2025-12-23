<?php

declare(strict_types=1);

namespace App\Domains\Auth\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Enrollment;
use App\Domains\Courses\Models\Teacher;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'mobile',
        'role',
        'bio',
        'avatar_path',
        'social_links',
        'password',
        'legacy_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
            'role' => UserRole::class,
        ];
    }

    /**
     * Teacher profile (if user is a teacher)
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Courses taught by this user (teacher)
     */
    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /**
     * Courses enrolled by this user (student)
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Courses enrolled by this user (student) - relation through enrollments
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withTimestamps();
    }

    /**
     * Check if user is enrolled in a course
     */
    public function isEnrolled(Course $course): bool
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if user is teacher
     */
    public function isTeacher(): bool
    {
        return $this->role === UserRole::Teacher;
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->role === UserRole::Student;
    }
}

