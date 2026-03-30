<?php
require_once __DIR__.'/../../config/session.php';
require_once __DIR__.'/../../config/connexion_bdd.php';

// ---------- actions ----------
if(isset($_POST['ajouter'])){
    $ref = $_POST['reference'];
    $nom = $_POST['nom'];
    $cat = (int)$_POST['categorie'];
    $uni = $_POST['unite'];
    $seuil = (int)$_POST['seuil'];
    $max   = (int)$_POST['capacite'];
    $connexion->prepare("INSERT INTO produits(reference_produit,nom_produit,id_categorie,unite,seuil_alerte,capacite_max)
                   VALUES(?,?,?,?,?,?)")
        ->execute([$ref,$nom,$cat,$uni,$seuil,$max]);
    header('Location: produits.php');exit;
}

if(isset($_POST['modifier'])){
    $id  = (int)$_POST['id'];
    $ref = $_POST['reference'];
    $nom = $_POST['nom'];
    $cat = (int)$_POST['categorie'];
    $uni = $_POST['unite'];
    $seuil = (int)$_POST['seuil'];
    $max   = (int)$_POST['capacite'];
    $connexion->prepare("UPDATE produits
                   SET reference_produit=?,nom_produit=?,id_categorie=?,unite=?,seuil_alerte=?,capacite_max=?
                   WHERE id_produit=?")
        ->execute([$ref,$nom,$cat,$uni,$seuil,$max,$id]);
    header('Location: produits.php');exit;
}

if(isset($_GET['suppr'])){
    $id=(int)$_GET['suppr'];
    $connexion->prepare("DELETE FROM produits WHERE id_produit=?")->execute([$id]);
    header('Location: produits.php');exit;
}

// ---------- données ----------
$categories = $connexion->query("SELECT * FROM categories_produits ORDER BY nom_categorie")->fetchAll();
$produits   = $connexion->query("SELECT p.*,c.nom_categorie
                           FROM produits p
                           JOIN categories_produits c ON c.id_categorie=p.id_categorie
                           ORDER BY p.nom_produit")->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Produits - BABACIMMO</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
  :root{--c1:#0b3d91;--c2:#0066ff;--c3:#f5f7fa;--c4:#fff;--text:#222;--text-light:#6c757d;--radius:10px;--shadow:0 2px 8px rgba(0,0,0,.07);}
  *{box-sizing:border-box;margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;}
  body{background:var(--c3);color:var(--text);}
  .topbar{display:flex;justify-content:space-between;align-items:center;padding:18px 24px;background:var(--c4);box-shadow:var(--shadow);}
  .btn{background:var(--c1);color:#fff;padding:8px 14px;border-radius:var(--radius);border:none;font-size:14px;cursor:pointer;}
  .btn:hover{opacity:.9;}
  .table-wrapper{background:var(--c4);margin:24px;border-radius:var(--radius);box-shadow:var(--shadow);overflow:auto;}
  table{width:100%;border-collapse:none;font-size:15px;}
  th,td{padding:12px 14px;text-align:left;border-bottom:1px solid #e9ecef;}
  th{background:#f8f9fa;color:var(--text-light);font-weight:600;text-transform:uppercase;font-size:12px;}
  tbody tr:hover{background:#f8f9fa;}
  .modal{position:fixed;inset:0;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;z-index:999;}
  .modal-content{background:var(--c4);padding:24px;border-radius:var(--radius);width:90%;max-width:480px;}
  .modal-content h2{margin-bottom:16px;font-size:20px;}
  .modal-content label{display:block;margin-bottom:4px;font-weight:600;font-size:14px;}
  .modal-content input,.modal-content select{width:100%;padding:8px 10px;margin-bottom:14px;border:1px solid #ccc;border-radius:var(--radius);}
  .modal-content .actions{text-align:right;margin-top:10px;}
  .hide{display:none;}
  </style>
</head>
<body>

<div class="topbar">
  <h1><i class="fa fa-box"></i> Gestion des produits</h1>
  <div>
    <a href="/app/index.php" class="btn"><i class="fa fa-arrow-left"></i> Retour</a>
    <button class="btn" onclick="openModal()"><i class="fa fa-plus"></i> Nouveau</button>
  </div>
</div>

<div class="table-wrapper">
  <table>
    <thead>
      <tr>
        <th>Référence</th><th>Nom</th><th>Catégorie</th><th>Unité</th>
        <th>Seuil</th><th>Capacité max</th><th width="110"></th>
      </tr>
    </thead>
    <tbody>
      <?php if(!$produits): ?>
        <tr><td colspan="7" style="text-align:center;color:#999">Aucun produit</td></tr>
      <?php else: ?>
        <?php foreach($produits as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['reference_produit']) ?></td>
            <td><?= htmlspecialchars($p['nom_produit']) ?></td>
            <td><?= htmlspecialchars($p['nom_categorie']) ?></td>
            <td><?= $p['unite'] ?></td>
            <td><?= $p['seuil_alerte'] ?></td>
            <td><?= $p['capacite_max'] ?></td>
            <td>
              <a href="?edit=<?= $p['id_produit'] ?>" class="btn" style="background:#f59f00;"><i class="fa fa-pen"></i></a>
              <a href="?suppr=<?= $p['id_produit'] ?>" class="btn" style="background:#e03131;"
                 onclick="return confirm('Supprimer ce produit ?')"><i class="fa fa-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- ===== MODALE ===== -->
<div id="modal" class="modal <?= isset($_GET['edit'])?'':'hide' ?>">
  <div class="modal-content">
    <?php
      // édition
      if(isset($_GET['edit'])){
          $edit = $pdo->prepare("SELECT * FROM produits WHERE id_produit=?");
          $edit->execute([(int)$_GET['edit']]);
          $e = $edit->fetch();
      }
    ?>
    <h2><?= isset($e)?'Modifier':'Nouveau' ?> produit</h2>
    <form method="post">
      <?php if(isset($e)): ?>
        <input type="hidden" name="id" value="<?= $e['id_produit'] ?>">
      <?php endif; ?>

      <label>Référence</label>
      <input type="text" name="reference" required value="<?= $e['reference_produit']??'' ?>">

      <label>Nom</label>
      <input type="text" name="nom" required value="<?= $e['nom_produit']??'' ?>">

      <label>Catégorie</label>
      <select name="categorie" required>
        <?php foreach($categories as $c): ?>
          <option value="<?= $c['id_categorie'] ?>"
             <?= (isset($e)&&$e['id_categorie']==$c['id_categorie'])?'selected':'' ?>>
             <?= $c['nom_categorie'] ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Unité</label>
      <input type="text" name="unite" required value="<?= $e['unite']??'' ?>"
             placeholder="ex : m², pcs, kg">

      <label>Seuil d'alerte</label>
      <input type="number" min="0" name="seuil" required value="<?= $e['seuil_alerte']??0 ?>">

      <label>Capacité max</label>
      <input type="number" min="0" name="capacite" required value="<?= $e['capacite_max']??0 ?>">

      <div class="actions">
        <button type="button" class="btn" onclick="closeModal()">Annuler</button>
        <button class="btn"><?= isset($e)?'Modifier':'Ajouter' ?></button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(){document.getElementById('modal').classList.remove('hide')}
function closeModal(){document.getElementById('modal').classList.add('hide')}
</script>
</body>
</html>