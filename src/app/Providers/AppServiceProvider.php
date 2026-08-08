<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdmin()) {
                return true;
            }

            return null;
        });

        Gate::define('permission', fn (User $user, string $permission) => $user->hasPermission($permission));

        Blade::if('permission', fn (string $permission) => auth()->check() && auth()->user()->can('permission', $permission));

        Livewire::resolveMissingComponent(function ($name) {
            $class = str_replace(".", "\\", ucwords($name, "."));
            $class = str_replace("-", "", $class);

            if (class_exists($class) && is_subclass_of($class, \Livewire\Component::class)) {
                return $class;
            }

            if (class_exists($class . "\Index") && is_subclass_of($class . "\Index", \Livewire\Component::class)) {
                return $class . "\Index";
            }

            if (class_exists("App\\Http\\Livewire\\$class") && is_subclass_of("App\\Http\\Livewire\\$class", \Livewire\Component::class)) {
                return "App\\Http\\Livewire\\$class";
            }

            if (class_exists("App\\Http\\Livewire\\$class\\Index") && is_subclass_of("App\\Http\\Livewire\\$class\\Index", \Livewire\Component::class)) {
                return "App\\Http\\Livewire\\$class\\Index";
            }

            return null;
        });
    }
}
