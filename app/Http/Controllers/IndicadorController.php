<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndicatorApplicationRequest;
use App\Http\Requests\StoreIndicatorDefinitionRequest;
use App\Models\Carrera;
use App\Models\CursoAcademico;
use App\Models\Indicador;
use App\Models\IndicadorAnio;
use App\Models\IndicadorCarrera;
use App\Models\IndicadorDepartamento;
use App\Models\IndicadorFacultad;
use App\Models\IndicadorGrupo;
use App\Models\IndicadorValor;
use App\Services\AcademicScopeService;
use App\Services\AuditService;
use App\Services\IndicadorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IndicadorController extends Controller
{
    public function index(Request $request, AcademicScopeService $scopeService): View
    {
        // Cargar solo lo necesario; categoría es decorativa y no se persiste
        $indicadores = Indicador::query()->with('categoria')->orderBy('nombre')->get();
        $cursosAcademicos = CursoAcademico::all();

        // Entidades relacionadas para selección
        $carreras = Carrera::all();

        $applications = collect([
            ...IndicadorGrupo::query()->with(['indicador.categoria', 'grupo.anioAcademico.carrera'])->get(),
            ...IndicadorDepartamento::query()->with(['indicador.categoria', 'departamento'])->get(),
            ...IndicadorCarrera::query()->with(['indicador.categoria', 'carrera'])->get(),
            ...IndicadorAnio::query()->with(['indicador.categoria', 'anioAcademico.carrera'])->get(),
            ...IndicadorFacultad::query()->with(['indicador.categoria', 'facultad'])->get(),
        ])->filter(function ($application) use ($request, $scopeService): bool {
            [$type, $entityId] = match (true) {
                $application instanceof IndicadorGrupo => ['grupo', $application->grupo_id],
                $application instanceof IndicadorDepartamento => ['departamento', $application->departamento_id],
                $application instanceof IndicadorCarrera => ['carrera', $application->carrera_id],
                $application instanceof IndicadorAnio => ['anno', $application->anio_academico_id],
                default => ['facultad', $application->facultad_id],
            };

            return $entityId !== null && $scopeService->canAccess($request->user(), $type, (int) $entityId);
        });

        $applicationEntities = $scopeService->entityOptions($request->user());
        $exportEntities = $scopeService->entityOptions($request->user(), true);

        return view('indicadores', compact('indicadores', 'cursosAcademicos', 'carreras', 'applications', 'applicationEntities', 'exportEntities'));
    }

    public function store(StoreIndicatorDefinitionRequest $request, AuditService $audit): RedirectResponse
    {
        $indicador = Indicador::query()->create($request->validated());
        $audit->recordModel('indicador.creado', $indicador);

        return redirect()->route('indicadores')->with('success', 'Indicador creado exitosamente.');
    }

    public function apply(
        StoreIndicatorApplicationRequest $request,
        IndicadorService $service,
        AuditService $audit,
    ): RedirectResponse {
        $data = $request->validated();
        $application = $service->apply(Indicador::query()->findOrFail($data['indicador_id']), $data);
        $audit->recordModel('indicador.aplicado', $application);

        return back()->with('success', 'Indicador aplicado correctamente.');
    }

    public function destroyApplication(
        Request $request,
        string $type,
        int $application,
        AcademicScopeService $scopeService,
        AuditService $audit,
    ): RedirectResponse {
        [$model, $entityKey] = match ($type) {
            'grupo' => [IndicadorGrupo::class, 'grupo_id'],
            'departamento' => [IndicadorDepartamento::class, 'departamento_id'],
            'carrera' => [IndicadorCarrera::class, 'carrera_id'],
            'anno' => [IndicadorAnio::class, 'anio_academico_id'],
            'facultad' => [IndicadorFacultad::class, 'facultad_id'],
            default => abort(404),
        };
        $record = $model::query()->findOrFail($application);
        abort_unless($scopeService->canAccess($request->user(), $type, (int) $record->{$entityKey}), 403);
        $before = $record->attributesToArray();
        $record->delete();
        $audit->record('indicador.aplicacion_eliminada', $model, $application, $before);

        return back()->with('success', 'Aplicación eliminada correctamente.');
    }

    public function storeValores(Request $request)
    {
        // Validar y guardar cada valor de indicador
        if ($request->has('valores') && is_array($request->valores)) {
            foreach ($request->valores as $indicador_id => $valor) {
                if (! is_numeric($indicador_id) || ! is_numeric($valor)) {
                    continue;
                }

                // Validar que el indicador existe
                $indicador = Indicador::find($indicador_id);
                if ($indicador) {
                    IndicadorValor::updateOrCreate(
                        ['indicador_id' => $indicador_id],
                        ['valor' => $valor]
                    );
                }
            }
        }

        return redirect()->route('indicadores')->with('success', 'Valores guardados exitosamente');
    }
}
