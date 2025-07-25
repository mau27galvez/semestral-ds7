<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\News;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class NewsCategory extends Component
{
    public Category $category;

    #[Computed]
    public function news()
    {
        return News::query()
            ->with(['category', 'author'])
            ->published()
            ->where('category_id', $this->category->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.public-homepage');
    }
}
