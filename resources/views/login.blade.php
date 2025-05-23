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
                    @if ($errors->has('email'))
                    <div class="form-error">
                        {{ $errors->first('email') }}
                    </div>  
                    @endif
                    <label class="form-label" for="email">E-mail Address:</label>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="email" name="email" id="email" placeholder="Enter your email" required />
                    </div>

                    <label class="form-label" for="password">Password:</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Enter your password" required />
                        <i class="fas fa-eye toggle-password" onclick="togglePassword()" style="cursor: pointer; margin-right: 15px;"></i>
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
                        <p><br>You're an admin? Login to the respective login page please!</p>
                        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="btn admin-login-btn">Go back</a>
                        </div>
            </div>

        </div>
        

    </div>

    <p class="footer-text">® DDDM</p>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        </script>

</body>
</html>
