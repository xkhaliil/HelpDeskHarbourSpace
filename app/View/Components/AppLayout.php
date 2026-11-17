<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * The title is used in the browser tab and as the big page heading.
     * Full width pages (like the home page) draw their own sections.
     */
    public function __construct(
        public ?string $title = null,
        public bool $fullWidth = false,
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
