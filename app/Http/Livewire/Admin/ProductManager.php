<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductManager extends Component
{
    use WithFileUploads;

    public $products;

    // product fields
    public $productId;
    public $category_name;
    public $name;
    public $description;
    public $price_per_piece;
    public $available_quantity;
    public $is_available = true;

    // uploaded images (array)
    public $images = [];

    public $editing = false;

    protected $rules = [
        'category_name' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price_per_piece' => 'required|numeric|min:0',
        'available_quantity' => 'required|integer|min:0',
        'is_available' => 'boolean',
        'images.*' => 'image|max:5120', // 5MB
    ];

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $this->products = Product::with('productImages')->orderBy('created_at', 'desc')->get();
    }

    public function createProduct()
    {
        $this->resetForm();
        $this->editing = false;
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->category_name = $product->category_name;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price_per_piece = $product->price_per_piece;
        $this->available_quantity = $product->available_quantity;
        $this->is_available = (bool) $product->is_available;
        $this->editing = true;
    }

    public function saveProduct()
    {
        $this->validate();

        if ($this->editing && $this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->update([
                'category_name' => $this->category_name,
                'name' => $this->name,
                'description' => $this->description,
                'price_per_piece' => $this->price_per_piece,
                'available_quantity' => $this->available_quantity,
                'is_available' => $this->is_available,
            ]);
        } else {
            $product = Product::create([
                'category_name' => $this->category_name,
                'name' => $this->name,
                'description' => $this->description,
                'price_per_piece' => $this->price_per_piece,
                'available_quantity' => $this->available_quantity,
                'is_available' => $this->is_available,
            ]);
        }

        // Handle images
        if (! empty($this->images)) {
            foreach ($this->images as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_link' => $path,
                ]);
            }
        }

        $this->resetForm();
        $this->loadProducts();
        session()->flash('message', 'Product saved.');
    }

    public function toggleAvailability($id)
    {
        $product = Product::findOrFail($id);
        $product->is_available = ! (bool) $product->is_available;
        $product->save();
        $this->loadProducts();
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        // remove images from disk
        foreach ($product->productImages as $img) {
            if ($img->image_link && \Storage::disk('public')->exists($img->image_link)) {
                \Storage::disk('public')->delete($img->image_link);
            }
            $img->delete();
        }
        $product->delete();
        $this->loadProducts();
    }

    protected function resetForm()
    {
        $this->productId = null;
        $this->category_name = '';
        $this->name = '';
        $this->description = '';
        $this->price_per_piece = '';
        $this->available_quantity = '';
        $this->is_available = true;
        $this->images = [];
        $this->editing = false;
    }

    public function render()
    {
        return view('livewire.admin.product-manager');
    }
}
