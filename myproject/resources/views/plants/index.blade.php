@extends('layouts.app')

@section('title', 'Plant inventory')

@section('content')
    <div class="page-head">
        <div><h1>Plant inventory</h1><div class="muted">Manage the plants and stock in your nursery.</div></div>
        <a class="button" href="{{ route('plants.create') }}">＋ Add plant</a>
    </div>
    <section class="card">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Photo</th><th>Plant</th><th>Category</th><th>Unit price</th><th>Stock</th><th>Added by</th><th></th></tr></thead>
                <tbody>
                    @forelse ($plants as $plant)
                        <tr>
                            <td>
                                @if ($plant->image_path)
                                    <img class="plant-thumb" src="{{ asset('storage/'.$plant->image_path) }}" alt="{{ $plant->name }}">
                                @else
                                    <span class="plant-placeholder" aria-label="No photo uploaded">🌱</span>
                                @endif
                            </td>
                            <td><a href="{{ route('plants.show', $plant) }}"><strong>{{ $plant->name }}</strong></a></td>
                            <td>{{ $plant->category }}</td>
                            <td>{{ number_format((float) $plant->unit_price, 2) }}</td>
                            <td><span class="badge {{ $plant->stock_status === 'out_of_stock' ? 'out' : '' }}">{{ $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</span></td>
                            <td>{{ $plant->user->name }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('plants.edit', $plant) }}">Edit</a>
                                    <form method="POST" action="{{ route('plants.destroy', $plant) }}" onsubmit="return confirm('Remove this plant from inventory?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="7">No plants yet. Add your first plant to start your inventory.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $plants->links() }}</div>
    </section>
@endsection
