@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
    <div class="container-fluid px-2 px-sm-3 py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-11 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <h2 class="text-center mb-3 mb-md-4 h3 h2-md">Регистрация</h2>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="first_name" class="form-label small">Имя</label>
                                        <input type="text"
                                               name="first_name"
                                               id="first_name"
                                               class="form-control @error('first_name') is-invalid @enderror"
                                               value="{{ old('first_name') }}"
                                               required>
                                        @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="last_name" class="form-label small">Фамилия</label>
                                        <input type="text"
                                               name="last_name"
                                               id="last_name"
                                               class="form-control @error('last_name') is-invalid @enderror"
                                               value="{{ old('last_name') }}"
                                               required>
                                        @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2 mb-md-3">
                                <label for="email" class="form-label small">Email</label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2 mb-md-3">
                                <label for="password" class="form-label small">Пароль</label>
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 mb-md-4">
                                <label for="password_confirmation" class="form-label small">Подтверждение пароля</label>
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2">
                                    Зарегистрироваться
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


