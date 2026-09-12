<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTOs\Admin\UpdateUserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function index(Request $request): View
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserStoreRequest $request, UserService $service): RedirectResponse
    {
        $service->createFromAdmin($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Пользователь успешно создан');
    }

    public function show(User $user): View
    {
        $user->load(['roles', 'orders', 'addresses']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $dto = UpdateUserDto::fromRequest($request);
        $this->userService->updateByAdmin($user, $dto); // Если нужен UserService в update, добавьте его в аргументы метода или конструктор

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Данные пользователя успешно обновлены');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Вы не можете удалить свой собственный аккаунт']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Пользователь успешно удален');
    }

    public function resetPassword(Request $request, User $user, UserService $service): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        $service->resetPassword($user, $validated['password']);

        return back()->with('status', 'Пароль пользователя успешно изменен');
    }
}
