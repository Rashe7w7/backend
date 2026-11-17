<meta charset="UTF-8">
<table border="1">
    <thead><tr><th>Indicador</th><th>Categoría</th><th>Valor</th></tr></thead>
    <tbody>@foreach($rows as $row)<tr><td>{{ $row['nombre'] }}</td><td>{{ $row['categoria'] }}</td><td>{{ $row['valor'] }}</td></tr>@endforeach</tbody>
</table>
