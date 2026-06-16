<?php

namespace App\Models;

use FalaqX\Core\Model;

/**
 * Example: UserModel
 */
class UserModel extends Model
{
    protected string $table      = 'users';
    protected string $primaryKey = 'id';
    protected array  $fillable   = ['name', 'email', 'password'];

    /** Find a user by email address. */
    public function findByEmail(string $email): array|false
    {
        return $this->findBy('email', $email);
    }

    /** Return only active users. */
    public function active(): array
    {
        return $this->where('status', 'active');
    }
}
