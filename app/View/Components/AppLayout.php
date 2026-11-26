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
        // The menu is defined once (below) and shown in the header and in the footer.
        return view('layouts.app', [
            'menu' => $this->menu(),
        ]);
    }

    /**
     * The links of the main menu. They depend on who is looking at the page.
     *
     * - "route" is the name of the route the link goes to.
     * - "active" is the route name (or list of names) for which the link is highlighted.
     *   A "*" matches everything, so "admin.tickets.*" also highlights the ticket detail page.
     *
     * The method is protected: public methods of a component are also sent to the view,
     * and that would replace the "menu" array that render() gives to the view.
     *
     * @return array<int, array{label: string, route: string, active: string|array<int, string>}>
     */
    protected function menu(): array
    {
        // Everybody sees the home page, also visitors who are not logged in.
        $links = [
            ['label' => 'Home', 'route' => 'home', 'active' => 'home'],
        ];

        $user = auth()->user();

        // Visitors who are not logged in only get the public links.
        if ($user === null) {
            return $links;
        }

        // Administrators get the links of the admin panel.
        if ($user->isAdmin()) {
            $links[] = ['label' => 'Admin', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'];
            $links[] = ['label' => 'Tickets', 'route' => 'admin.tickets.index', 'active' => 'admin.tickets.*'];
            $links[] = ['label' => 'Manage categories', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*'];
            $links[] = ['label' => 'Users', 'route' => 'admin.users.index', 'active' => 'admin.users.*'];

            return $links;
        }

        // Students and teachers get the links to their own tickets.
        $links[] = ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'];
        $links[] = ['label' => 'My tickets', 'route' => 'tickets.index', 'active' => ['tickets.index', 'tickets.show', 'tickets.edit']];
        $links[] = ['label' => 'New ticket', 'route' => 'tickets.create', 'active' => 'tickets.create'];

        return $links;
    }
}
