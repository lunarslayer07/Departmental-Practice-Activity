@extends('layouts.app')

@section('title', 'Administrator login')

@section('content')
    <section class="login-shell">
        <div class="login-brand">Welcome back</div>
        <p class="muted" style="margin-bottom:22px">Sign in to manage your nursery inventory.</p>
        <div class="card">
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="field">
                    <label for="identifier">Email or username</label>
                    <input id="identifier" name="identifier" type="text" value="{{ old('identifier') }}" autocomplete="username" required autofocus>
                    @error('identifier')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <label style="display:flex;align-items:center;gap:8px;margin:0 0 20px;font-size:14px">
                    <input name="remember" type="checkbox" value="1" style="width:auto"> Remember me
                </label>
                <button class="button" type="submit" style="width:100%">Sign in</button>
            </form>
        </div>
    </section>
@endsection
