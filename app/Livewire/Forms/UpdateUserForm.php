<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Validation\Rule;

class UpdateUserForm extends Form
{
    public int $id = 0;
    public string $name = '';
    public string $email = '';
    public string $role = 'regular';
    public string|null $password = null;

    public function rules()
    {
        return [
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->id)],
            'role' => ['required', 'in:admin,supervisor,editor,regular'],
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
