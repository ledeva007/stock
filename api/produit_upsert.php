<?php
require_once __DIR__.'/../config/connexion_bdd.php';
header('Content-Type: application/json');

try {
    // 1) crée le produit
    $connexion->prepare(
    "INSERT INTO produits (reference_produit, nom_produit, id_categorie, unite, id_magasin_default)
     VALUES (?, ?, ?, ?, ?)")
  ->execute([$_POST['reference'], $_POST['nom'], $_POST['categorie'], $_POST['unite'], $_POST['magasin_default']]);

$idProduit = $connexion->lastInsertId();

// Crée UN SEUL stock pour le magasin choisi
$connexion->prepare("INSERT INTO stock (id_produit, id_magasin, quantite_actuelle) VALUES (?, ?, 0)")
    ->execute([$idProduit, $_POST['magasin_default']]);
    
    echo json_encode(['ok' => true]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}