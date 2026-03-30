<?php
/* =========================================
   FICHIER : connexion_bdd.php
   RÔLE    : Connexion à la base de données
========================================= */

$serveur = "localhost";
$base_de_donnees = "gestion_stock";
$utilisateur = "root";
$mot_de_passe = ""; // à modifier si besoin

try {
    $connexion = new PDO(
        "mysql:host=$serveur;dbname=$base_de_donnees;charset=utf8mb4",
        $utilisateur,
        $mot_de_passe,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $erreur) {
    die("Erreur de connexion à la base de données : " . $erreur->getMessage());
}
