@if(empty($items))
    <div class="alert alert-info mb-0">
        Корзина пуста. <a href="{{ route('categories.index') }}">Перейти в каталог</a>
    </div>
@else

    <div class="d-flex justify-content-end mb-3">
        <form method="POST" action="{{ route('cart.clear') }}" data-ajax-cart="1">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Очистить всю корзину?')">
                ✕ Очистить корзину
            </button>
        </form>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="table-responsive bg-white p-3 rounded shadow-sm">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Цена</th>
                        <th class="text-center" style="width: 220px;">Количество</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        @php
                            $product = $item['product'] ?? $item->product ?? $item;
                        @endphp
                        <tr>
                            <td>{{ $product->name ?? 'Товар' }}</td>
                            <td>{{ number_format($product->price ?? 0, 0, '.', ' ') }} ₽</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <form method="POST"
                                          action="{{ $item['quantity'] <= 1 ? route('cart.items.destroy', $product) : route('cart.items.update', $product) }}"
                                          data-ajax-cart="1"
                                          data-cart-action="update">
                                        @csrf
                                        @method($item['quantity'] <= 1 ? 'DELETE' : 'PATCH')

                                        @if($item['quantity'] > 1)
                                            <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                        @endif

                                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                                            −
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('cart.items.update', $product) }}"
                                          data-ajax-cart="1"
                                          data-cart-action="set">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number"
                                               name="quantity"
                                               value="{{ $item['quantity'] }}"
                                               min="1"
                                               max="{{ $product->stock ?? 100 }}"
                                               step="1"
                                               class="form-control form-control-sm text-center"
                                               style="width: 70px">
                                    </form>

                                    <form method="POST"
                                          action="{{ route('cart.items.update', $product) }}"
                                          data-ajax-cart="1"
                                          data-cart-action="update">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" @disabled($item['quantity'] >= ($product->stock ?? 100))>
                                            +
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-3 shadow-sm border-0">
                <div class="mb-3 border-bottom pb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Всего товаров:</span>
                        <strong>{{ $totalQuantity }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Итого к оплате:</span>
                        <strong class="fs-4 text-primary">{{ number_format($totalPrice, 0, '.', ' ') }} ₽</strong>
                    </div>
                </div>

                <form method="POST" action="{{ route('orders.store') }}">
                    @csrf

                    <div class="mb-3 pb-3 border-bottom">
                        <h3 class="h6 mb-2">Адрес доставки</h3>
                        @if($defaultAddress)
                            <div class="p-2 bg-light rounded border small text-muted">
                                📍 <strong>Основной адрес:</strong> <br>
                                {{ $defaultAddress->city }}, {{ $defaultAddress->street }}, д. {{ $defaultAddress->house }}
                                <input type="hidden" name="address_id" value="{{ $defaultAddress->id }}">
                            </div>
                        @else
                            <div class="alert alert-warning p-2 small mb-0">
                                ⚠️ Адрес не установлен в профиле.
                                <a href="{{ route('profile.form') }}" class="alert-link">Добавить адрес</a>
                            </div>
                        @endif
                    </div>

                    <h3 class="h6 mb-2">Способ оплаты</h3>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash"
                            {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_cash">Наличными при получении</label>
                    </div>

                    <div class="form-check mt-1">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="card"
                            {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_card">Картой при получении</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3 py-2 fw-bold">
                        Оформить заказ
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif
