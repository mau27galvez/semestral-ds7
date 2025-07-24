<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Validation\Rule;

class UpdateCategoryForm extends Form
{
    public int $id = 0;
    public string $name = '';
    public string $slug = '';

    public function rules()
    {
        return [
            'id' => 'required|exists:categories,id',
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($this->id)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($this->id)],
        ];
    }
}
