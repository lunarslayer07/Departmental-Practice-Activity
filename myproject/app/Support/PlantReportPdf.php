<?php

namespace App\Support;

use App\Models\Plant;
use Illuminate\Support\Collection;

class PlantReportPdf
{
    public function render(Collection $plants, array $filters): string
    {
        $records = $plants->map(fn (Plant $plant) => sprintf(
            '%-28s %-16s %10s %-13s',
            $this->shorten($plant->name, 28),
            $this->shorten($plant->category, 16),
            number_format((float) $plant->unit_price, 2),
            $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock',
        ))->all();

        $pagesOfRecords = array_chunk($records ?: ['No plants match the selected filters.'], 42);
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        ];
        $pageReferences = [];

        foreach ($pagesOfRecords as $index => $pageRecords) {
            $pageId = 4 + ($index * 2);
            $contentId = $pageId + 1;
            $pageReferences[] = $pageId.' 0 R';
            $lines = [
                'Plant Nursery Inventory Report',
                'Report: '.$this->reportName($filters['report']),
                'Category: '.($filters['category'] ?: 'All').'    Stock: '.($filters['status'] ? str_replace('_', ' ', $filters['status']) : 'All'),
                'Plant                         Category              Price Stock status',
                str_repeat('-', 78),
                ...$pageRecords,
                'Generated: '.now()->format('Y-m-d H:i'),
            ];
            $stream = "BT\n/F1 9 Tf\n50 760 Td\n";

            foreach ($lines as $lineIndex => $line) {
                if ($lineIndex > 0) {
                    $stream .= "0 -14 Td\n";
                }

                $stream .= '('.$this->escape($line).") Tj\n";
            }

            $stream .= "ET\n";
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 3 0 R >> >> /Contents '.$contentId.' 0 R >>';
            $objects[$contentId] = '<< /Length '.strlen($stream)." >>\nstream\n".$stream.'endstream';
        }

        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $pageReferences).'] /Count '.count($pageReferences).' >>';
        ksort($objects);

        $document = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];

        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($document);
            $document .= $id." 0 obj\n".$object."\nendobj\n";
        }

        $xrefOffset = strlen($document);
        $document .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        for ($id = 1; $id <= count($objects); $id++) {
            $document .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }

        $document .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF";

        return $document;
    }

    private function shorten(string $value, int $length): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return strlen($value) > $length ? substr($value, 0, $length - 3).'...' : $value;
    }

    private function escape(string $value): string
    {
        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ''], $value);
    }

    private function reportName(string $report): string
    {
        return match ($report) {
            'in_stock' => 'In-stock plants',
            'out_of_stock' => 'Out-of-stock plants',
            'category' => 'Plants by category',
            'price_list' => 'Plant price list',
            default => 'All plants',
        };
    }
}
