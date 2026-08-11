@extends('layouts.app')

@section('title', 'Добавление нового товара')

@section('content')
    <div class="container py-4">
        {{-- Навигация назад к списку --}}
        <div class="mb-3">
            <a href="{{ route('products.index') }}" class="text-decoration-none">← Вернуться в каталог</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h1 class="h4 mb-0">➕ Добавление нового товара</h1>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Подключаем внутренние поля из _form.blade.php --}}
                    @include('admin.products._form')

                    <div class="mt-4 border-top pt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4">Создать товар</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
