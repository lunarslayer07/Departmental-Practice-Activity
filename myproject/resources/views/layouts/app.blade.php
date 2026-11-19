<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Plant Nursery Inventory') · Greenhouse</title>
    <style>
        :root{font-family:Inter,Segoe UI,Arial,sans-serif;color:#24342d;background:#f4f7f3;font-synthesis:none}
        *{box-sizing:border-box}body{margin:0}a{color:#236c4b;text-decoration:none}a:hover{text-decoration:underline}
        .topbar{background:#173d2b;color:#fff;padding:16px max(24px,calc((100vw - 1120px)/2));display:flex;align-items:center;justify-content:space-between;gap:20px}
        .brand{font-weight:750;color:#fff;font-size:18px}.nav{display:flex;align-items:center;gap:18px}.nav a{color:#e2f2e7}
        .nav form{margin:0}.nav button{background:transparent;color:#fff;border:1px solid #6c9079;padding:8px 12px;border-radius:8px;cursor:pointer}
        main{max-width:1120px;margin:38px auto;padding:0 24px}.page-head{display:flex;justify-content:space-between;align-items:center;gap:18px;margin-bottom:24px}
        h1{font-size:30px;margin:0 0 6px;color:#1c392a}h2{font-size:19px;margin:0 0 18px}.muted{color:#708078}.card{background:#fff;border:1px solid #e3ebe5;border-radius:14px;padding:24px;box-shadow:0 4px 18px #183d2b08}
        .button{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:#26734d;color:#fff;border:0;border-radius:9px;padding:10px 15px;font-weight:650;cursor:pointer;font-size:14px}
        .button:hover{background:#1d5d3d;text-decoration:none}.button.secondary{color:#2c5740;background:#e8f1eb}.button.danger{background:#a94343}.actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
        table{width:100%;border-collapse:collapse;text-align:left}th{font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#728078;background:#f7faf7}
        th,td{padding:14px 12px;border-bottom:1px solid #edf1ed;vertical-align:top}td{font-size:14px}.table-wrap{overflow-x:auto}
        .badge{display:inline-block;padding:5px 9px;border-radius:99px;background:#e7f4eb;color:#287147;font-size:12px;font-weight:700;white-space:nowrap}
        .badge.out{background:#fbeaea;color:#9e3b3b}.alert{padding:13px 16px;border-radius:9px;margin-bottom:18px}.alert.success{background:#e8f5eb;color:#226540}
        .alert.error{background:#fff0ee;color:#9b3930}.field{margin-bottom:18px}.field label{display:block;font-weight:650;margin-bottom:7px}
        input,select,textarea{width:100%;padding:11px 12px;border:1px solid #d5dfd8;border-radius:8px;background:#fff;color:#24342d;font:inherit}
        textarea{min-height:120px;resize:vertical}.field-error{color:#a33d38;font-size:13px;margin-top:5px}.form-grid{max-width:700px}
        .filters{display:grid;grid-template-columns:1.2fr 1fr 1fr auto;gap:12px;align-items:end}.filters .field{margin:0}
        .pagination{margin-top:20px}.detail-grid{display:grid;grid-template-columns:170px 1fr;gap:14px}.detail-grid dt{font-weight:700;color:#697970}.detail-grid dd{margin:0}
        .login-shell{max-width:440px;margin:8vh auto}.login-brand{font-size:34px;margin-bottom:8px}.empty{padding:35px;text-align:center;color:#718078}
        .plant-thumb{width:58px;height:58px;object-fit:cover;border-radius:10px;display:block}
        .plant-placeholder{width:58px;height:58px;border-radius:10px;background:#e8f1eb;display:grid;place-items:center;font-size:26px}
        .plant-detail-image{width:min(100%,520px);height:320px;object-fit:cover;border-radius:12px;margin-bottom:24px}
        .plant-detail-placeholder{width:min(100%,520px);height:220px;border-radius:12px;background:#e8f1eb;display:grid;place-items:center;font-size:68px;margin-bottom:24px}
        @media(max-width:700px){.topbar{align-items:flex-start;flex-direction:column}.nav{flex-wrap:wrap;gap:12px}.page-head{align-items:flex-start;flex-direction:column}.filters{grid-template-columns:1fr}.detail-grid{grid-template-columns:1fr;gap:5px}.detail-grid dd{margin-bottom:10px}main{margin-top:26px}}
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ auth()->check() ? route('plants.index') : route('login') }}">🌿 Greenhouse Inventory</a>
        @auth
            <nav class="nav" aria-label="Main navigation">
                <a href="{{ route('plants.index') }}">Plants</a>
                <a href="{{ route('reports.plants') }}">Reports</a>
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form>
            </nav>
        @endauth
    </header>
    <main>
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert error" role="alert">
                <strong>Please correct the following:</strong>
                <ul style="margin-bottom:0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</body>
</html>
