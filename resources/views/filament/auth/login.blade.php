<div class="branding-wrapper">
    <div class="branding-logo">
        <img src="{{ asset('images/logo.webp') }}" alt="Logo">
    </div>
    <div class="login-title-responsive">
        {{ filament()->getId() === 'admin' ? 'Welcome, Admin!' : 'Welcome, Employee!' }}
    </div>
    <h3 class="slogan-text">We make every event</h3>
    <h1 class="slogan-highlight">UNFORGETTABLE</h1>
    
</div>

<a href="{{ url('/') }}" class="back-btn">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="14" fill="none" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 6px;"><path d="M15 19l-7-7 7-7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Back
</a>

<style>
    body {
        background: linear-gradient(-190deg, rgba(255, 255, 255, 0.7), rgba(248, 107, 132, 0.7)),
        url("{{ asset('images/loginimg.jpg') }}")no-repeat center center fixed !important;
        background-size: cover !important;
        margin: 0;
        font-family: 'Poppins', sans-serif;
    }

    .branding-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-top: clamp(20px, 4vw, 40px);
        padding: clamp(10px, 2vw, 20px);
        width: 100%;
        box-sizing: border-box;
    }

    .branding-logo img {
        width: clamp(200px, 30vw, 300px);
        height: auto;
    }

    .slogan-text {
        font-size: clamp(1.1rem, 2.5vw, 1.3rem);
        color: white;
        margin: clamp(8px, 1.5vw, 10px) 0 0;
        font-weight: 600;
        text-shadow: 2px 2px 5px black;
        transition: color 0.2s ease;
    }

    .slogan-highlight {
        font-size: clamp(1.5rem, 3vw, 2rem);
        color: #f86b84;
        margin: 0;
        font-weight: 900;
        text-shadow: 2px 2px 5px black;
        line-height: 1.2;
    }

    @media screen and (min-width: 1024px) {
        .branding-wrapper {
            position: fixed;
            top: 30px;
            left: clamp(30px, 8vw, 100px);
            align-items: flex-start;
            text-align: left;
        }

        .branding-logo img {
            width: clamp(300px, 25vw, 380px);
        }

        .slogan-text {
            font-size: clamp(1.8rem, 2.5vw, 2.2rem);
        }

        .slogan-highlight {
            font-size: clamp(2rem, 3vw, 2.8rem);
        }

        main {
            position: absolute;
            right: clamp(30px, 8vw, 100px);
            width: min(90%, 450px);
        }

        main:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #f86b84 !important;
            border-radius: 12px;
            z-index: -9;
            transform: rotate(7deg);
        }

        .login-title-responsive, .slogan-text {
            color: white;
        }
    }

    @media screen and (max-width: 1023px) {
        main {
            width: min(90%, 400px);
            margin: 0 auto;
            padding: clamp(15px, 3vw, 20px);
            box-sizing: border-box;
        }

        .login-title-responsive {
            color: #e84e67;
        }
        
        .slogan-text {
            color: #f86b84;
        }
    }

    @media screen and (max-width: 768px) {
        .slogan-text {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: #f86b84;
        }

        .slogan-highlight {
            font-size: clamp(1.3rem, 2.5vw, 1.8rem);
        }

        .login-title-responsive {
            color: #e84e67;
        }
    }

    @media screen and (max-width: 480px) {
        .slogan-text {
            font-size: clamp(0.9rem, 1.8vw, 1.1rem);
            color: #f86b84;
        }

        .slogan-highlight {
            font-size: clamp(1.1rem, 2vw, 1.5rem);
        }

        .login-title-responsive {
            color: #e84e67;
        }
    }

    .back-btn {
        position: fixed;
        top: clamp(10px, 2vh, 20px);
        left: clamp(10px, 2vw, 20px);
        display: flex;
        align-items: center;
        background: #f86b84;
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: clamp(0.4em, 1vw, 0.7em) clamp(1em, 2vw, 2em);
        font-size: clamp(0.875rem, 1.2vw, 1.1rem);
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
        z-index: 1000;
    }
    .back-btn:hover {
        background: #ff4f68;
        box-shadow: 0 6px 24px rgba(0,0,0,0.13);
        transform: translateY(-2px) scale(1.03);
    }

    .back-btn svg {
        width: clamp(14px, 1.2vw, 18px);
        height: auto;
        margin-right: clamp(4px, 0.5vw, 6px);
    }

    .login-title-responsive {
        color: #fff;
        font-size: clamp(1.1rem, 2.5vw, 1.5rem);
        font-weight: 700;
        margin-top: clamp(12px, 2vw, 18px);
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.4);
        text-align: center;
        padding: 0 clamp(5px, 1vw, 10px);
        transition: color 0.2s ease;
    }

    @media (max-width: 900px) {
        .login-title-responsive {
            font-size: 1.1em;
            margin-top: 12px;
        }
    }

    @media (max-height: 700px) {
        .branding-wrapper {
            margin-top: clamp(10px, 2vh, 20px);
        }
        .branding-logo img {
            width: clamp(160px, 20vw, 200px);
        }
        .slogan-text {
            font-size: clamp(0.9rem, 2vh, 1.1rem);
            margin: clamp(5px, 1vh, 8px) 0 0;
            color: #f86b84;
        }
        .slogan-highlight {
            font-size: clamp(1.1rem, 2.2vh, 1.5rem);
            line-height: 1.1;
        }
        .login-title-responsive {
            color: #e84e67;
        }
    }
</style>
