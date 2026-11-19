<div class="form-grid">
    <div class="field">
        <label for="name">Plant name</label>
        <input id="name" name="name" value="{{ old('name', $plant->name ?? '') }}" maxlength="255" required>
        @error('name')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="field">
        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">Choose a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}" @selected(old('category', $plant->category ?? '') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        @error('category')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="field">
        <label for="unit_price">Unit price</label>
        <input id="unit_price" name="unit_price" type="number" step="0.01" min="0" value="{{ old('unit_price', $plant->unit_price ?? '') }}" required>
        @error('unit_price')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="field">
        <label for="stock_status">Stock status</label>
        <select id="stock_status" name="stock_status" required>
            <option value="in_stock" @selected(old('stock_status', $plant->stock_status ?? 'in_stock') === 'in_stock')>In stock</option>
            <option value="out_of_stock" @selected(old('stock_status', $plant->stock_status ?? '') === 'out_of_stock')>Out of stock</option>
        </select>
        @error('stock_status')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="field">
        <label for="description">Description</label>
        <textarea id="description" name="description" maxlength="5000" required>{{ old('description', $plant->description ?? '') }}</textarea>
        @error('description')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="field">
        <label for="image">Plant photo (JPEG, PNG, or WebP; up to 4 MB)</label>
        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        @error('image')<div class="field-error">{{ $message }}</div>@enderror
        @if (isset($plant) && $plant->image_path)
            <p class="muted">Current photo:</p>
            <img src="{{ asset('storage/'.$plant->image_path) }}" alt="{{ $plant->name }}" style="width:120px;height:120px;object-fit:cover;border-radius:10px">
        @endif
    </div>
    <div class="actions">
        <button class="button" type="submit">{{ $submitLabel }}</button>
        <a class="button secondary" href="{{ route('plants.index') }}">Cancel</a>
    </div>
</div>
