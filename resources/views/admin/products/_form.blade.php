<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main fields --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('name') border-red-400 @enderror">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price (₹)</label>
                    <input type="number" name="price" step="0.01" min="0"
                           value="{{ old('price', $product->price ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('price') border-red-400 @enderror">
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Bulk Price (₹)
                        <span class="text-gray-400 font-normal">optional</span>
                    </label>
                    <input type="number" name="bulk_price" step="0.01" min="0"
                           value="{{ old('bulk_price', $product->bulk_price ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit ?? 'kg') }}"
                           placeholder="kg, bunch, piece..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input type="number" name="stock" min="0"
                           value="{{ old('stock', $product->stock ?? 0) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Order Quantity</label>
                    <input type="number" name="min_order_qty" min="1"
                        value="{{ old('min_order_qty', $product->min_order_qty ?? 1) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('min_order_qty') border-red-400 @enderror">
                    @error('min_order_qty') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

        </div>
    </div>

    {{-- Sidebar: image, category, status --}}
    <div class="space-y-4">

        {{-- Image upload --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <label class="block text-sm font-medium text-gray-700 mb-3">Product Image</label>

            {{-- Show existing image on edit --}}
            @if(isset($product) && $product->image)
                <img src="{{ Storage::url($product->image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-40 object-contain rounded-lg mb-3 border border-gray-200">
                <p class="text-xs text-gray-400 mb-2">Upload a new image to replace this one.</p>
            @else
                <div class="w-full h-40 bg-green-50 rounded-lg flex items-center justify-center text-4xl mb-3 border border-dashed border-gray-300">
                    🥬
                </div>
            @endif

            <input type="file" name="image" accept="image/*"
                   class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-400 mt-1">JPG, PNG or WebP. Max 2MB.</p>
        </div>

        {{-- Category --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select name="category_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">— Uncategorised —</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Status --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 rounded text-green-600 focus:ring-green-500">
                <div>
                    <p class="text-sm font-medium text-gray-700">Active</p>
                    <p class="text-xs text-gray-400">Visible to customers in the shop</p>
                </div>
            </label>
        </div>

    </div>
</div>