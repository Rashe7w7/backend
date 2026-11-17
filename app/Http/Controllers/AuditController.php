<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->isAdministrator(), 403);

        $records = Auditoria::query()->with('user')
            ->when($request->integer('user_id'), fn ($query, $id) => $query->where('user_id', $id))
            ->when($request->string('accion')->toString(), fn ($query, $action) => $query->where('accion', $action))
            ->when($request->date('desde'), fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($request->date('hasta'), fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('audit.index', compact('records'));
    }
}
