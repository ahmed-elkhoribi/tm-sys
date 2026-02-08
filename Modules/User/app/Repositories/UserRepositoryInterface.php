<?php

namespace Modules\User\Repositories;

use Modules\User\Entities\User;

interface UserRepositoryInterface
{
    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Find user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;
}
