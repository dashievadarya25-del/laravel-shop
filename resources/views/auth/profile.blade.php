@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
    <div class="container-fluid px-2 px-sm-3 py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-11 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <h2 class="text-center mb-3 mb-md-4 h3 h2-md">Профиль пользователя</h2>

                        @if(session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update', $user) }}">
                            @csrf
                            @method('PATCH')

                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="first_name" class="form-label small">Имя</label>
                                        <input type="text"
                                               name="first_name"
                                               id="first_name"
                                               class="form-control @error('first_name') is-invalid @enderror"
                                               value="{{ old('first_name', $user->first_name) }}">
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
                                               value="{{ old('last_name', $user->last_name) }}">
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
                                       value="{{ old('email', $user->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 mb-md-4">
                                <label for="phone" class="form-label small">Телефон</label>
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="+7 (___) ___-__-__">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2">
                                    Сохранить изменения
                                </button>
                            </div>
                        </form>

                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-semibold text-gray-900">Адреса доставки</h4>
                                <button id="toggleAddressFormBtn"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                    + Добавить новый
                                </button>
                            </div>

                            @if($addresses->isEmpty())
                                <p class="text-sm text-gray-500 mb-4">У вас пока нет сохраненных адресов.</p>
                            @else
                                <div class="space-y-2 mb-4">
                                    @foreach($addresses as $address)
                                        <div class="flex justify-between items-center p-3 rounded-lg border text-sm transition-all {{ $address->is_default ? 'bg-blue-50/50 border-blue-500 ring-1 ring-blue-500' : 'bg-white border-gray-200' }}">
                                            <div>
                                                <p class="font-medium text-gray-800">
                                                    г. {{ $address->city }}, ул. {{ $address->street }}, д. {{ $address->house }}
                                                </p>
                                                @if($address->is_default)
                                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                Основной адрес
                            </span>
                                                @endif
                                            </div>

                                            @if(!$address->is_default)
                                                <form action="{{ route('profile.addresses.default', $address) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                                        Выбрать
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div id="addAddressForm" class="{{ $errors->has('city') || $errors->has('street') || $errors->has('house') ? '' : 'hidden' }} mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg transition-all">
                                <h5 class="text-sm font-semibold text-gray-800 mb-3">Новый адрес</h5>
                                <form action="{{ route('profile.addresses.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label for="address_city" class="block text-xs font-medium text-gray-700 mb-1">Город</label>
                                        <input type="text" name="city" id="address_city" class="w-full px-3 py-2 text-sm bg-white border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('city') border-red-500 @else border-gray-300 @enderror" value="{{ old('city') }}" required>
                                        @error('city')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="col-span-2">
                                            <label for="address_street" class="block text-xs font-medium text-gray-700 mb-1">Улица</label>
                                            <input type="text" name="street" id="address_street" class="w-full px-3 py-2 text-sm bg-white border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('street') border-red-500 @else border-gray-300 @enderror" value="{{ old('street') }}" required>
                                            @error('street')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label for="address_house" class="block text-xs font-medium text-gray-700 mb-1">Дом / Кв</label>
                                            <input type="text" name="house" id="address_house" class="w-full px-3 py-2 text-sm bg-white border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('house') border-red-500 @else border-gray-300 @enderror" value="{{ old('house') }}" required>
                                            @error('house')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    <div class="pt-1">
                                        <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors shadow-sm">
                                            Сохранить адрес
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid">
                            <a href="{{ route('password.form') }}" class="btn btn-outline-secondary py-2">
                                Изменить пароль
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput && phoneInput.value) {
                phoneInput.value = phoneInput.value.replace(/\+/g, '');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleAddressFormBtn');
            const formBlock = document.getElementById('addAddressForm');

            if (toggleBtn && formBlock) {
                toggleBtn.addEventListener('click', function() {
                    formBlock.classList.toggle('hidden');
                });
            }
        });
    </script>
@endpush
