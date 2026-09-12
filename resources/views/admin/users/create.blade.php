@extends('layouts.app')

@section('title', 'Добавление пользователя')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Новый пользователь</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            ← Назад к списку
        </a>
    </div>

    {{-- Вывод ошибок валидации --}}
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="row g-3">
                    {{-- Имя --}}
                    <div class="col-12 col-md-6">
                        <label for="first_name" class="form-label mb-1">Имя <span class="text-danger">*</span></label>
                        <input type="text"
                               name="first_name"
                               id="first_name"
                               value="{{ old('first_name') }}"
                               class="form-control @error('first_name') is-invalid @enderror"
                               required>
                    </div>

                    {{-- Фамилия --}}
                    <div class="col-12 col-md-6">
                        <label for="last_name" class="form-label mb-1">Фамилия <span class="text-danger">*</span></label>
                        <input type="text"
                               name="last_name"
                               id="last_name"
                               value="{{ old('last_name') }}"
                               class="form-control @error('last_name') is-invalid @enderror"
                               required>
                    </div>

                    {{-- Email --}}
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label mb-1">Email <span class="text-danger">*</span></label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="example@mail.com"
                               required>
                    </div>

                    {{-- Телефон --}}
                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label mb-1">Телефон</label>
                        <input type="text"
                               name="phone"
                               id="phone"
                               value="{{ old('phone') }}"
                               class="form-control @error('phone') is-invalid @enderror"
                               placeholder="+7 (999) 123-45-67">
                    </div>

                    {{-- Роль пользователя --}}
                    <div class="col-12 col-md-6">
                        <label for="role_id" class="form-label mb-1">Роль <span class="text-danger">*</span></label>
                        <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>Выберите роль</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                    {{ $role->name }} ({{ $role->slug }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Статус --}}
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label mb-1">Статус <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" @selected(old('status', 'active') === 'active')>Активен (доступ разрешен)</option>
                            <option value="blocked" @selected(old('status') === 'blocked')>Заблокирован (доступ запрещен)</option>
                        </select>
                    </div>

                    <div class="col-12 hr border-top my-3"></div>

                    {{-- Пароль --}}
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label mb-1">Пароль <span class="text-danger">*</span></label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Минимум 8 символов"
                               required>
                    </div>

                    {{-- Подтверждение пароля --}}
                    <div class="col-12 col-md-6">
                        <label for="password_confirmation" class="form-label mb-1">Подтверждение пароля <span class="text-danger">*</span></label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control"
                               required>
                    </div>

                    {{-- Кнопки действий --}}
                    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-success px-4">Создать пользователя</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
