<?php

namespace App\Providers;

use App\Models\Indicador;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        Gate::define('manage-users', fn (User $user): bool => $user->isAdministrator());
        Gate::define('write-academic-data', fn (User $user): bool => $user->canWriteAcademicData());
        Gate::define('manage-indicator', function (User $user, Indicador|string|null $indicator = null): Response {
            $specification = $indicator instanceof Indicador ? $indicator->especificacion : $indicator;

            return $user->canManageIndicatorSpecification($specification)
                ? Response::allow()
                : Response::deny('No tienes permisos para realizar esta operación con este tipo de indicador');
        });
        Gate::define('view-student-helpers', fn (User $user): bool => $user->canViewStudentHelpers());
        Gate::define('manage-student-helpers', fn (User $user): bool => $user->canManageStudentHelpers());
        Gate::define('delete-student-helpers', fn (User $user): bool => $user->canDeleteStudentHelpers());
        Gate::define('export-indicator-documents', fn (User $user): Response => $user->canManageAnyIndicator()
            ? Response::allow()
            : Response::deny('No tienes permisos para realizar esta operación con este tipo de indicador'));
    }
}
