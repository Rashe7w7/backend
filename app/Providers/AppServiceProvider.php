<?php

namespace App\Providers;

use App\Models\AnioAcademico;
use App\Models\Carrera;
use App\Models\Departamento;
use App\Models\Facultad;
use App\Models\Grupo;
use App\Services\AuditService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
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

        Gate::define('manage-users', fn ($user) => $user->isAdministrator());
        Gate::define('write-academic-data', fn ($user) => $user->canWriteAcademicData());
        Gate::define('create-indicators', fn ($user) => collect([
            'administrador', 'ppa', 'jefe_departamento', 'rector', 'vicerrector', 'decano',
        ])->contains(fn (string $role) => $user->hasRole($role)));
        Gate::define('manage-indicator-definitions', fn ($user) => $user->isAdministrator());
        Gate::define('view-audit', fn ($user) => $user->isAdministrator());

        Event::listen(Login::class, fn (Login $event) => app(AuditService::class)
            ->record('sesion.iniciada', 'users', $event->user->getAuthIdentifier()));
        Event::listen(Logout::class, fn (Logout $event) => app(AuditService::class)
            ->record('sesion.cerrada', 'users', $event->user?->getAuthIdentifier()));

        foreach ([Facultad::class, Departamento::class, Carrera::class, AnioAcademico::class, Grupo::class] as $academicModel) {
            $academicModel::created(fn (Model $model) => app(AuditService::class)->recordModel('estructura.creada', $model));
            $academicModel::updated(fn (Model $model) => app(AuditService::class)->recordModel('estructura.actualizada', $model, $model->getOriginal()));
            $academicModel::deleted(fn (Model $model) => app(AuditService::class)->recordModel('estructura.eliminada', $model, $model->getOriginal()));
        }
    }
}
