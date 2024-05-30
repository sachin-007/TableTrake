<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You Page</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('./food.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        #container {
            border: 5px solid white;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 5px;
        }

        #text {
            font-size: 48px;
            opacity: 0;
            animation: showText 4s forwards;
        }

        @keyframes showText {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div id="container">
        <center>
            <h1 id="text">
               
            </h1>
        </center>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textElement = document.getElementById('text');
            const text = 'Thank You ! Visit Again';
            let index = 0;

            function showText() {
                if (index < text.length) {
                    textElement.innerHTML += text.charAt(index);
                    index++;
                    setTimeout(showText, 100); // Delay between showing each letter
                }
            }

            showText();
        });
    </script>
</body>
</html>
