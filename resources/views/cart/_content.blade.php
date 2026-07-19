@if(empty($items))
    <div class="alert alert-info mb-0">
        Корзина пуста. <a href="{{ route('categories.index') }}">Перейти в каталог</a>
    </div>
@else
    <!-- КНОПКА ОЧИСТКИ КОРЗИНЫ -->
    <div class="d-flex justify-content-end mb-3">
        <form method="POST" action="{{ route('cart.clear') }}" data-ajax-cart="1">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Очистить всю корзину?')">
                ✕ Очистить корзину
            </button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Товар</th>
                <th>Цена</th>
                <th class="text-center" style="width: 220px;">Количество</th>
                <th class="text-end" style="width: 50px;"></th> <!-- Колонка для удаления -->
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
                            <!-- Кнопка "Минус" -->
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

                            <!-- Поле ввода количества -->
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

                            <!-- Кнопка "Плюс" -->
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
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Блок с общей суммой -->
    <div class="d-flex justify-content-end align-items-center mt-3 gap-3">
        <div>
            Общее количество: <strong class="fs-5">{{ $totalQuantity }}</strong>
        </div>
        <div>
            Итого к оплате: <strong class="fs-4 text-primary">{{ number_format($totalPrice, 0, '.', ' ') }} ₽</strong>
        </div>
    </div>
@endif
