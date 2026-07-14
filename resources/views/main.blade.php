@extends('layouts.app')

@section('title', 'Главная')
@section('content')
    <div class="container-fluid px-2 px-sm-3 py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold mb-3">Добро пожаловать в Laravel Shop</h1>
                    <p class="lead text-muted">Ваш надежный интернет-магазин</p>
                </div>

                <div class="row g-4">
                    <!-- Здесь будет контент -->
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-4 text-center">
                                <h3 class="mb-3">Начните покупки прямо сейчас!</h3>
                                <p class="text-muted mb-4">Выберите категорию товаров или воспользуйтесь поиском</p>
                                <div class="d-flex gap-3 justify-content-center flex-wrap">
                                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Каталог товаров</a>
                                    <a href="#" class="btn btn-outline-primary btn-lg">О магазине</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

