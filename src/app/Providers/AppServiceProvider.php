<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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
