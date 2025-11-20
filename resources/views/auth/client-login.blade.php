<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Prompt Quality Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/mntl.svg') }}">
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

        /* New Layout Wrapper */
        .login-layout {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            width: 100%;
            max-width: 400px;
            align-items: center;
        }

        .intro-section {
            text-align: center;
            width: 100%;
        }

        .container {
            width: 100%;
            padding: 40px;
            /* glass-card class handles background */
        }

        .how-to-section {
            text-align: center;
            padding: 16px;
            margin-top: 20px;
            border-top: 1px solid rgba(var(--clr-border-rgb-light), 0.5);
        }

        @media (min-width: 900px) {
            .login-layout {
                flex-direction: row;
                max-width: 1000px;
                justify-content: space-between;
                gap: 60px;
                align-items: center;
            }

            .intro-section {
                text-align: left;
                flex: 1;
                max-width: 480px;
            }

            .how-to-section {
                text-align: left;
                padding: 20px 0 0 0;
                margin-top: 30px;
            }

            .how-to-section .help-text {
                margin-left: 0 !important;
                max-width: 100% !important;
            }

            .container {
                max-width: 400px;
                flex-shrink: 0;
            }
            
            .logo {
                justify-content: flex-start !important;
            }

            .service-description {
                text-align: left !important;
                padding: 0 !important;
            }

            .service-description p {
                margin-left: 0 !important;
                max-width: 100% !important;
            }
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
            width: 160px;
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

        .service-description {
            text-align: center;
            margin-bottom: 32px;
            padding: 0 20px;
        }

        .service-description h2 {
            color: var(--clr-text-main);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .service-description p {
            color: var(--clr-text-sub);
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 16px;
            max-width: 320px;
            margin-left: auto;
            margin-right: auto;
        }

        .description-highlight {
            color: var(--clr-primary) !important;
            font-weight: 500;
            font-size: 15px !important;
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
    <div class="login-layout">
        <!-- Left Side: Intro -->
        <div class="intro-section">
            <div class="logo">
                <img src="{{ asset('img/mntl.svg') }}" alt="MNTL Logo">
            </div>

            <div class="service-description">
                <h2>Mental Prompt Analytics</h2>
                <p>Transform your prompts into powerful, effective communication tools. Track, analyze, and optimize your prompt quality with real-time insights and data-driven recommendations.</p>
                <p class="description-highlight">Elevate your prompting game with intelligent quality assessment and actionable feedback.</p>
            </div>

            <div class="how-to-section">
                <p style="color: var(--clr-text-main); font-size: 14px; font-weight: 600; margin-bottom: 8px;">First time here?</p>
                <p class="help-text" style="margin-bottom: 12px; max-width: 280px; margin-left: auto; margin-right: auto; line-height: 1.5;">
                    Learn how to install the MCP server and configure your editor to start tracking prompt quality.
                </p>
                <a href="https://github.com/mftzk/mental-prompt/blob/main/README.md" target="_blank" style="color: var(--clr-primary); text-decoration: none; font-weight: 500; font-size: 14px; display: inline-flex; align-items: center; gap: 4px;">
                    <span>Read the Setup Guide</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>
            </div>
        </div>

        <!-- Right Side: Login Card -->
        <div class="container glass-card">
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
        </div>
    </div>
</body>
</html>