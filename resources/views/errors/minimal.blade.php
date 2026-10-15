<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/fts-logo.svg') }}">

        <style>
            :root {
                --primary: #666cff;
                --primary-light: #7a7fff;
                --primary-dark: #5f5aec;
                --ink: #4c4e64;
                --muted: #8a8d93;
                --bg: #f8f7fa;
                --card-bg: #ffffff;
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    --ink: #d0d4f1;
                    --muted: #a3a6c2;
                    --bg: #25293c;
                    --card-bg: #2f3349;
                }
            }

            * {
                box-sizing: border-box;
            }

            html, body {
                height: 100%;
                margin: 0;
            }

            body {
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                background: var(--bg);
                color: var(--ink);
                overflow: hidden;
                position: relative;
            }

            /* Soft floating brand-colour blobs for a modern, alive backdrop */
            .blob {
                position: fixed;
                border-radius: 50%;
                filter: blur(60px);
                opacity: 0.35;
                z-index: 0;
                pointer-events: none;
                animation: drift 14s ease-in-out infinite;
            }

            .blob-1 {
                width: 380px;
                height: 380px;
                top: -120px;
                left: -100px;
                background: radial-gradient(circle, var(--primary-light), transparent 70%);
                animation-delay: 0s;
            }

            .blob-2 {
                width: 320px;
                height: 320px;
                bottom: -140px;
                right: -80px;
                background: radial-gradient(circle, var(--primary), transparent 70%);
                animation-delay: -6s;
            }

            .blob-3 {
                width: 220px;
                height: 220px;
                top: 45%;
                right: 12%;
                background: radial-gradient(circle, var(--primary-dark), transparent 70%);
                opacity: 0.2;
                animation-delay: -3s;
            }

            @keyframes drift {
                0%, 100% { transform: translate(0, 0) scale(1); }
                50% { transform: translate(30px, -25px) scale(1.08); }
            }

            .wrapper {
                position: relative;
                z-index: 1;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
            }

            .card {
                width: 100%;
                max-width: 480px;
                background: var(--card-bg);
                border-radius: 20px;
                padding: 48px 40px;
                text-align: center;
                box-shadow: 0 24px 60px -20px rgba(102, 108, 255, 0.35), 0 4px 16px rgba(76, 78, 100, 0.08);
                animation: rise 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            @keyframes rise {
                from {
                    opacity: 0;
                    transform: translateY(24px) scale(0.98);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .icon {
                width: 88px;
                height: 88px;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 22px;
                background: linear-gradient(135deg, rgba(102, 108, 255, 0.18), rgba(122, 127, 255, 0.08));
                animation: float 3.5s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-8px); }
            }

            .icon svg {
                width: 42px;
                height: 42px;
                stroke: var(--primary-dark);
            }

            .code {
                font-size: 4.25rem;
                font-weight: 800;
                line-height: 1;
                letter-spacing: -0.03em;
                margin: 0 0 8px;
                background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .message {
                font-size: 1.05rem;
                font-weight: 600;
                margin: 0 0 8px;
                color: var(--ink);
            }

            .hint {
                font-size: 0.9rem;
                color: var(--muted);
                margin: 0 0 28px;
                line-height: 1.5;
            }

            .btn-home {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 28px;
                border-radius: 10px;
                background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
                color: #fff;
                font-weight: 600;
                font-size: 0.95rem;
                text-decoration: none;
                box-shadow: 0 10px 24px -8px rgba(102, 108, 255, 0.6);
                transition: transform 0.18s ease, box-shadow 0.18s ease;
            }

            .btn-home:hover {
                transform: translateY(-2px);
                box-shadow: 0 14px 28px -8px rgba(102, 108, 255, 0.7);
            }

            .btn-home svg {
                width: 18px;
                height: 18px;
                stroke: currentColor;
            }

            @media (max-width: 480px) {
                .card {
                    padding: 36px 24px;
                }

                .code {
                    font-size: 3.25rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <div class="wrapper">
            <div class="card">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 8v5"></path>
                        <circle cx="12" cy="16" r="0.6" fill="currentColor"></circle>
                    </svg>
                </div>

                <p class="code">@yield('code')</p>
                <p class="message">@yield('message')</p>
                <p class="hint">@yield('hint', __("Let's get you back to somewhere that works."))</p>

                <a href="{{ route('home') }}" class="btn-home">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 10.5 12 3l9 7.5"></path>
                        <path d="M5 9.5V21h14V9.5"></path>
                    </svg>
                    {{ __('Go to Home') }}
                </a>
            </div>
        </div>
    </body>
</html>
