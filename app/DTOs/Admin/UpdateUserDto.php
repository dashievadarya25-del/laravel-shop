<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

use App\Http\Requests\Admin\UpdateUserRequest;

class UpdateUserDto
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly string $status,
        public readonly int $roleId,
        public readonly ?string $password = null,
    ) {
    }

    public static function fromRequest(UpdateUserRequest $request): self
    {
        return new self(
            firstName: $request->input('first_name'),
            lastName: $request->input('last_name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            status: $request->input('status'),
            roleId: (int) $request->input('role_id'),
            password: $request->input('password'),
        );
    }
}
