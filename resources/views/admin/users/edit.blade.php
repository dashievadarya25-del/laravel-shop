@extends('layouts.app')

@section('title', 'Редактирование пользователя')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Редактирование пользователя: {{ $user->first_name }} {{ $user->last_name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            ← Назад к списку
        </a>
    </div>

    {{-- Статусы успешных операций --}}
    @if(session('status'))
        <div class="alert alert-success mb-3">
            {{ session('status') }}
        </div>
    @endif

    {{-- Общий вывод ошибок валидации --}}
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Карточка 1: Основные данные --}}
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title h6 mb-0">Профиль пользователя</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="first_name" class="form-label mb-1">Имя <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="first_name"
                                       id="first_name"
                                       value="{{ old('first_name', $user->first_name) }}"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="last_name" class="form-label mb-1">Фамилия <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="last_name"
                                       id="last_name"
                                       value="{{ old('last_name', $user->last_name) }}"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label mb-1">Email <span class="text-danger">*</span></label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       value="{{ old('email', $user->email) }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label mb-1">Телефон</label>
                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="+7 (999) 123-45-67">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="role_id" class="form-label mb-1">Роль <span class="text-danger">*</span></label>
                                <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                                            {{ $role->name }} ({{ $role->slug }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label mb-1">Статус <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" @selected(old('status', $user->status) === 'active')>Активен (доступ разрешен)</option>
                                    <option value="blocked" @selected(old('status', $user->status) === 'blocked')>Заблокирован (доступ запрещен)</option>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">Сохранить изменения</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Карточка 2: Сброс пароля --}}
        <div class="col-12 col-lg-4">
            <div class="card h-100 border-warning">
                <div class="card-header bg-warning bg-opacity-10 py-3">
                    <h5 class="card-title h6 mb-0 text-warning-emphasis">Безопасность и пароль</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small">
                            Вы можете принудительно установить новый пароль для данного пользователя. Нажмите «Обновить пароль» после заполнения полей.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.users.password', $user) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="password" class="form-label mb-1">Новый пароль</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control @error('password', 'resetPassword') is-invalid @enderror"
                                   placeholder="Минимум 8 символов"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label mb-1">Подтверждение пароля</label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-dark">Обновить пароль</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
