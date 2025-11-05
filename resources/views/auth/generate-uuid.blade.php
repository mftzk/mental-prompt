<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate UUID - Prompt Quality Dashboard</title>
    @vite(['resources/css/theme.css', 'resources/js/theme.js'])
    <style>
        body {
            background: var(--clr-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: var(--clr-primary);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .logo p {
            color: var(--clr-text-sub);
            font-size: 14px;
        }

        #generateSection {
            text-align: center;
        }

        #generateSection p {
            color: var(--clr-text-sub);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        #resultSection {
            display: none;
        }

        .uuid-display {
            background: var(--clr-card-bg);
            border: 2px solid var(--clr-border);
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
            position: relative;
        }

        .uuid-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--clr-text-sub);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .uuid-value {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            color: var(--clr-text-main);
            word-break: break-all;
            line-height: 1.6;
        }

        .copy-button {
            margin-top: 12px;
            width: 100%;
            background: var(--clr-primary);
            padding: 10px;
            font-size: 14px;
            border-radius: 8px;
            color: var(--clr-text-on-primary);
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .copy-button.copied {
            background: var(--clr-accent);
        }

        .warning-box {
            background: rgba(239, 150, 81, 0.1);
            border-left: 4px solid var(--clr-accent);
            padding: 16px;
            border-radius: 8px;
            margin: 24px 0;
        }

        .warning-box strong {
            display: block;
            color: var(--clr-accent);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .warning-box p {
            color: var(--clr-text-main);
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .actions a,
        .actions button {
            flex: 1;
            text-align: center;
            text-decoration: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--clr-primary);
            color: var(--clr-text-on-primary);
            border: none;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--clr-card-bg);
            color: var(--clr-text-main);
            border: 2px solid var(--clr-border);
        }

        .btn-secondary:hover {
            background: var(--clr-border);
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--clr-text-on-primary);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
        }

        .back-link a {
            color: var(--clr-primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .back-link a:hover {
            color: var(--clr-accent);
            text-decoration: underline;
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="container glass-card">
        <div class="logo">
            <h1>🎯 Prompt Quality</h1>
            <p>Generate Client UUID</p>
        </div>

        <div id="generateSection">
            <p>
                Click the button below to generate a unique UUID for accessing your prompt quality analytics dashboard.
            </p>
            <button onclick="generateUUID()" id="generateBtn">
                Generate UUID
            </button>
        </div>

        <div id="resultSection">
            <div class="uuid-display">
                <div class="uuid-label">Your Client UUID</div>
                <div class="uuid-value" id="uuidValue"></div>
                <button class="copy-button" onclick="copyToClipboard()" id="copyBtn">
                    📋 Copy to Clipboard
                </button>
            </div>

            <div class="warning-box">
                <strong>⚠️ Important: Save Your UUID</strong>
                <p>
                    This UUID is your key to accessing your analytics dashboard. 
                    Save it in a secure location. You'll need it to log in.
                </p>
            </div>

            <div class="actions">
                <a href="{{ route('login') }}" class="btn-primary">
                    Go to Login
                </a>
                <button onclick="generateAnother()" class="btn-secondary">
                    Generate Another
                </button>
            </div>
        </div>

        <div class="back-link">
            <a href="{{ route('login') }}">← Back to Login</a>
        </div>
    </div>

    <script>
        async function generateUUID() {
            const btn = document.getElementById('generateBtn');
            const originalText = btn.innerHTML;
            
            try {
                // Show loading state
                btn.disabled = true;
                btn.innerHTML = '<span class="loading"></span>';

                // Call API to generate UUID
                const response = await fetch('/api/generate-uuid', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to generate UUID');
                }

                const data = await response.json();

                // Display the UUID
                document.getElementById('uuidValue').textContent = data.uuid;
                document.getElementById('generateSection').style.display = 'none';
                document.getElementById('resultSection').style.display = 'block';

            } catch (error) {
                alert('Error generating UUID: ' + error.message);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        function copyToClipboard() {
            const uuidText = document.getElementById('uuidValue').textContent;
            const btn = document.getElementById('copyBtn');
            
            navigator.clipboard.writeText(uuidText).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '✓ Copied!';
                btn.classList.add('copied');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('copied');
                }, 2000);
            }).catch(err => {
                alert('Failed to copy: ' + err);
            });
        }

        function generateAnother() {
            document.getElementById('generateSection').style.display = 'block';
            document.getElementById('resultSection').style.display = 'none';
            document.getElementById('generateBtn').disabled = false;
            document.getElementById('generateBtn').innerHTML = 'Generate UUID';
        }
    </script>
</body>
</html>

