<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Splash Screen</title>
  <style>
    body, html {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #f8f9fa;
      overflow: hidden;
    }
    #splash {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      height: 100%;
      position: absolute;
      background-color: #ffffff;
      opacity: 1;
      transition: opacity 2s ease-in-out;
    }
    #splash.fade-out {
      opacity: 0;
    }
    #splash img {
      max-width: 50%;
      height: auto;
    }
  </style>
</head>
<body>
  <div id="splash">
    <img src="./static/img/humor_logo.png" alt="Splash Image">
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const splash = document.getElementById('splash');
      
      // Start fade-in
      setTimeout(() => {
        splash.classList.add('fade-in');
      }, 100); // Short delay to ensure transition is visible

      // Start fade-out after fade-in is complete
      setTimeout(() => {
        splash.classList.add('fade-out');
      }, 2100); // 2 seconds for fade-in, then start fade-out

      // Redirect after fade-out is complete
      setTimeout(() => {
        window.location.href = './public/index.php';
      }, 4100); // Total 4 seconds (2s fade-in + 2s display + 2s fade-out)
    });
  </script>
</body>
</html>

