<?php

declare(strict_types=1);

namespace App\Http\ViewComposers;

use App\Domains\Core\Models\NavigationItem;
use Illuminate\Contracts\View\View;

/**
 * View Composer for Layout Data
 * Provides shared data to layout views (auth status, cart, user info, navigation)
 */
class LayoutComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $isAuthenticated = auth()->check();
        $sessionCart = array_values(session('cart.items', []));
        $cartCount = collect($sessionCart)->sum(fn ($item) => (int) ($item['quantity'] ?? 0));
        $userName = auth()->user()?->name;

        $headerMenu = NavigationItem::getMenu('header');
        $footerMenu = NavigationItem::getMenu('footer');

        $view->with([
            'isAuthenticated' => $isAuthenticated,
            'sessionCart' => $sessionCart,
            'cartCount' => $cartCount,
            'userName' => $userName,
            'headerMenu' => $headerMenu,
            'footerMenu' => $footerMenu,
        ]);
    }
}

