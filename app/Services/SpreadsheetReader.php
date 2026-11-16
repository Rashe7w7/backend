<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class SpreadsheetReader
{
    public function read(UploadedFile $file): array
    {
        return strtolower($file->getClientOriginalExtension()) === 'xlsx'
            ? $this->xlsx($file->getRealPath())
            : $this->csv($file->getRealPath());
    }

    private function csv(string $path): array
    {
        $handle = fopen($path, 'rb');
        if (! $handle) {
            throw new RuntimeException('No se pudo leer el archivo.');
        }
        $delimiter = str_contains((string) fgets($handle), ';') ? ';' : ',';
        rewind($handle);
        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $this->associate($rows);
    }

    private function xlsx(string $path): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new RuntimeException('El archivo XLSX no es válido.');
        }
        $shared = [];
        if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            foreach ((new SimpleXMLElement($xml))->si as $item) {
                $shared[] = $this->text($item);
            }
        }
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheet === false) {
            throw new RuntimeException('El XLSX no contiene una primera hoja.');
        }
        $rows = [];
        foreach ((new SimpleXMLElement($sheet))->sheetData->row as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $column = count($values);
                if (preg_match('/^([A-Z]+)[0-9]+$/i', (string) $cell['r'], $match)) {
                    $column = 0;
                    foreach (str_split(strtoupper($match[1])) as $letter) {
                        $column = $column * 26 + ord($letter) - ord('A') + 1;
                    }
                    $column--;
                }
                $values = array_pad($values, $column + 1, null);
                $value = (string) $cell->v;
                $values[$column] = match ((string) $cell['t']) {
                    's' => $shared[(int) $value] ?? '',
                    'inlineStr' => $this->text($cell->is),
                    default => $value,
                };
            }
            $rows[] = $values;
        }

        return $this->associate($rows);
    }

    private function text(SimpleXMLElement $element): string
    {
        if (isset($element->t)) {
            return (string) $element->t;
        }

        $text = '';
        foreach ($element->r as $run) {
            $text .= (string) $run->t;
        }

        return $text;
    }

    private function associate(array $rows): array
    {
        if (count($rows) < 2) {
            return [];
        }
        $headers = array_map(fn ($value) => strtolower(trim((string) $value)), array_shift($rows));
        $headers[0] = preg_replace('/^\x{FEFF}/u', '', $headers[0]);

        return array_values(array_map(function ($row) use ($headers) {
            $row = array_pad($row, count($headers), null);

            return array_combine($headers, array_slice($row, 0, count($headers)));
        }, array_filter($rows, fn ($row) => count(array_filter($row, fn ($value) => $value !== null && $value !== '')) > 0)));
    }
}
