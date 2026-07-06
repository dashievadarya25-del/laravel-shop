@php($perPage = $dto->per_page ?? 10)

@extends('layouts.app')

@section('title', 'Каталог товаров')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-2">Каталог товаров</h1>

            <form method="GET" action="{{ route('products.index') }}" class="d-flex align-items-center gap-2">
                <label for="per_page" class="mb-0">Товаров на странице:</label>
                <select name="per_page"
                        id="per_page"
                        class="form-select"
                        style="width: 120px"
                        onchange="this.form.submit()">
                    @foreach([10, 25, 50, 100] as $option)
                        <option value="{{ $option }}" @selected($perPage == $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </form>
        </div>

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
                <p>Товары ещё не добавлены.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->appends(['per_page' => $perPage])->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection

