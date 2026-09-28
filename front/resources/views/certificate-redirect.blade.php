<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Redirect - Upskills Academy</title>
    <meta http-equiv="refresh" content="2;url={{ $certificateUrl }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #0057A6 0%, #004280 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 48px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .logo {
            margin-bottom: 32px;
        }

        .logo img {
            max-width: 220px;
            height: auto;
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #0057A6 0%, #3379b8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon svg {
            width: 40px;
            height: 40px;
            stroke: white;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        h1 {
            color: #0057A6;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .certificate-number {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 24px;
            padding: 12px 24px;
            background: #f3f4f6;
            border-radius: 8px;
            display: inline-block;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }

        .message {
            color: #4b5563;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            margin: 0 auto 24px;
            border: 4px solid #e5e7eb;
            border-top-color: #0057A6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #0057A6 0%, #004280 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 87, 166, 0.3);
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 87, 166, 0.4);
        }

        .footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 32px 24px;
            }

            h1 {
                font-size: 24px;
            }

            .message {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <svg viewBox="0 0 200 80" xmlns="http://www.w3.org/2000/svg">
                <!-- Brand Colors and Academy Emblem -->
                <rect x="10" y="15" width="180" height="50" rx="5" fill="#0057A6"/>
                <text x="100" y="38" font-family="Arial, sans-serif" font-size="16" font-weight="bold" fill="#3cb4b0" text-anchor="middle">UPSKILLS ACADEMY</text>
                <text x="100" y="54" font-family="Arial, sans-serif" font-size="11" fill="white" text-anchor="middle">English Teaching Excellence</text>
            </svg>
        </div>

        <div class="icon">
            <svg viewBox="0 0 24 24">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path>
            </svg>
        </div>

        <h1>Certificate Found</h1>

        <div class="certificate-number">
            Certificate #{{ $serialnumber }}
        </div>

        <p class="message">
            You are being redirected to view your certificate on our secure certificate server.
            The page will open automatically in a moment...
        </p>

        <div class="spinner"></div>

        <a href="{{ $certificateUrl }}" class="button" target="_blank">
            View Certificate Now
        </a>

        <div class="footer">
            Upskills Academy Excellence<br>
            © {{ date('Y') }} All Rights Reserved
        </div>
    </div>

    <script>
        // Auto-redirect after 2 seconds
        setTimeout(function() {
            window.open('{{ $certificateUrl }}', '_blank');

            // Optional: redirect current page to home after opening certificate
            setTimeout(function() {
                window.location.href = '{{ url("/ar") }}';
            }, 1000);
        }, 2000);
    </script>
</body>
</html>
