@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Управление товарами</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Добавить товар</a>
    </div>

    {{-- Вывод уведомлений об успехе --}}
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Таблица товаров --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead>
                <tr>
                    <th>Изображение</th>
                    <th>SKU</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th>Склад</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="" style="max-height: 50px;" class="img-thumbnail">
                            @else
                                <span class="text-muted">Нет фото</span>
                            @endif
                        </td>
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name ?? 'Без категории' }}</td>
                        <td>{{ number_format($product->price, 2) }} руб.</td>
                        <td>{{ $product->stock }} шт.</td>
                        <td>
                                <span class="badge bg-{{ $product->status === \App\Models\Product::STATUS_ACTIVE ? 'success' : 'secondary' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">Редактировать</a>

                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Удалить этот товар?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Товары не найдены</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{-- Пагинация (ссылки на другие страницы) --}}
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
