@extends('layouts.app')

@section('title', 'Edit plant')

@section('content')
    <div class="page-head"><div><h1>Edit plant</h1><div class="muted">Update {{ $plant->name }} in your inventory.</div></div></div>
    <section class="card">
        <form method="POST" action="{{ route('plants.update', $plant) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('plants._form', ['submitLabel' => 'Save changes'])
        </form>
    </section>
@endsection
