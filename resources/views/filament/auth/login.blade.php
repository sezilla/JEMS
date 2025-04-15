<div class="branding-wrapper">
    <div class="branding-logo">
        <img src="{{ asset('images/logo.webp') }}" alt="Logo">
    </div>
    <h3 class="slogan-text">We make every event</h3>
    <h1 class="slogan-highlight">UNFORGETTABLE</h1>
</div>


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
        margin-top: 40px;
        padding: 10px;
    }

    .branding-logo img {
        width: 300px;
        height: auto;
    }

    .slogan-text {
        font-size: 1.3em;
        color: white;
        margin: 10px 0 0;
        font-weight: 600;
        text-shadow: 2px 2px 5px black;
    }

    .slogan-highlight {
        font-size: 2em;
        color: #f86b84;
        margin: 0;
        font-weight: 900;
        text-shadow: 2px 2px 5px black;
    }

    @media screen and (min-width: 1024px) {
        .branding-wrapper {
            position: fixed;
            top: 30px;
            left: 100px;
            align-items: flex-start;
            text-align: left;
        }

        .branding-logo img {
            width: 380px;
        }

        .slogan-text {
            font-size: 3em;
        }

        .slogan-highlight {
            font-size: 4em;

        }

        main {
            position: absolute;
            right: 100px;
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
    }
</style>
