{{-- Minimales Layout für die öffentlichen Einladungsseiten --}}
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') – {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: #f3f4f6; color: #111827; min-height: 100vh;
            display: flex; align-items: center; justify-content: center; padding: 1.5rem;
        }
        .card {
            background: #fff; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,.1);
            width: 100%; max-width: 26rem; padding: 2rem;
        }
        h1 { font-size: 1.25rem; margin-bottom: .5rem; }
        p { color: #4b5563; font-size: .925rem; line-height: 1.5; margin-bottom: 1rem; }
        label { display: block; font-size: .875rem; font-weight: 600; margin-bottom: .25rem; }
        input {
            width: 100%; padding: .55rem .75rem; border: 1px solid #d1d5db; border-radius: .5rem;
            font-size: .95rem; margin-bottom: 1rem;
        }
        input:focus { outline: 2px solid #059669; border-color: transparent; }
        button {
            width: 100%; padding: .65rem; background: #059669; color: #fff; font-weight: 600;
            border: 0; border-radius: .5rem; font-size: .95rem; cursor: pointer;
        }
        button:hover { background: #047857; }
        .error { color: #b91c1c; font-size: .85rem; margin: -0.5rem 0 1rem; }
        .muted { font-size: .8rem; color: #6b7280; margin-top: 1rem; text-align: center; }
        a { color: #059669; }
    </style>
</head>
<body>
    <div class="card">
        @yield('content')
    </div>
</body>
</html>
