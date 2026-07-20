<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // @role('Owner') ... @endrole
        Blade::if('role', function (string $role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        // @hasanyrole('Owner|Manager') ... @endhasanyrole
        Blade::if('hasanyrole', function (string $roles) {
            if (! auth()->check()) {
                return false;
            }

            $roles = explode('|', $roles);

            return auth()->user()->hasAnyRole($roles);
        });

        // @permission('cms.pages.view') ... @endpermission
        Blade::if('permission', function (string $permission) {
            return auth()->check() && auth()->user()->can($permission);
        });
    }
}
