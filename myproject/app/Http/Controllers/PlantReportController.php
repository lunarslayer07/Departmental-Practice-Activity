<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Support\PlantReportPdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PlantReportController extends Controller
{
    private const REPORTS = ['all', 'in_stock', 'out_of_stock', 'category', 'price_list'];

    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);

        return view('reports.plants', [
            'plants' => $this->filteredPlants($filters)->with('user')->latest()->paginate(15)->withQueryString(),
            'categories' => Plant::query()->distinct()->orderBy('category')->pluck('category'),
            'filters' => $filters,
        ]);
    }

    public function pdf(Request $request, PlantReportPdf $pdf): Response
    {
        $filters = $this->validatedFilters($request);
        $plants = $this->filteredPlants($filters)->orderBy('name')->get();
        $content = $pdf->render($plants, $filters);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="plant-report.pdf"',
        ]);
    }

    public function excel(Request $request): Response
    {
        $filters = $this->validatedFilters($request);
        $plants = $this->filteredPlants($filters)->orderBy('name')->get();

        return response()
            ->view('reports.excel', compact('plants', 'filters'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="plant-report.xls"');
    }

    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'report' => ['nullable', 'in:'.implode(',', self::REPORTS)],
            'category' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:in_stock,out_of_stock'],
        ]);

        return [
            'report' => $validated['report'] ?? 'all',
            'category' => $validated['category'] ?? null,
            'status' => $validated['status'] ?? null,
        ];
    }

    private function filteredPlants(array $filters): Builder
    {
        $query = Plant::query();

        if ($filters['report'] === 'in_stock') {
            $query->where('stock_status', 'in_stock');
        } elseif ($filters['report'] === 'out_of_stock') {
            $query->where('stock_status', 'out_of_stock');
        }

        // The report type and explicit filters are intersected so every export matches the visible report.
        return $query
            ->when($filters['category'], fn (Builder $builder, string $category) => $builder->where('category', $category))
            ->when($filters['status'], fn (Builder $builder, string $status) => $builder->where('stock_status', $status));
    }
}
