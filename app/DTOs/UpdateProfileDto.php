<?php

namespace App\DTOs;

use App\Http\Requests\UpdateProfileRequest;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Data;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class UpdateProfileDto extends Data
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public ?string $email,
        public ?string $phone,
    ) {}

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        return self::from($request->validated());
    }
}
