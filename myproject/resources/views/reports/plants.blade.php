@extends('layouts.app')

@section('title', 'Plant reports')

@section('content')
    <div class="page-head">
        <div><h1>Plant reports</h1><div class="muted">Filter inventory and export the current report.</div></div>
        <div class="actions">
            <a class="button secondary" href="{{ route('reports.plants.pdf', request()->query()) }}">Download PDF</a>
            <a class="button secondary" href="{{ route('reports.plants.excel', request()->query()) }}">Export Excel</a>
        </div>
    </div>
    <section class="card" style="margin-bottom:20px">
        <form method="GET" action="{{ route('reports.plants') }}" class="filters">
            <div class="field">
                <label for="report">Report type</label>
                <select id="report" name="report">
                    <option value="all" @selected($filters['report'] === 'all')>All plants</option>
                    <option value="in_stock" @selected($filters['report'] === 'in_stock')>In-stock plants</option>
                    <option value="out_of_stock" @selected($filters['report'] === 'out_of_stock')>Out-of-stock plants</option>
                    <option value="category" @selected($filters['report'] === 'category')>Plants by category</option>
                    <option value="price_list" @selected($filters['report'] === 'price_list')>Plant price list</option>
                </select>
            </div>
            <div class="field">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="status">Stock status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    <option value="in_stock" @selected($filters['status'] === 'in_stock')>In stock</option>
                    <option value="out_of_stock" @selected($filters['status'] === 'out_of_stock')>Out of stock</option>
                </select>
            </div>
            <button class="button" type="submit">Apply filters</button>
        </form>
    </section>
    <section class="card">
        <div class="page-head" style="margin-bottom:10px">
            <h2>{{ $plants->total() }} {{ Str::plural('plant', $plants->total()) }}</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Plant</th><th>Category</th><th>Unit price</th><th>Stock status</th><th>Description</th></tr></thead>
                <tbody>
                    @forelse ($plants as $plant)
                        <tr>
                            <td><a href="{{ route('plants.show', $plant) }}"><strong>{{ $plant->name }}</strong></a></td>
                            <td>{{ $plant->category }}</td>
                            <td>{{ number_format((float) $plant->unit_price, 2) }}</td>
                            <td><span class="badge {{ $plant->stock_status === 'out_of_stock' ? 'out' : '' }}">{{ $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</span></td>
                            <td>{{ $plant->description }}</td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="5">No plants match the selected filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $plants->links() }}</div>
    </section>
@endsection
