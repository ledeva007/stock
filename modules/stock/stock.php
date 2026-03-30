<?php
require_once __DIR__.'/../../config/session.php';
require_once __DIR__.'/../../config/connexion_bdd.php';
// SUPPRIMER
if(isset($_GET['delete'])){
    $idProd = (int)$_GET['delete'];
    $idMag  = (int)$_GET['mag'];
    $connexion->prepare("DELETE FROM stock WHERE id_produit=? AND id_magasin=?")->execute([$idProd, $idMag]);
    header('Location: stock.php'); exit;
}

// MODIFIER (redirection vers modale)
if(isset($_GET['edit'])){
    $idProd = (int)$_GET['edit'];
    $idMag  = (int)$_GET['mag'];
    $editData = $connexion->prepare("SELECT p.*, s.quantite_actuelle, m.nom_magasin 
                                     FROM produits p 
                                     JOIN stock s ON s.id_produit=p.id_produit 
                                     JOIN magasins m ON m.id_magasin=s.id_magasin 
                                     WHERE p.id_produit=? AND s.id_magasin=?");
    $editData->execute([$idProd, $idMag]);
    $edit = $editData->fetch();
}
$magasins = $connexion->query("SELECT id_magasin, nom_magasin FROM magasins ORDER BY nom_magasin")->fetchAll(PDO::FETCH_ASSOC);
$selMag   = $_GET['mag'] ?? '';
$search   = $_GET['search'] ?? '';

$sql = "SELECT * FROM vue_etat_stock WHERE 1";

if($selMag) $sql .= " AND id_magasin = ".(int)$selMag;
if($search) $sql .= " AND nom_produit LIKE ".$connexion->quote('%'.$search.'%');

$sql .= " ORDER BY nom_produit";
$data = $connexion->query($sql)->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Stock - BABACIMMO</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
  :root{--c1:#0b3d91;--c2:#0066ff;--c3:#f5f7fa;--c4:#fff;--text:#222;--text-light:#6c757d;--radius:12px;--shadow:0 2px 8px rgba(0,0,0,.07);}
  *{box-sizing:border-box;margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;}
  body{background:var(--c3);color:var(--text);line-height:1.5;}
  a{text-decoration:none;color:inherit;}
  .topbar{display:flex;justify-content:space-between;align-items:center;padding:18px 24px;background:var(--c4);box-shadow:var(--shadow);}
  .topbar h1{font-size:24px;font-weight:600;}
  .btn{background:var(--c1);color:#fff;padding:8px 16px;border-radius:var(--radius);border:none;font-size:14px;cursor:pointer;}
  .btn:hover{opacity:.9;}
  .filters{background:var(--c4);margin:24px;border-radius:var(--radius);box-shadow:var(--shadow);padding:18px;display:flex;gap:12px;align-items:center;}
  .filters select{padding:6px 10px;border:1px solid #ccc;border-radius:6px;}
  .table-wrapper{background:var(--c4);margin:0 24px 24px;border-radius:var(--radius);box-shadow:var(--shadow);overflow:auto;}
  table{width:100%;border-collapse:none;font-size:15px;}
  th,td{padding:12px 14px;text-align:left;border-bottom:1px solid #e9ecef;}
  th{background:#f8f9fa;color:var(--text-light);font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.5px;}
  tbody tr:hover{background:#f8f9fa;}
  .badge{display:inline-block;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:500;}
  .vert {background:#d3f9d0;color:#2b8a3e;}
  .orange{background:#ffe8cc;color:#e67700;}
  .rouge {background:#ffd6d3;color:#c92a2a;}
  .gris  {background:#e9ecef;color:#868e96;}
  @media print{.filters,.topbar a{display:none}}
  </style>
  <style>
.modal{position:fixed;inset:0;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;z-index:999;}
.modal-content{background:#fff;padding:24px;border-radius:12px;width:90%;max-width:480px;}
.modal-content h2{margin-bottom:16px;font-size:20px;}
.modal-content label{display:block;margin-bottom:4px;font-weight:600;font-size:14px;}
.modal-content input,.modal-content select{width:100%;padding:8px 10px;margin-bottom:14px;border:1px solid #ccc;border-radius:8px;}
.modal-content .actions{text-align:right;margin-top:10px;}
.hide{display:none;}
</style>
</head>
<body>

<div class="topbar">
  <h1><i class="fa fa-boxes-stacked"></i> État du stock</h1>
  <div>
    <a href="/app/modules/tableau_bord/index.php" class="btn"><i class="fa fa-arrow-left"></i> Retour</a>
    <button class="btn" onclick="window.print()"><i class="fa fa-print"></i> Imprimer</button> <button class="btn" onclick="openEntreeModal()">
  <i class="fa fa-arrow-down"></i> Entrée stock
</button>
    <button class="btn" onclick="openProduitModal()"><i class="fa fa-plus"></i> Nouveau produit</button>
  </div>
</div>

<!-- ===== FILTRE ===== -->
<div class="filters">
  <label>Magasin :</label>
  <select onchange="location='?mag='+this.value">
    <option value="">-- Tous --</option>
    <?php foreach($magasins as $m): ?>
      <option value="<?= $m['id_magasin'] ?>" <?= $selMag==$m['id_magasin']?'selected':'' ?>>
        <?= $m['nom_magasin'] ?>
      </option>
    <?php endforeach; ?>
  </select>

  <!-- Recherche instantanée -->
  <label style="margin-left:20px;">Recherche :</label>
  <input type="text" id="searchInput" placeholder="Tapez pour filtrer..." 
         style="padding:8px 14px;border:1px solid #ccc;border-radius:8px;width:250px;">
</div>

<!-- ===== TABLEAU ===== -->
<div class="table-wrapper">
  <table id="tableStock">
    <thead>
  <tr>
    <th>Produit</th>
    <th>Magasin</th>
    <th>Quantité</th>
    <th>État</th>
    <th>Actions</th>
  </tr>
</thead>
    <tbody>
  <?php if(!$data): ?>
    <tr><td colspan="5" style="text-align:center;color:#999">Aucune donnée</td></tr>
  <?php else: ?>
    <?php foreach($data as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['nom_produit']) ?></td>
        <td><?= htmlspecialchars($row['nom_magasin']) ?></td>
        <td><?= $row['quantite_actuelle'] ?></td>
        
        <!-- État seul -->
        <td>
          <span class="badge <?= $row['quantite_actuelle']==0 ? 'rouge':'vert' ?>">
            <?= $row['quantite_actuelle']==0 ? 'Rupture':'Disponible' ?>
          </span>
        </td>
        
        <!-- Actions à côté -->
        <td>
          <div style="display:flex;gap:8px;">
            <a href="?edit=<?= $row['id_produit'] ?>&mag=<?= $row['id_magasin'] ?>" class="btn" style="background:#f59f00;font-size:11px;padding:5px 10px;">
              <i class="fa fa-pen"></i>
            </a>
            <a href="#" onclick="openConfirmModal('<?= htmlspecialchars($row['nom_produit']) ?>', '?delete=<?= $row['id_produit'] ?>&mag=<?= $row['id_magasin'] ?>')" class="btn" style="background:#e03131;font-size:11px;padding:5px 10px;">
              <i class="fa fa-trash"></i>
            </a>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
</tbody>
  </table>
</div>
<!-- ===== MODALE PRODUIT ===== -->
<div id="produitModal" class="modal hide">
  <div class="modal-content">
    <h2>Nouveau produit</h2>
    <form id="formProduit">
      <input type="text" name="reference" required placeholder="Référence">
      <input type="text" name="nom" required placeholder="Nom">
      <select name="magasin_default" required>
        <option value="">-- Magasin par défaut --</option>
        <?php foreach($magasins as $m): ?>
        <option value="<?= $m['id_magasin'] ?>"><?= $m['nom_magasin'] ?></option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="unite" required placeholder="Unité (m², pcs, kg…)">
      <div class="actions">
        <button type="button" class="btn" onclick="closeProduitModal()">Annuler</button>
        <button class="btn">Ajouter</button>
      </div>
    </form>
  </div>
</div>

<script>
function openProduitModal(){document.getElementById('produitModal').classList.remove('hide')}
function closeProduitModal(){document.getElementById('produitModal').classList.add('hide')}

document.getElementById('formProduit').addEventListener('submit',e=>{
  e.preventDefault();
  fetch('/app/api/produit_upsert.php',{method:'POST',body:new FormData(e.target)})
    .then(r=>r.json())
    .then(res=>{
       if(res.ok){ location.reload(); }   // recharge le tableau stock
       else{ alert(res.msg||'Erreur'); }
    });
});
</script>
<!-- MODALE ENTREE -->
<div id="entreeModal" class="modal hide">
  <div class="modal-content">
    <h2>Entrée en stock</h2>
    <form id="formEntree">
      <select name="produit" required>
        <option value="">-- Produit --</option>
        <?php
        $prod = $connexion->query("SELECT id_produit, nom_produit FROM produits ORDER BY nom_produit")->fetchAll();
        foreach($prod as $p) echo '<option value="'.$p['id_produit'].'">'.$p['nom_produit'].'</option>';
        ?>
      </select>

      <select name="magasin" required>
        <option value="">-- Magasin --</option>
        <?php
        $mag = $connexion->query("SELECT id_magasin, nom_magasin FROM magasins ORDER BY nom_magasin")->fetchAll();
        foreach($mag as $m) echo '<option value="'.$m['id_magasin'].'">'.$m['nom_magasin'].'</option>';
        ?>
      </select>

      <input type="number" name="quantite" min="1" required placeholder="Quantité">

      <div class="actions">
        <button type="button" class="btn" onclick="closeEntreeModal()">Annuler</button>
        <button class="btn">Valider</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEntreeModal(){document.getElementById('entreeModal').classList.remove('hide')}
function closeEntreeModal(){document.getElementById('entreeModal').classList.add('hide')}

document.getElementById('formEntree').addEventListener('submit',e=>{
  e.preventDefault();
  fetch('/app/api/entree_upsert.php',{method:'POST',body:new FormData(e.target)})
    .then(r=>r.json())
    .then(res=>{
       if(res.ok){ location.reload(); }
       else{ alert(res.msg||'Erreur'); }
    });
});
</script>
<?php if(isset($edit)): ?>
<div id="editModal" class="modal">
  <div class="modal-content">
    <h2>Modifier <?= htmlspecialchars($edit['nom_produit']) ?></h2>
    <form method="post" action="stock_update.php">
      <input type="hidden" name="id_produit" value="<?= $edit['id_produit'] ?>">
      <input type="hidden" name="id_magasin" value="<?= $idMag ?>">
      
      <label>Référence</label>
      <input type="text" name="reference" value="<?= $edit['reference_produit'] ?>" required>
      
      <label>Nom</label>
      <input type="text" name="nom" value="<?= $edit['nom_produit'] ?>" required>
      
      <label>Quantité actuelle</label>
      <input type="number" name="quantite" value="<?= $edit['quantite_actuelle'] ?>" min="0" required>
      
      <div class="actions">
        <a href="stock.php" class="btn" style="background:#6c757d;">Annuler</a>
        <button class="btn">Enregistrer</button>
      </div>
    </form>
  </div>
</div>
<script>document.getElementById('editModal').classList.remove('hide');</script>
<?php endif; ?>
<!-- MODALE CONFIRMATION SUPPRESSION -->
<div id="confirmModal" class="modal hide">
  <div class="modal-content" style="max-width:400px;text-align:center;">
    <i class="fa fa-triangle-exclamation" style="font-size:48px;color:#e03131;margin-bottom:16px;"></i>
    <h2 style="margin-bottom:12px;">Confirmer la suppression</h2>
    <p style="color:#6c757d;margin-bottom:24px;">
      Êtes-vous sûr de vouloir supprimer <strong id="nomProduitSuppr"></strong> du stock ?
      <br>Cette action est irréversible.
    </p>
    <div style="display:flex;gap:12px;justify-content:center;">
      <button class="btn" onclick="closeConfirmModal()" style="background:#6c757d;">Non, annuler</button>
      <a id="btnConfirmerSuppr" href="#" class="btn" style="background:#e03131;">Oui, supprimer</a>
    </div>
  </div>
</div>
<script>
function openConfirmModal(nomProduit, urlSuppr){
  document.getElementById('nomProduitSuppr').textContent = nomProduit;
  document.getElementById('btnConfirmerSuppr').href = urlSuppr;
  document.getElementById('confirmModal').classList.remove('hide');
}

function closeConfirmModal(){
  document.getElementById('confirmModal').classList.add('hide');
}
</script>
<script>
document.getElementById('searchInput').addEventListener('input', function(){
  const filtre = this.value.toLowerCase();
  const lignes = document.querySelectorAll('#tableStock tbody tr');
  
  lignes.forEach(ligne => {
    const nomProduit = ligne.cells[0].textContent.toLowerCase(); // 1ère colonne = Produit
    if(nomProduit.includes(filtre)){
      ligne.style.display = '';
    } else {
      ligne.style.display = 'none';
    }
  });
});
</script>
</body>
</html>