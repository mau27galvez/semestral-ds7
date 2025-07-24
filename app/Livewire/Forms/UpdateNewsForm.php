<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Validation\Rule;

class UpdateNewsForm extends Form
{
    public int $id = 0;
    public string $title = '';
    public string $paragraph = '';
    public string $author = '';
    public bool $is_published = false;
    public int $category_id = 0;
    public array $existing_images = [];
    public array $new_images = [];

    public function rules()
    {
        return [
            'id' => 'required|exists:news,id',
            'title' => ['required', 'string', 'max:255'],
            'paragraph' => ['required', 'string'],
            'author' => ['required', 'string', 'max:255'],
            'is_published' => ['boolean'],
            'category_id' => ['required', 'exists:categories,id'],
            'new_images.*' => ['image', 'max:2048'], // 2MB max per image
        ];
    }

    public function messages()
    {
        return [
            'new_images.*.image' => 'Each file must be an image.',
            'new_images.*.max' => 'Each image must not be larger than 2MB.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
        ];
    }
}
