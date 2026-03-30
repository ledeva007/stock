<?php
require_once __DIR__.'/../config/connexion_bdd.php';
header('Content-Type: application/json');

try{
    $prod  = (int)$_POST['produit'];
    $mag   = (int)$_POST['magasin'];
    $qte   = (int)$_POST['quantite'];

    // 1) incrémente le stock
    $connexion->prepare("UPDATE stock
                   SET quantite_actuelle = quantite_actuelle + ?
                   WHERE id_produit = ? AND id_magasin = ?")
        ->execute([$qte, $prod, $mag]);

    // 2) trace dans entrees_stock
    $connexion->prepare("INSERT INTO entrees_stock(id_produit,id_magasin,quantite,id_fournisseur)
                   VALUES(?,?,?,NULL)")   // fournisseur facultatif pour plus tard
        ->execute([$prod, $mag, $qte]);

    echo json_encode(['ok'=>true]);
}catch(Exception $e){
    http_response_code(500);
    echo json_encode(['ok'=>false,'msg'=>$e->getMessage()]);
}