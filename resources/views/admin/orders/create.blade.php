@extends('layouts.app')

@section('title', 'Создание нового заказа')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Новый заказ (Админка)</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            ← Назад к списку
        </a>
    </div>

    {{-- Вывод ошибок валидации (например, если на складе не хватило товара) --}}
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.orders.store') }}" class="row g-4">
        @csrf

        {{-- Левая колонка: Основные параметры заказа --}}
        <div class="col-12 col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title h6 mb-0">Данные покупателя и доставки</h5>
                </div>
                <div class="card-body small">
                    {{-- Выбор пользователя --}}
                    <div class="mb-3">
                        <label for="user_id" class="form-label mb-1">Покупатель <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="" disabled selected>Выберите клиента</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                    {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Способ оплаты --}}
                    <div class="mb-3">
                        <label for="payment_method" class="form-label mb-1">Способ оплаты <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            @foreach($paymentLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Начальный статус заказа --}}
                    <div class="mb-3">
                        <label for="status" class="form-label mb-1">Начальный статус <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            @foreach($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', 'pending') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">При выборе статуса «Оплачен» система автоматически спишет остатки со склада.</small>
                    </div>

                    {{-- Адрес доставки --}}
                    <div class="mb-0">
                        <label for="shipping_address" class="form-label mb-1">Адрес доставки <span class="text-danger">*</span></label>
                        <textarea name="shipping_address"
                                  id="shipping_address"
                                  rows="3"
                                  class="form-control"
                                  placeholder="Город, улица, дом, квартира..."
                                  required>{{ old('shipping_address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg">📦 Создать заказ</button>
            </div>
        </div>

        {{-- Правая колонка: Динамическое добавление товаров --}}
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title h6 mb-0">Позиции заказа</h5>
                    <button type="button" id="add-item-btn" class="btn btn-sm btn-primary">
                        ➕ Добавить товар
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small" id="items-table">
                            <thead class="table-light">
                            <tr>
                                <th scope="col">Товар</th>
                                <th scope="col" style="width: 140px;">Историческая цена (₽)</th>
                                <th scope="col" style="width: 110px;">Кол-во</th>
                                <th scope="col" style="width: 50px;"></th>
                            </tr>
                            </thead>
                            <tbody id="items-container">
                            {{-- JS будет вставлять строки товаров --}}
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][product_id]" class="form-select product-select" required onchange="updatePricePlaceholder(this)">
                                        <option value="" disabled selected>Выберите товар из каталога</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                {{ $product->name }} (В наличии: {{ $product->stock }} шт.) — {{ number_format((float)$product->price, 0, ',', ' ') }} ₽
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][price]" class="form-control price-input" placeholder="0" min="0" step="0.01" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control" value="1" min="1" required>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">🗑️</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- JavaScript для динамического добавления строк --}}
    <script>
        let rowIndex = 1;

        // Кнопка добавления новой строки
        document.getElementById('add-item-btn').addEventListener('click', function() {
            const container = document.getElementById('items-container');

            // Создаем новую строку таблицы
            const row = document.createElement('tr');
            row.className = 'item-row';

            row.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select product-select" required onchange="updatePricePlaceholder(this)">
                    <option value="" disabled selected>Выберите товар из каталога</option>
                    @foreach($products as $product)
            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} (В наличии: {{ $product->stock }} шт.) — {{ number_format((float)$product->price, 0, ',', ' ') }} ₽
                        </option>
                    @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[${rowIndex}][price]" class="form-control price-input" placeholder="0" min="0" step="0.01" required>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control" value="1" min="1" required>
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">🗑️</button>
            </td>
        `;

            container.appendChild(row);
            rowIndex++;
        });

        // Функция автоподстановки текущей цены товара в поле "Историческая цена"
        function updatePricePlaceholder(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const price = selectedOption.getAttribute('data-price');

            // Находим поле цены в этой же строке таблицы
            const row = selectElement.closest('.item-row');
            const priceInput = row.querySelector('.price-input');

            if (priceInput && price) {
                priceInput.value = parseFloat(price);
            }
        }

        // Функция удаления строки (с защитой от удаления последней оставшейся строки)
        function removeRow(buttonElement) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                buttonElement.closest('tr').remove();
            } else {
                alert('В заказе должен быть как минимум один товар!');
            }
        }
    </script>
@endsection
