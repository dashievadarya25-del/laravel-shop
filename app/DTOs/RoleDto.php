<?php

declare(strict_types=1);

namespace App\DTOs;

class RoleDto
{
    public function __construct(
        public string $name,
        public string $slug,
    ) {
    }

}
