@extends('layouts.app')

@section('title', 'Add plant')

@section('content')
    <div class="page-head"><div><h1>Add plant</h1><div class="muted">Enter a new plant inventory record.</div></div></div>
    <section class="card">
        <form method="POST" action="{{ route('plants.store') }}" enctype="multipart/form-data">
            @csrf
            @include('plants._form', ['submitLabel' => 'Save plant'])
        </form>
    </section>
@endsection
