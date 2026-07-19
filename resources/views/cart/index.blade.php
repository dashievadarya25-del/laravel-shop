@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-3">Корзина</h1>

        <div id="cart-content">
            @include('cart._content', ['items' => $items, 'totalQuantity' => $totalQuantity, 'totalPrice' => $totalPrice])
        </div>
    </div>
@endsection

