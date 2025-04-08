<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/styleslogin.css') }}">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
</head>
<body>
    <div class="container">
        <div class="signin-signup">

            <div class="form-wrapper">
                <form action="{{ route('login') }}" method="POST" class="sign-in-form">
                    @csrf
                    
                    <h2 class="brand-name">JEMS</h2>
                    <h1 class="title">Login</h1>
                    <p class="subtitle">Hey there! Ready to get started?</p>

                    <label class="form-label" for="email">E-mail Address:</label>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="email" name="email" id="email" required />
                    </div>

                    <label class="form-label" for="password">Password:</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" required />
                    </div>

                    <div class="remember-me">
                        <label>
                            <input type="checkbox" name="remember"> Remember Me
                        </label>
                    </div>

                    <input type="submit" value="Login" class="btn" />
                </form>

            </div>

            <div class="right-panel">
                    <div class="image-overlay"></div>
                    <img src="/images/logo.webp" alt="Logo" class="panel-logo">
                    <div class="panel-text">
                        <h1>Welcome Back!</h1>
                        <p><br>Are you an admin? Login here instead.</p>

                        <a href="{{ route('admin.login') }}" class="btn admin-login-btn">Switch to Admin Login</a>
                    </div>
            </div>

        </div>
        <p class="footer-text">® DDDM</p>

    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
