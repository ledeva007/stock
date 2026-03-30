<?php
require_once __DIR__.'/../../config/session.php';
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: /app/modules/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - BABACIMMO</title>

  <!-- ======  Fonts & icônes  ====== -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
  :root{
      --c1:#0b3d91; --c2:#0066ff; --c3:#f5f7fa; --c4:#ffffff;
      --text:#222; --text-light:#6c757d; --radius:14px; --shadow:0 4px 18px rgba(0,0,0,.08);
  }
  *{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif;}
  body{
     background: linear-gradient(rgba(245,247,250,.85), rgba(245,247,250,.85)),
                linear-gradient(160deg, #0b3d91 0%, #0066ff 45%, #43e97b 100%);
    background-attachment: fixed;
}

  /* ----------  SIDEBAR  ---------- */
  .sidebar{
      position:fixed;
      top:0;left:0;
      width:220px;height:100vh;
      background:var(--c1);
      display:flex;flex-direction:column;
      padding:28px 20px;
      transition:.3s;
  }
  .sidebar .logo{
      font-size:22px;font-weight:700;color:#fff;margin-bottom:40px;text-align:center;letter-spacing:.5px;
  }
  .sidebar a{
      display:flex;align-items:center;
      padding:12px 16px;margin-bottom:10px;
      border-radius:var(--radius);
      color:#fff;text-decoration:none;font-weight:500;transition:.25s;
  }
  .sidebar a:hover{background:rgba(255,255,255,.12);}
  .sidebar a i{width:22px;margin-right:14px;font-size:18px;text-align:center;}
  .sidebar .logout{margin-top:auto;background:rgba(255,255,255,.15);}
  .sidebar .logout:hover{background:rgba(255,255,255,.25);}

  /* ----------  MAIN  ---------- */
  .main{margin-left:220px;padding:30px;}
  .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;}
  .header h1{font-size:28px;font-weight:700;}
  .header .user{font-weight:500;color:var(--text-light);}
  .breadcrumb{font-size:14px;color:var(--text-light);margin-bottom:24px;}

  /* ----------  CARDS  ---------- */
  .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:24px;margin-bottom:40px;}
  .card{
      background:var(--c4);padding:24px;border-radius:var(--radius);box-shadow:var(--shadow);
      display:flex;align-items:center;gap:20px;transition:.25s;
  }
  .card:hover{transform:translateY(-4px);}
  .card .icon{
      width:60px;height:60px;border-radius:50%;display:grid;place-items:center;font-size:24px;color:#fff;
  }
  .card:nth-child(1) .icon{background:linear-gradient(135deg,#667eea,#764ba2);}
  .card:nth-child(2) .icon{background:linear-gradient(135deg,#f093fb,#f5576c);}
  .card:nth-child(3) .icon{background:linear-gradient(135deg,#4facfe,#00f2fe);}
  .card:nth-child(4) .icon{background:linear-gradient(135deg,#43e97b,#38f9d7);}
  .card div span{display:block;font-size:14px;color:var(--text-light);}
  .card div p{font-size:32px;font-weight:700;margin-top:4px;}

  /* ----------  TABLE  ---------- */
  .table-wrapper{background:var(--c4);border-radius:var(--radius);box-shadow:var(--shadow);padding:24px;margin-bottom:30px;}
  .table-wrapper h2{margin-bottom:20px;font-size:20px;font-weight:600;}
  table{width:100%;border-collapse:collapse;font-size:15px;}
  th,td{padding:14px 12px;text-align:left;border-bottom:1px solid #e9ecef;}
  th{color:var(--text-light);font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.5px;}
  tbody tr:hover{background:#f8f9fa;}
  .badge{font-size:12px;padding:4px 10px;border-radius:20px;font-weight:500;}
  .badge.success{background:#d3f9d0;color:#2b8a3e;}
  .badge.warning{background:#ffe8cc;color:#e67700;}

  /* ----------  CHART  ---------- */
  .chart-box{background:var(--c4);border-radius:var(--radius);box-shadow:var(--shadow);padding:24px;height:260px;display:flex;align-items:center;justify-content:center;color:var(--text-light);}
  /* simple bar chart mockup */
  .bars{display:flex;align-items:flex-end;gap:10px;height:160px;}
  .bars div{width:26px;background:var(--c2);border-radius:4px 4px 0 0;}
  .bars div:nth-child(1){height:40%;}
  .bars div:nth-child(2){height:60%;}
  .bars div:nth-child(3){height:75%;}
  .bars div:nth-child(4){height:50%;}
  .bars div:nth-child(5){height:90%;}
  .bars div:nth-child(6){height:65%;background:#adb5bd;}

  /* ----------  RESPONSIVE  ---------- */
  @media(max-width:992px){
      .sidebar{transform:translateX(-100%);}
      .main{margin-left:0;}
  }
  @media(max-width:600px){
      .cards{grid-template-columns:1fr;}
      .header{flex-direction:column;align-items:flex-start;gap:12px;}
  }
  </style>
</head>
<body>

<!-- ==========  SIDEBAR  ========== -->
<nav class="sidebar">
    <div class="logo">BABACIMMO</div>
    <a href="/app/modules/tableau_bord/index.php"><i class="fa fa-th-large"></i>Dashboard</a>
    <a href="/app/modules/stock/stock.php"><i class="fa fa-boxes-stacked"></i>Stock</a>
    <a href="/app/modules/entrees/entrees.php"><i class="fa fa-arrow-down"></i>Entrées</a>
    <a href="/app/modules/sorties/sorties.php"><i class="fa fa-arrow-up"></i>Sorties</a>
    <a href="/app/modules/inventaire/inventaire.php"><i class="fa fa-clipboard-list"></i>Inventaire</a>
    <a href="/app/modules/commandes/commandes.php"><i class="fa fa-receipt"></i>Commandes</a>
    <a href="/app/modules/fournisseurs/fournisseurs.php"><i class="fa fa-truck-field"></i>Fournisseurs</a>
    <a href="/app/modules/auth/deconnexion.php" class="logout"><i class="fa fa-right-from-bracket"></i>Déconnexion</a>
</nav>

<!-- ==========  MAIN  ========== -->
<main class="main">
    <div class="header">
        <div>
            <h1>Tableau de bord</h1>
            <div class="breadcrumb">Accueil / Dashboard</div>
        </div>
        <div class="user">Bonjour, <strong><?= htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur'); ?></strong></div>
    </div>

    <!-- 4 cards -->
    <div class="cards">
        <div class="card">
            <div class="icon"><i class="fa fa-boxes-stacked"></i></div>
            <div>
                <span>Stock total</span>
                <p id="stock_total">--</p>
            </div>
        </div>
        <div class="card">
            <div class="icon"><i class="fa fa-triangle-exclamation"></i></div>
            <div>
                <span>Ruptures</span>
                <p id="ruptures">--</p>
            </div>
        </div>
        <div class="card">
            <div class="icon"><i class="fa fa-arrow-down"></i></div>
            <div>
                <span>Entrées du jour</span>
                <p id="entrees_jour">--</p>
            </div>
        </div>
        <div class="card">
            <div class="icon"><i class="fa fa-arrow-up"></i></div>
            <div>
                <span>Sorties du jour</span>
                <p id="sorties_jour">--</p>
            </div>
        </div>
    </div>

    <!-- 2 tables -->
    <div class="table-wrapper">
        <h2>Dernières sorties</h2>
        <table id="table_sorties">
            <thead>
                <tr><th>Date</th><th>Produit</th><th>Qté</th><th>Magasin</th><th>Statut</th></tr>
            </thead>
            <tbody>
                <tr><td colspan="5" style="text-align:center;color:#999">Aucune donnée</td></tr>
            </tbody>
        </table>
    </div>

    <div class="table-wrapper">
        <h2>Dernières entrées</h2>
        <table id="table_entrees">
            <thead>
                <tr><th>Date</th><th>Produit</th><th>Qté</th><th>Fournisseur</th><th>Statut</th></tr>
            </thead>
            <tbody>
                <tr><td colspan="5" style="text-align:center;color:#999">Aucune donnée</td></tr>
            </tbody>
        </table>
    </div>

    <!-- mini graph -->
    <div class="table-wrapper">
        <h2>Mouvements du mois</h2>
        <div class="chart-box">
            <div class="bars">
                <div title="Sem 1"></div><div title="Sem 2"></div><div title="Sem 3"></div>
                <div title="Sem 4"></div><div title="Sem 5"></div><div title="Sem 6"></div>
            </div>
        </div>
    </div>
</main>

<!-- ==========  JS (AJAX)  ========== -->
<script>
/* Exemple rapide pour remplir les cartes */
fetch('/api/dashboard.php')
  .then(r=>r.json())
  .then(d=>{
      document.getElementById('stock_total').textContent   = d.stock_total;
      document.getElementById('ruptures').textContent      = d.ruptures;
      document.getElementById('entrees_jour').textContent  = d.entrees_jour;
      document.getElementById('sorties_jour').textContent  = d.sorties_jour;
  });
</script>
<script>
fetch('/app/api/dashboard.php')
  .then(r=>r.json())
  .then(d=>{
      document.getElementById('stock_total').textContent   = d.stock_total;
      document.getElementById('ruptures').textContent      = d.ruptures;
      document.getElementById('entrees_jour').textContent  = d.entrees_jour;
      document.getElementById('sorties_jour').textContent  = d.sorties_jour;

      // table entrées
      const te = document.querySelector('#table_entrees tbody');
      te.innerHTML = d.last_entrees.map(r=>`
        <tr>
          <td>${r.date_entree}</td>
          <td>${r.nom_produit}</td>
          <td>${r.quantite}</td>
          <td>${r.nom_fournisseur||'-'}</td>
        </tr>`).join('');

      // table sorties
      const ts = document.querySelector('#table_sorties tbody');
      ts.innerHTML = d.last_sorties.map(r=>`
        <tr>
          <td>${r.date_sortie}</td>
          <td>${r.nom_produit}</td>
          <td>${r.quantite}</td>
          <td>${r.nom_magasin}</td>
        </tr>`).join('');
  });
  </script>
</body>
</html>