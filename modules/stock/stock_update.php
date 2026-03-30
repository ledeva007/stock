<?php
require_once __DIR__.'/../../config/session.php';
require_once __DIR__.'/../../config/connexion_bdd.php';

$idProd = (int)$_POST['id_produit'];
$idMag  = (int)$_POST['id_magasin'];

// Mise à jour produit
$connexion->prepare("UPDATE produits SET reference_produit=?, nom_produit=? WHERE id_produit=?")
    ->execute([$_POST['reference'], $_POST['nom'], $idProd]);

// Mise à jour quantité stock
$connexion->prepare("UPDATE stock SET quantite_actuelle=? WHERE id_produit=? AND id_magasin=?")
    ->execute([$_POST['quantite'], $idProd, $idMag]);

header('Location: stock.php');