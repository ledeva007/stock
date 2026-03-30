<?php
require_once __DIR__ . '/../../config/session.php';
?>

<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion - BABACIMMO</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      height: 100vh;
      background: url('babacimmo1.png') center/cover no-repeat;
      background-size: 90.5%;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
    }

    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
      backdrop-filter: blur(0px);
    }

    .login-container {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.85);
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
      width: 100%;
      max-width: 400px;
      text-align: center;
      backdrop-filter: blur(6px);
      animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .logo {
      width: 60px;
      height: auto;
      margin: 0 auto 20px auto;
    }

    h2 {
      margin-bottom: 10px;
      color: #00bcd4;
    }

    .subtitle {
      font-size: 14px;
      color: #555;
      margin-bottom: 25px;
    }

    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #222;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    .btn {
      width: 100%;
      padding: 12px;
      background: #00bcd4;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #0097a7;
    }

    .footer {
      margin-top: 20px;
      font-size: 12px;
      color: #666;
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 30px 20px;
        border-radius: 12px;
      }

      .logo {
       width: 60px;
       height: 60px;
       object-fit: contain;
       background: transparent;
      }
    }
    .error-message {
    background: #ffe5e5;
    color: #b00020;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}
  </style>
</head>
<body>
  <div class="login-container">
    <img src="logo1.png" alt="Logo BABACIMMO" class="logo" />
    <h2>Connexion</h2>
    <p class="subtitle">Application de gestion de stock immobilier</p>
    <form action="connexion.php" method="post">
      <div class="form-group">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="identifiant" name="identifiant" required autocomplete="username" />
      </div>
      <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required autocomplete="current-password" />
      </div>
      <?php if (!empty($_SESSION['erreur'])): ?>
    <div class="error-message">
        <?= htmlspecialchars($_SESSION['erreur']); ?>
    </div>
    <?php unset($_SESSION['erreur']); ?>
<?php endif; ?>
      <button type="submit" class="btn">Se connecter</button>
    </form>
    <div class="footer">© 2026 BABACIMMO - Tous droits réservés</div>
  </div>
</body>
</html>