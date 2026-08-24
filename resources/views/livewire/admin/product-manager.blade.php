<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Products</h2>
        <div>
            <button wire:click="createProduct" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Add Product</button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
            <div class="grid gap-4">
                @foreach($products as $product)
                    <div class="p-4 border rounded flex items-center">
                        <div class="w-20 h-20 bg-gray-100 mr-4 flex items-center justify-center">
                            @if($product->productImages->first())
                                <img src="{{ asset('storage/'.$product->productImages->first()->image_link) }}" alt="" class="w-full h-full object-cover" />
                            @else
                                <span class="text-gray-400">no image</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold">{{ $product->name }}</div>
                            <div class="text-sm text-gray-600">{{ $product->category_name }}</div>
                            <div class="text-sm">৳{{ $product->price_per_piece }} — <span class="text-xs {{ $product->is_available ? 'text-green-600' : 'text-red-600' }}">{{ $product->is_available ? 'Available' : 'Unavailable' }}</span></div>
                        </div>
                        <div class="space-x-2">
                            <button wire:click="editProduct({{ $product->id }})" class="px-3 py-1 bg-yellow-400 text-white rounded">Edit</button>
                            <button wire:click="toggleAvailability({{ $product->id }})" class="px-3 py-1 bg-indigo-600 text-white rounded">Toggle</button>
                            <button wire:click="deleteProduct({{ $product->id }})" class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-4 border rounded">
            <h3 class="font-semibold mb-2">{{ $editing ? 'Edit' : 'Create' }} Product</h3>

            <div class="space-y-2">
                <div>
                    <label class="block text-sm">Category</label>
                    <input wire:model.defer="category_name" class="w-full border rounded px-2 py-1" />
                    @error('category_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm">Name</label>
                    <input wire:model.defer="name" class="w-full border rounded px-2 py-1" />
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm">Description</label>
                    <textarea wire:model.defer="description" class="w-full border rounded px-2 py-1"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm">Price (per piece)</label>
                        <input wire:model.defer="price_per_piece" class="w-full border rounded px-2 py-1" />
                        @error('price_per_piece') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm">Available quantity</label>
                        <input wire:model.defer="available_quantity" class="w-full border rounded px-2 py-1" />
                        @error('available_quantity') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="is_available" class="mr-2" /> Available
                    </label>
                </div>

                <div>
                    <label class="block text-sm">Images (multiple)</label>
                    <input type="file" wire:model="images" multiple class="w-full" />
                    @error('images.*') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

                    <div class="mt-2 grid grid-cols-3 gap-2">
                        @if(!empty($images))
                            @foreach($images as $img)
                                <div class="w-full h-20 bg-gray-100 overflow-hidden">
                                    <img src="{{ $img->temporaryUrl() }}" class="w-full h-full object-cover" alt="preview" />
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <button wire:click="saveProduct" class="bg-red-600 text-white px-4 py-2 rounded">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
