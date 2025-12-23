<?php

declare(strict_types=1);

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class LegacyUserProvider extends EloquentUserProvider
{
    public function __construct(HasherContract $hasher, string $model)
    {
        parent::__construct($hasher, $model);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plain = (string) ($credentials['password'] ?? '');

        // First try the primary hasher.
        if ($this->hasher->check($plain, $user->getAuthPassword())) {
            return true;
        }

        // Fallback to legacy WordPress-style MD5 hashes.
        if (!empty($user->legacy_password) && hash_equals((string) $user->legacy_password, md5($plain))) {
            // Promote the password to the current hasher.
            $user->forceFill([
                'password' => $this->hasher->make($plain),
                'legacy_password' => null,
            ])->save();

            return true;
        }

        return false;
    }
}

