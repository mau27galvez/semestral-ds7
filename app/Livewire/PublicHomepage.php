<?php

namespace App\Livewire;

use App\Models\News;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PublicHomepage extends Component
{
    #[Computed]
    public function latestNews()
    {
        return News::query()
            ->with(['category', 'author'])
            ->published()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
    }

    #[Computed]
    public function allNews()
    {
        return News::query()
            ->with(['category', 'author'])
            ->published()
            ->orderBy('created_at', 'desc')
            ->paginate(6);
    }

    public function render()
    {
        return view('livewire.public-homepage');
    }
}
