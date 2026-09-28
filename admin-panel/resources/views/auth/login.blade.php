<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Upskills Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #093162 0%, #081d36 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -250px;
            right: -250px;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            bottom: -200px;
            left: -200px;
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-left {
            background: linear-gradient(135deg, #093162 0%, #081d36 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -100px;
            left: -100px;
        }

        .logo-container {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-img {
            max-width: 200px;
            height: auto;
            margin-bottom: 1.5rem;
            filter: brightness(0) invert(1);
        }

        .logo-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .brand-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 300;
            margin-bottom: 2rem;
        }

        .features-list {
            list-style: none;
            padding: 0;
            text-align: left;
            position: relative;
            z-index: 2;
        }

        .features-list li {
            padding: 0.75rem 0;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            opacity: 0.95;
        }

        .features-list li i {
            margin-right: 1rem;
            font-size: 1.2rem;
            color: #3cb4b0;
        }

        .login-right {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: #093162;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #6c757d;
            font-size: 1rem;
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #093162;
            font-size: 1.1rem;
            z-index: 2;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.9rem 1rem 0.9rem 3rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            width: 100%;
            background: #f9fafb;
        }

        .form-control:focus {
            border-color: #093162;
            box-shadow: 0 0 0 4px rgba(0, 113, 174, 0.1);
            background: white;
            outline: none;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            margin-right: 0.5rem;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #093162;
            border-color: #093162;
        }

        .form-check-label {
            color: #6b7280;
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #093162 0%, #081d36 100%);
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 113, 174, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 113, 174, 0.4);
            background: linear-gradient(135deg, #081d36 0%, #081d36 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }

        .login-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .secure-badge {
            display: inline-flex;
            align-items: center;
            color: #059669;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .secure-badge i {
            margin-right: 0.5rem;
        }

        .copyright {
            color: #9ca3af;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .test-accounts {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f0f9ff;
            border-radius: 8px;
            border: 1px solid #bae6fd;
        }

        .test-accounts strong {
            color: #093162;
            display: block;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .test-accounts small {
            display: block;
            color: #6b7280;
            font-size: 0.8rem;
            line-height: 1.6;
        }

        @media (max-width: 968px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .login-left {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .login-right {
                padding: 2rem 1.5rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="logo-container">
                <div class="logo-icon">
                    <img src="/admin/app-assets/images/logo/logo-upskills-white.svg" alt="Upskills Academy" style="max-width: 220px; width: 100%;">
                </div>
                <h2 class="brand-title">Upskills Academy<br>Admin Panel</h2>
                <p class="brand-subtitle">English Teaching Management System</p>
            </div>

            <ul class="features-list">
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Secure Access Control</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Complete System Management</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Real-time Analytics</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Student & Course Control</span>
                </li>
            </ul>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Sign in to access the admin dashboard</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="admin@up-skills.academy"
                               required
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Enter your password"
                               required>
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Remember me for 30 days
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Sign In to Dashboard
                </button>
            </form>

            <div class="login-footer">
                <div class="secure-badge">
                    <i class="fas fa-shield-check"></i>
                    Secure Admin Access Only
                </div>
                <div class="copyright">
                    © {{ date('Y') }} Upskills Academy. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
