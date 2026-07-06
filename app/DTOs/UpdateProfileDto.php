<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\UpdateProfileRequest;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class UpdateProfileDto extends Data
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        public ?string $email,
        public ?string $phone,
    ) {
    }

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        return self::from($request->validated());
    }
}
