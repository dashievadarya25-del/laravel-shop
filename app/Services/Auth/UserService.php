<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTOs\Admin\UpdateUserDto;
use App\DTOs\RegisterDto;
use App\DTOs\UpdateProfileDto;
use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

    public function createFromAdmin(array $data): User
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'status'     => $data['status'],
            'password'   => Str::random(10),
        ]);

        if (!empty($data['role_id'])) {
            $user->roles()->sync([$data['role_id']]);
        }

        return $user;
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->update([
            'password' => $password
        ]);
    }

    public function updateByAdmin(User $user, UpdateUserDto $dto): User
    {
        $user->first_name = $dto->firstName;
        $user->last_name = $dto->lastName;
        $user->email = $dto->email;
        $user->phone = $dto->phone;
        $user->status = $dto->status;

        if ($dto->password) {
            $user->password = $dto->password;
        }

        $user->save();

        if ($dto->roleId) {
            $user->roles()->sync([$dto->roleId]);
        }

        return $user;
    }
}
