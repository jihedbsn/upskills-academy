<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'أكاديمية أب سكيلز لتعليم الإنجليزية')</title>
    <style>
        /* ========================================
           UPSKILLS ACADEMY EMAIL DESIGN SYSTEM
           Professional & Trustworthy
           ======================================== */
        
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-align: right;
        }
        
        /* ========================================
           COLOR PALETTE
           ======================================== */
        :root {
            /* Primary Brand Colors */
            --primary-blue: #093162;
            --primary-blue-dark: #081d36;
            --primary-blue-light: #3379b8;
            
            /* Status Colors */
            --success-green: #059669;
            --success-green-light: #10b981;
            --warning-orange: #FCD116;
            --warning-orange-light: #fce055;
            --error-red: #dc2626;
            --error-red-light: #ef4444;
            --info-blue: #0284c7;
            
            /* Neutral Colors */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            /* Background Colors */
            --bg-success: #ecfdf5;
            --bg-warning: #fffbeb;
            --bg-error: #fef2f2;
            --bg-info: #eff9f9;
            --bg-neutral: #f9fafb;
        }
        
        /* ========================================
           LAYOUT STRUCTURE
           ======================================== */
        .email-wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e5e7eb;
            border-top: 4px solid #093162;
        }
        
        /* ========================================
           TOP BAR (LOGO)
           ======================================== */
        .email-top-bar {
            background-color: #ffffff;
            padding: 24px 40px;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
        }

        .top-bar-logo {
            max-width: 180px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* ========================================
           HEADER SECTION (HERO)
           ======================================== */
        .email-header {
            background-color: #ffffff;
            padding: 10px 40px 30px;
            text-align: center;
        }
        
        .header-subtitle {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 16px;
        }
        
        .header-title {
            font-size: 28px;
            font-weight: 800;
            margin: 0;
            color: #093162;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .header-accent {
            width: 60px;
            height: 4px;
            background-color: #3cb4b0;
            margin: 20px auto 0;
            border-radius: 2px;
        }
        
        /* ========================================
           CONTENT SECTION
           ======================================== */
        .email-content {
            padding: 48px 40px;
            background-color: #ffffff;
            text-align: right;
        }
        
        .greeting {
            font-size: 18px;
            color: #111827;
            margin-bottom: 24px;
            font-weight: 600;
        }
        
        .content-text {
            font-size: 16px;
            line-height: 1.7;
            color: #4b5563;
            margin-bottom: 20px;
        }
        
        .content-text strong {
            color: #1f2937;
            font-weight: 600;
        }
        
        /* ========================================
           INFORMATION CARDS
           ======================================== */
        .info-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            margin: 32px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            text-align: right;
        }
        
        .info-card.primary {
            background-color: #eff9f9;
            border-color: #bfdbfe;
            border-left: none;
            border-top: 4px solid #093162;
        }
        
        .info-card.success {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
            border-left: none;
            border-top: 4px solid #10b981;
        }
        
        .info-card.warning {
            background-color: #fffbeb;
            border-color: #fde68a;
            border-left: none;
            border-top: 4px solid #FCD116;
        }
        
        .info-card.error {
            background-color: #fef2f2;
            border-color: #fecaca;
            border-left: none;
            border-top: 4px solid #ef4444;
        }
        
        .info-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 13px;
        }
        
        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .info-list-item {
            padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-size: 15px;
            color: #374151;
            display: flex;
            justify-content: space-between;
        }
        
        .info-list-item:last-child {
            border-bottom: none;
        }
        
        .info-list-item strong {
            color: #1f2937;
            font-weight: 600;
        }
        
        /* ========================================
           BUTTONS & CALLS TO ACTION
           ======================================== */
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .button {
            display: inline-block;
            padding: 16px 36px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 6px rgba(0, 50, 100, 0.1);
        }
        
        .button-primary {
            background-color: #093162;
            color: #ffffff !important;
            border: 2px solid #093162;
        }
        .button-primary:visited, .button-primary:hover { color: #ffffff !important; }
        
        .button-primary:hover {
            background-color: #081d36;
            border-color: #081d36;
            box-shadow: 0 6px 12px rgba(0, 50, 100, 0.15);
            transform: translateY(-1px);
        }
        
        .button-success {
            background-color: #297a7b;
            color: #ffffff !important;
        }
        .button-success:visited, .button-success:hover { color: #ffffff !important; }
        
        .button-success:hover {
            background-color: #047857;
        }
        
        .button-outline {
            background-color: transparent;
            color: #093162;
            border: 2px solid #093162;
            box-shadow: none;
        }
        
        .button-outline:hover {
            background-color: #eff9f9;
            color: #081d36;
            border-color: #081d36;
        }
        
        /* ========================================
           DIVIDERS & SPACING
           ======================================== */
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 32px 0;
            border: none;
        }
        
        .spacer-small {
            height: 16px;
        }
        
        .spacer-medium {
            height: 24px;
        }
        
        .spacer-large {
            height: 32px;
        }
        
        /* ========================================
           FOOTER SECTION
           ======================================== */
        .email-footer {
            background-color: #f9fafb;
            padding: 32px 40px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        
        .footer-text {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 12px;
        }
        
        .footer-signature {
            font-size: 15px;
            color: #374151;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 8px;
        }
        
        .footer-company {
            font-size: 14px;
            color: #6b7280;
        }
        
        .footer-links {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-link {
            color: #093162;
            text-decoration: none;
            font-size: 13px;
            margin: 0 12px;
            font-weight: 500;
        }
        
        .footer-link:hover {
            text-decoration: underline;
            color: #081d36;
        }
        
        .copyright {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
        }
        
        /* ========================================
           UTILITY CLASSES
           ======================================== */
        .text-center {
            text-align: center;
        }
        
        .text-bold {
            font-weight: 600;
        }
        
        .text-muted {
            color: #6b7280;
        }
        
        .text-small {
            font-size: 14px;
        }
        
        /* ========================================
           RESPONSIVE DESIGN
           ======================================== */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 10px;
            }
            
            .email-container {
                border-radius: 8px;
            }
            
            .email-top-bar {
                padding: 20px;
            }
            
            .email-content {
                padding: 32px 20px;
            }
            
            .email-header {
                padding: 32px 20px;
            }
            
            .email-footer {
                padding: 32px 20px;
            }
            
            .header-title {
                font-size: 24px;
            }
            
            .button {
                padding: 14px 28px;
                font-size: 15px;
                width: 100%;
                box-sizing: border-box;
            }
            
            .info-list-item {
                flex-direction: column;
            }
            
            .info-list-item strong {
                margin-bottom: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Top Bar with Logo -->
            <div class="email-top-bar">
            </div>

            <!-- Header Hero Section -->
            <div class="email-header">
                <a href="{{ config('app.url') }}" style="display:inline-block; margin-bottom:14px;">
                    <img src="{{ isset($message) ? $message->embed(public_path('assets/img/general/logo-upskills-email.png')) : asset('assets/img/general/logo-upskills-email.png') }}" alt="Upskills Academy" width="170" style="width:170px; height:auto; border:0; display:inline-block;">
                </a>
                <div class="header-subtitle">التميز في التدريب الرياضي</div>
                <h1 class="header-title">@yield('email-title')</h1>
                <div class="header-accent"></div>
            </div>
            
            <!-- Content -->
            <div class="email-content">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="email-footer">
                <p class="footer-signature">مع أطيب التحيات،</p>
                <p class="footer-company">فريق أكاديمية أب سكيلز لتعليم الإنجليزية</p>
                
                <div class="footer-links">
                    <a href="{{ config('app.url') }}" class="footer-link">زيارة الموقع</a>
                    <a href="{{ config('app.url') }}/contact" class="footer-link">اتصل بالدعم</a>
                    <a href="{{ config('app.url') }}/help" class="footer-link">مركز المساعدة</a>
                </div>
                
                <p class="footer-text">
                    هذه رسالة آلية من أكاديمية أب سكيلز. يرجى عدم الرد مباشرة على هذا البريد الإلكتروني.
                </p>
                
                <p class="footer-text" style="margin-top: 16px;">
                    <strong style="color: #374151;">تواصل معنا:</strong> 
                    <a href="mailto:info@up-skills.academy" style="color: #093162; text-decoration: none; font-weight: 500;">info@up-skills.academy</a>
                </p>
                
                <p class="copyright">
                    &copy; {{ date('Y') }} أكاديمية أب سكيلز لتعليم الإنجليزية. جميع الحقوق محفوظة.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
