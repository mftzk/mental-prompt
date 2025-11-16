<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Prompt Quality Dashboard</title>
    @vite(['resources/css/theme.css', 'resources/js/theme.js'])
    <style>
        :root {
            --clr-card-bg-rgb-light: 255, 255, 255;
            --clr-border-rgb-light: 209, 213, 219;
            --clr-card-bg-rgb-dark: 36, 43, 61;
            --clr-border-rgb-dark: 45, 55, 72;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--clr-bg);
            background-image:
                radial-gradient(at 0% 0%, hsla(253, 100%, 7%, 0.1) 0px, transparent 50%),
                radial-gradient(at 98% 1%, hsla(22, 100%, 7%, 0.15) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 400px;
            width: 100%;
            padding: 40px;
        }

        .glass-card {
            background: rgba(var(--clr-card-bg-rgb-light), 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(var(--clr-border-rgb-light), 0.2);
        }

        :root[data-theme="dark"] .glass-card {
            background: rgba(var(--clr-card-bg-rgb-dark), 0.4);
            border: 1px solid rgba(var(--clr-border-rgb-dark), 0.2);
        }

        .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 160px; /* Lebar yang lebih sesuai */
            height: auto;
        }

        .logo h1 {
            color: var(--clr-text-main);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .logo p {
            color: var(--clr-text-sub);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-weight: 500;
            color: var(--clr-text-main);
            margin-bottom: 8px;
            font-size: 14px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--clr-border);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Courier New', monospace;
            background-color: transparent;
            color: var(--clr-text-main);
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(63, 125, 88, 0.1);
        }

        input[type="text"].error {
            border-color: var(--clr-danger);
        }

        .error-message {
            color: var(--clr-danger);
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .success-message {
            background: rgba(63, 125, 88, 0.1);
            color: var(--clr-primary);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid rgba(63, 125, 88, 0.2);
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--clr-border);
        }

        .divider span {
            background: var(--clr-bg);
            padding: 0 15px;
            color: var(--clr-text-sub);
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        .generate-link {
            text-align: center;
        }

        .generate-link a {
            color: var(--clr-primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .generate-link a:hover {
            color: var(--clr-accent);
            text-decoration: underline;
        }

        .help-text {
            font-size: 13px;
            color: var(--clr-text-sub);
            margin-top: 8px;
        }

        form button[type="submit"] {
            width: 100%;
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="container glass-card">
        <div class="logo">
            <img src="{{ asset('img/mntl.svg') }}" alt="MNTL Logo">
        </div>

        @if(session('error'))
        <div class="error-message" style="background: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
        @endif

        @if(session('message'))
        <div class="success-message">
            {{ session('message') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="uuid">Client UUID</label>
                <input
                    type="text"
                    id="uuid"
                    name="uuid"
                    class="input-styled {{ $errors->has('uuid') ? 'error' : '' }}"
                    placeholder="550e8400-e29b-41d4-a716-446655440000"
                    pattern="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"
                    value="{{ old('uuid') }}"
                    required
                    autofocus
                >
                @if($errors->has('uuid'))
                <div class="error-message">
                    ⚠️ {{ $errors->first('uuid') }}
                </div>
                @endif
                <p class="help-text">Enter your unique client UUID to access your analytics</p>
            </div>

            <button type="submit">
                Access Dashboard
            </button>
        </form>

        <div class="divider">
            <span>Don't have a UUID?</span>
        </div>

        <div class="generate-link">
            <a href="{{ route('generate-uuid') }}">Generate new UUID →</a>
        </div>
    </div>
</body>
</html>

