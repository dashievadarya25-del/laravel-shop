@extends('layouts.app')

@section('title', 'Редактирование товара: ' . $product->name)

@section('content')
    <div class="container py-4">
        {{-- Навигация назад в каталог --}}
        <div class="mb-3">
            <a href="{{ route('products.index') }}" class="text-decoration-none">← Вернуться в каталог</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h1 class="h4 mb-0">✏️ Редактирование товара: <span class="text-primary">{{ $product->name }}</span></h1>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Подключаем поля формы, переменная $product прокидывается автоматически --}}
                    @include('admin.products._form')

                    <div class="mt-4 border-top pt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4">Обновить товар</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
