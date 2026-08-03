<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTOs\RegisterDto;
use App\DTOs\UpdateProfileDto;
use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function register(RegisterDto $dto): User
    {
        $user = new User();
        $user->first_name = $dto->firstName;
        $user->last_name = $dto->lastName;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->save();

        return $user;
    }

    /**
     * @throws AuthenticationException
     */
    public function updateProfile(UpdateProfileDto $dto): void
    {
        $user = Auth::user();
        $user->fill($dto->toArray());
        $user->save();
    }

    /**
     * @throws ValidationException
     */
    public function updatePassword(
        User $user,
        string $currentPassword,
        string $newPassword
    ): void {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Invalid current password']);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
    }

    public function setDefaultAddress(User $user, Address $address): void
    {
        $user->addresses()->update(['is_default' => false]);

        $address->update(['is_default' => true]);
    }

    public function createAddress(User $user, array $data): Address
    {
        $isFirst = !$user->addresses()->exists();

        return $user->addresses()->create(
            array_merge($data, ['is_default' => $isFirst])
        );
    }
}
