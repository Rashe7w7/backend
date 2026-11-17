<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportIndicatorsRequest;
use App\Models\Indicador;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportExportController extends Controller
{
    public function csv(ExportIndicatorsRequest $request, AuditService $auditService): Response
    {
        $data = $request->validated();
        $indicators = Indicador::query()->with('categoria')->whereKey($data['indicadores'])->orderBy('nombre')->get();
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, ['Indicador', 'Categoría', 'Especificación', 'Entidad', 'Curso', 'Valor']);

        foreach ($indicators as $indicator) {
            fputcsv($stream, [
                $indicator->nombre,
                $indicator->categoria?->nombre ?? 'Sin categoría',
                $data['especificacion'],
                $data['entidad_id'],
                $data['curso_academico_id'] ?? '',
                $data['valores'][$indicator->id] ?? '',
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        $auditService->record('csv.generado', 'indicadores', null, null, [
            'especificacion' => $data['especificacion'],
            'entidad_id' => $data['entidad_id'],
            'curso_academico_id' => $data['curso_academico_id'] ?? null,
            'indicadores' => $data['indicadores'],
        ]);

        return response("\xEF\xBB\xBF".$csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="indicadores-'.now()->format('Ymd-His').'.csv"',
        ]);
    }

    public function excel(Request $request): Response
    {
        $rows = $this->rows($request);
        $cell = fn ($value) => '<Cell><Data ss:Type="String">'.htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</Data></Cell>';
        $tableRows = '<Row>'.$cell('Indicador').$cell('Categoría').$cell('Valor').'</Row>';
        foreach ($rows as $row) {
            $tableRows .= '<Row>'.$cell($row['nombre']).$cell($row['categoria']).$cell($row['valor']).'</Row>';
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<?mso-application progid="Excel.Sheet"?>'
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Worksheet ss:Name="Indicadores"><Table>'.$tableRows.'</Table></Worksheet></Workbook>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="indicadores-'.now()->format('Ymd-His').'.xml"',
        ]);
    }

    public function pdf(Request $request): Response
    {
        $lines = ['INFORME DE INDICADORES', 'Generado: '.now()->format('d/m/Y H:i'), ''];
        foreach ($this->rows($request) as $row) {
            $lines[] = sprintf('%s | %s | %s', $row['nombre'], $row['categoria'], $row['valor']);
        }

        return response($this->minimalPdf($lines), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="indicadores-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    /** @return array<int, array{nombre: string, categoria: string, valor: mixed}> */
    private function rows(Request $request): array
    {
        return Indicador::query()->with(['categoria', 'valores'])
            ->when($request->integer('indicador_id'), fn ($q, $id) => $q->whereKey($id))
            ->orderBy('nombre')->get()->map(fn ($indicator) => [
                'nombre' => $indicator->nombre,
                'categoria' => $indicator->categoria?->nombre ?? 'Sin categoría',
                'valor' => $indicator->valores->last()?->valor ?? 'Sin valor',
            ])->all();
    }

    private function minimalPdf(array $lines): string
    {
        $escape = fn (string $line) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], iconv('UTF-8', 'Windows-1252//TRANSLIT', $line) ?: $line);
        $pages = array_chunk($lines, 50);
        $fontId = 3 + (count($pages) * 2);
        $pageIds = [];
        $objects = [];
        foreach ($pages as $index => $pageLines) {
            $pageId = 3 + ($index * 2);
            $contentId = $pageId + 1;
            $pageIds[] = $pageId.' 0 R';
            $content = "BT /F1 11 Tf 50 790 Td 14 TL\n";
            foreach ($pageLines as $line) {
                $content .= '('.$escape($line).") Tj T*\n";
            }
            $content .= 'ET';
            $objects[$pageId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 {$fontId} 0 R >> >> /Contents {$contentId} 0 R >>";
            $objects[$contentId] = '<< /Length '.strlen($content)." >>\nstream\n{$content}\nendstream";
        }
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $pageIds).'] /Count '.count($pages).' >>';
        $objects[$fontId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n{$object}\nendobj\n";
        }
        $xref = strlen($pdf);
        $size = $fontId + 1;
        $pdf .= "xref\n0 {$size}\n0000000000 65535 f \n";
        for ($id = 1; $id < $size; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }

        return $pdf."trailer << /Size {$size} /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
    }
}
