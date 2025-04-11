<h3 id="slogan">Together we achieve exceptional goodness!</h3>

<style>
    body {
        background: rgb(34, 193, 195) !important;
        background: linear-gradient(0deg, rgba(34, 193, 195, 1) 0%, rgba(253, 187, 45, 1) 100%) !important;
        ;
    }

    @media screen and (min-width: 1024px) {
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
            background: darkcyan;
            border-radius: 12px;
            z-index: -9;

            /*box-shadow: -50px 80px 4px 10px #555;*/
            -webkit-transform: rotate(7deg);
            -moz-transform: rotate(7deg);
            -o-transform: rotate(7deg);
            -ms-transform: rotate(7deg);
            transform: rotate(7deg);
        }

        .fi-logo {
            position: fixed;
            left: 100px;
            font-size: 3em;
            color: cornsilk;
        }

        #slogan {
            position: fixed;
            left: 100px;
            margin-top: 50px;
            color: bisque;
            font-family: cursive;
            font-size: 2em;
            font-weight: bold;
            text-shadow: #3f6212 2px 2px 5px;
        }
    }
</style>
