<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTOs\RegisterDto;
use App\DTOs\UpdateProfileDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressStoreRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Address;
use App\Services\Auth\UserService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /** Показ формы регистрации */
    public function showRegistrationForm(): Factory|View
    {
        return view('auth.register');
    }

    /** Обработка регистрации */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $dto = RegisterDto::fromRequest($request);
        $user = $this
            ->userService
            ->register($dto);

        return redirect()
            ->route('login.form')
            ->with('status', 'Регистрация прошла успешно');
    }

    public function showLoginForm(): Factory|View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            return redirect()->intended('profile');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login.form');
    }

    public function showProfile(): Factory|View
    {
        $user = Auth::user();
        $addresses = $user->addresses()->get();

        return view('auth.profile', compact('user', 'addresses'));
    }

    public function makeAddressDefault(Address $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $this->userService->setDefaultAddress(Auth::user(), $address);

        return back()->with('status', 'Основной адрес успешно изменен!');
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $dto = UpdateProfileDto::fromRequest($request);
        $user = auth()->user();
        $this->userService->updateProfile($dto, $user);

        return redirect()->route('profile.form');
    }

    public function showChangePasswordForm(): Factory|View
    {
        return view('auth.change-password');
    }

    /**
     * @throws ValidationException
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $user = Auth::user();

        $this
            ->userService
            ->updatePassword(
                $user,
                $validatedData['current_password'],
                $validatedData['new_password']
            );

        return redirect()
            ->route('profile.form')
            ->with('status', 'Password successfully changed!');
    }

    public function storeAddress(AddressStoreRequest $request): RedirectResponse
    {
        $this->userService->createAddress(
            Auth::user(),
            $request->validated()
        );

        return back()->with('status', 'Новый адрес успешно добавлен!');
    }
}
