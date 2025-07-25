<?php

namespace App\Livewire;

use App\Models\News;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class PublicHomepage extends Component
{
    #[Computed]
    public function news()
    {
        return News::query()
            ->with(['category', 'author'])
            ->published()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.public-homepage');
    }
}
