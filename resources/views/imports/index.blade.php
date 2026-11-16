<x-layouts.app title="Importación">
    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <div><h1 class="text-3xl font-bold">Importar datos académicos</h1><p class="text-zinc-600">CSV o XLSX, máximo 10 MB. Revise las filas antes de confirmar.</p></div>
        @if(session('success'))<div class="rounded bg-green-50 p-3 text-green-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="rounded bg-red-50 p-3 text-red-800">{{ $errors->first() }}</div>@endif

        <form method="POST" action="{{ route('imports.preview') }}" enctype="multipart/form-data" class="grid gap-4 rounded-xl border p-5 md:grid-cols-3">
            @csrf
            <select name="tipo" required class="rounded border p-2">
                <option value="estudiantes">Estudiantes</option><option value="profesores">Profesores</option><option value="asignaturas">Asignaturas</option>
            </select>
            <input type="file" name="archivo" accept=".csv,.txt,.xlsx" required class="rounded border p-2">
            <button class="rounded bg-orange-600 p-2 font-semibold text-white">Validar y previsualizar</button>
        </form>

        @isset($rows)
            <div class="rounded-xl border p-4">
                <h2 class="mb-3 text-xl font-semibold">Vista previa: {{ count($rows) }} filas</h2>
                @if($rowErrors)
                    <div class="mb-4 rounded bg-red-50 p-3 text-red-800">
                        @foreach($rowErrors as $line => $messages)<p><strong>Fila {{ $line }}:</strong> {{ implode(' ', $messages) }}</p>@endforeach
                    </div>
                @endif
                <div class="max-h-96 overflow-auto"><table class="w-full text-sm"><tbody>
                    @foreach(array_slice($rows, 0, 50) as $index => $row)
                        <tr class="border-t @if(isset($rowErrors[$index + 2])) bg-red-50 @endif"><td class="p-2">{{ $index + 2 }}</td>@foreach($row as $value)<td class="p-2">{{ $value }}</td>@endforeach</tr>
                    @endforeach
                </tbody></table></div>
                @unless($rowErrors)
                    <form method="POST" action="{{ route('imports.store') }}" class="mt-4">@csrf<button class="rounded bg-green-700 px-4 py-2 font-semibold text-white">Confirmar importación</button></form>
                @endunless
            </div>
        @endisset
    </div>
</x-layouts.app>
