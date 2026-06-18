<?php

namespace App\DTOs;

use App\Http\Requests\Auth\RegisterRequest;
use Spatie\LaravelData\Data;

class RegisterDto extends Data
{
    public function __construct(
        #[MapInputName('first_name')]
        public string $firstName,
        #[MapInputName('last_name')]
        public string $lastName,
        public string $email,
        public ?string $phone,
        public string $password,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            $request->validated('first_name'),
            $request->validated('last_name'),
            $request->validated('email'),
            $request->validated('phone'),
            $request->validated('password'),
        );
    }
}
