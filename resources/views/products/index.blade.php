@php($perPage = $dto->per_page ?? 10)
@php($q = $dto->q ?? '')
@php($minPrice = $dto->min_price ?? '')
@php($maxPrice = $dto->max_price ?? '')
@php($inStock = $dto->in_stock ?? false)
@php($sort = $dto->sort ?? 'new')
@php($maxProductPricePlaceholder = $maxPrice ? ('до ' . $maxPrice) : 'максимальная цена товара')

@extends('layouts.app')

@section('title', 'Каталог товаров')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-3">Каталог товаров</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="GET" action="{{ route('products.index') }}" class="card card-body mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-4">
                    <label for="q" class="form-label mb-1">Поиск</label>
                    <input type="text"
                           name="q"
                           id="q"
                           value="{{ $q }}"
                           class="form-control"
                           placeholder="Название или артикул">
                </div>

                <div class="col-6 col-lg-2">
                    <label for="min_price" class="form-label mb-1">Цена от</label>
                    <input type="number"
                           name="min_price"
                           id="min_price"
                           value="{{ $minPrice }}"
                           placeholder="от 100"
                           min="0"
                           step="100"
                           class="form-control">
                </div>

                <div class="col-6 col-lg-2">
                    <label for="max_price" class="form-label mb-1">до</label>
                    <input type="number"
                           name="max_price"
                           id="max_price"
                           value="{{ $maxPrice }}"
                           placeholder="{{ $maxProductPricePlaceholder }}"
                           min="0"
                           step="100"
                           class="form-control">
                </div>

                <div class="col-6 col-lg-2">
                    <label for="sort" class="form-label mb-1">Сортировка</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="new" @selected($sort === 'new')>Сначала новые</option>
                        <option value="price_asc" @selected($sort === 'price_asc')>Цена: по возрастанию</option>
                        <option value="price_desc" @selected($sort === 'price_desc')>Цена: по убыванию</option>
                        <option value="name_asc" @selected($sort === 'name_asc')>Название: А → Я</option>
                        <option value="name_desc" @selected($sort === 'name_desc')>Название: Я → А</option>
                        <option value="stock_desc" @selected($sort === 'stock_desc')>Наличие: больше → меньше</option>
                        <option value="stock_asc" @selected($sort === 'stock_asc')>Наличие: меньше → больше</option>
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <label for="per_page" class="form-label mb-1">На странице</label>
                    <select name="per_page" id="per_page" class="form-select">
                        @foreach([10, 25, 50, 100] as $option)
                            <option value="{{ $option }}" @selected($perPage == $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-lg-4 mt-2">
                    <div class="form-check">
                        <input type="checkbox"
                               name="in_stock"
                               id="in_stock"
                               value="1"
                               class="form-check-input"
                            @checked($inStock)>
                        <label for="in_stock" class="form-check-label">Только в наличии</label>
                    </div>
                </div>

                <div class="col-12 col-lg-8 mt-2 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Применить</button>
                    <a href="{{ route('products.index', ['per_page' => $perPage]) }}"
                       class="btn btn-outline-secondary">
                        Сбросить
                    </a>
                </div>
            </div>
        </form>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
            @forelse($products as $product)
                <div class="col">
                    <div class="card h-100">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 class="card-img-top"
                                 alt="{{ $product->name }}">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                 style="height: 180px;">
                                <span class="text-muted">Нет изображения</span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="fw-semibold mb-3">{{ number_format($product->price, 0, ',', ' ') }} ₽</p>

                            @php($detailsUrl = Route::has('products.show') ? route('products.show', $product) : '#')
                            <a href="{{ $detailsUrl }}" class="btn btn-outline-primary mt-auto">
                                Подробнее
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p>По заданным условиям товары не найдены.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection


