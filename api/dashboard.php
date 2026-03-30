<?php
require_once __DIR__.'/../../config/connexion_bdd.php';
header('Content-Type: application/json');

$out = [];

// 1) stock total
$out['stock_total'] = $pdo->query(
    "SELECT SUM(quantite_actuelle) FROM stock"
)->fetchColumn();

// 2) ruptures
$out['ruptures'] = $pdo->query(
    "SELECT COUNT(*) FROM vue_produits_indisponibles"
)->fetchColumn();

// 3) entrées du jour
$out['entrees_jour'] = $pdo->query(
    "SELECT SUM(quantite) FROM vue_entrees_du_jour"
)->fetchColumn() ?: 0;

// 4) sorties du jour
$out['sorties_jour'] = $pdo->query(
    "SELECT SUM(quantite) FROM vue_sorties_du_jour"
)->fetchColumn() ?: 0;

// 5) dernières entrées (5 lignes)
$out['last_entrees'] = $pdo->query(
    "SELECT * FROM vue_entrees_du_jour ORDER BY date_entree DESC LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

// 6) dernières sorties (5 lignes)
$out['last_sorties'] = $pdo->query(
    "SELECT * FROM vue_sorties_du_jour ORDER BY date_sortie DESC LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($out);