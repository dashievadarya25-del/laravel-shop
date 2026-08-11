<div class="mb-3">
    <label for="name" class="form-label">Название товара</label>
    <input type="text"
           name="name"
           id="name"
           value="{{ old('name', $product->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror"
           required>
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    {{-- Поле: Цена --}}
    <div class="col-md-4 mb-3">
        <label for="price" class="form-label">Цена</label>
        <input type="number"
               step="0.01"
               name="price"
               id="price"
               value="{{ old('price', $product->price ?? '') }}"
               class="form-control @error('price') is-invalid @enderror"
               required>
        @error('price')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Поле: Количество на складе --}}
    <div class="col-md-4 mb-3">
        <label for="stock" class="form-label">Количество (Склад)</label>
        <input type="number"
               name="stock"
               id="stock"
               value="{{ old('stock', $product->stock ?? '') }}"
               class="form-control @error('stock') is-invalid @enderror"
               required>
        @error('stock')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Поле: SKU (Артикул) --}}
    <div class="col-md-4 mb-3">
        <label for="sku" class="form-label">SKU (Артикул)</label>
        <input type="text"
               name="sku"
               id="sku"
               value="{{ old('sku', $product->sku ?? '') }}"
               class="form-control @error('sku') is-invalid @enderror"
               required>
        @error('sku')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    {{-- Поле: Категория --}}
    <div class="col-md-6 mb-3">
        <label for="category_id" class="form-label">Категория</label>
        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">-- Выберите категорию --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Поле: Статус (Используем константы из модели Product) --}}
    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">Статус</label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(\App\Models\Product::STATUSES as $status)
                <option value="{{ $status }}"
                    {{ old('status', $product->status ?? \App\Models\Product::STATUS_ACTIVE) === $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Поле: Изображение товара --}}
<div class="mb-3">
    <label for="image" class="form-label">Изображение товара</label>
    <input type="file"
           name="image"
           id="image"
           class="form-control @error('image') is-invalid @enderror">
    @error('image')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    {{-- Показываем текущую картинку, если мы редактируем товар --}}
    @if(isset($product) && $product->image)
        <div class="mt-2">
            <p class="text-muted small mb-1">Текущее изображение:</p>
            <img src="{{ asset('storage/' . $product->image) }}" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
        </div>
    @endif
</div>
