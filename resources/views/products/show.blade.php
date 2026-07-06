@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-md-5">
                @if($product->image)
                    <div class="border rounded overflow-hidden">
                        <img src="{{ asset('storage/' . $product->image) }}"
                             class="w-100"
                             alt="{{ $product->name }}">
                    </div>
                @else
                    <div class="border rounded d-flex align-items-center justify-content-center bg-light"
                         style="height: 320px;">
                        <span class="text-muted">Нет изображения</span>
                    </div>
                @endif
            </div>

            <div class="col-md-7">
                <h1 class="h3 mb-3">{{ $product->name }}</h1>
                <p class="fs-4 fw-semibold mb-3">{{ number_format($product->price, 0, ',', ' ') }} ₽</p>

                <div class="mb-3 text-muted small">
                    <div>Артикул: {{ $product->sku }}</div>
                    <div>В наличии: {{ $product->stock }}</div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">Описание</h6>
                    <p class="mb-0">{{ $product->description ?? 'Описание пока не добавлено.' }}</p>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" disabled>Добавить в корзину</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Назад в каталог</a>
                </div>
            </div>
        </div>
    </div>
@endsection

