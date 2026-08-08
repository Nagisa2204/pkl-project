<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class AdminCategories extends Component
{
    public ?int $editingId = null;
    public ?int $parent_id = null;
    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public bool $is_active = true;

    public function mount(): void
    {
        $this->authorize('admin');
    }

    public function updatedName(): void
    {
        if (! $this->editingId) $this->slug = Str::slug($this->name);
    }

    public function edit(int $id): void
    {
        $this->authorize('admin');
        $category = Category::findOrFail($id);
        $this->fill($category->only(['parent_id', 'name', 'slug', 'description', 'is_active']));
        $this->editingId = $id;
    }

    public function save(): void
    {
        $this->authorize('admin');
        $data = $this->validate([
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn(array_filter([$this->editingId]))],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('categories')->ignore($this->editingId)],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);
        Category::updateOrCreate(['id' => $this->editingId], $data);
        $this->resetForm();
        session()->flash('success', 'Kategori berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        $this->authorize('admin');
        $category = Category::withCount(['products', 'children'])->findOrFail($id);
        abort_if($category->is_protected || $category->products_count || $category->children_count, 422, 'Kategori masih digunakan.');
        $category->delete();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'parent_id', 'name', 'slug', 'description']);
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.admin-categories', [
            'categories' => Category::with('parent')->withCount('products')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }
}
