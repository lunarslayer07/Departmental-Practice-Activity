@extends('layouts.app')

@section('title', $plant->name)

@section('content')
    <div class="page-head">
        <div><h1>{{ $plant->name }}</h1><div class="muted">Plant inventory record</div></div>
        <div class="actions">
            <a class="button secondary" href="{{ route('plants.index') }}">Back to plants</a>
            <a class="button" href="{{ route('plants.edit', $plant) }}">Edit plant</a>
            <form method="POST" action="{{ route('plants.destroy', $plant) }}" onsubmit="return confirm('Remove this plant from inventory?')">
                @csrf @method('DELETE')
                <button class="button danger" type="submit">Delete</button>
            </form>
        </div>
    </div>
    <section class="card">
        @if ($plant->image_path)
            <img class="plant-detail-image" src="{{ asset('storage/'.$plant->image_path) }}" alt="{{ $plant->name }}">
        @else
            <div class="plant-detail-placeholder" aria-label="No photo uploaded">🌿</div>
        @endif
        <dl class="detail-grid">
            <dt>Category</dt><dd>{{ $plant->category }}</dd>
            <dt>Unit price</dt><dd>{{ number_format((float) $plant->unit_price, 2) }}</dd>
            <dt>Stock status</dt><dd><span class="badge {{ $plant->stock_status === 'out_of_stock' ? 'out' : '' }}">{{ $plant->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</span></dd>
            <dt>Description</dt><dd>{{ $plant->description }}</dd>
            <dt>Created by</dt><dd>{{ $plant->user->name }}</dd>
            <dt>Last updated</dt><dd>{{ $plant->updated_at->format('M j, Y g:i A') }}</dd>
        </dl>
    </section>
@endsection
