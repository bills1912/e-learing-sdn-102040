<?php

namespace App\Livewire;

use App\Support\Nav;
use Illuminate\Contracts\View\View;
use Livewire\Component;

abstract class BaseComponent extends Component
{
    protected function view(string $view, array $data = [], string $pageTitle = '', ?string $pageSubtitle = null): View
    {
        $role = auth()->user()->role ?? 'siswa';

        return view($view, $data)->layout('layouts.app', [
            'navItems' => Nav::items($role),
            'pageTitle' => $pageTitle,
            'pageSubtitle' => $pageSubtitle,
            'title' => $pageTitle,
        ]);
    }
}
