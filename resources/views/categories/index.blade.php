@extends('layouts.app')

@section('title', 'Каталог')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Каталог</h1>
        </div>
        <p class="text-muted">Выберите категорию товаров.</p>

        @if($categories->isEmpty())
            <p class="mb-0">Категории пока не добавлены.</p>
        @else
            <div class="list-group">
                @foreach($categories as $category)
                    <a href="{{ route('categories.show', $category) }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span>{{ $category->name }}</span>
                        <span class="badge text-bg-secondary rounded-pill">{{ $category->products_count }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection

