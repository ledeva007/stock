<?php
require_once __DIR__ . '/../../config/connexion_bdd.php';
require_once __DIR__ . '/../../config/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user = trim($_POST['identifiant'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // si identifiant vide
    if (empty($user)) {
        $_SESSION['erreur'] = "Veuillez entrer votre identifiant.";
        header("Location: /app/modules/auth/login.php");
        exit;
    }

    // recherche utilisateur
    $stmt = $connexion->prepare("
        SELECT id_utilisateur, nom, prenom, mot_de_passe, role
        FROM utilisateurs
        WHERE identifiant = ?
    ");
    $stmt->execute([$user]);
    $utilisateur = $stmt->fetch();

    // verification
    if ($utilisateur && $mot_de_passe === $utilisateur['mot_de_passe']) {

        $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
        $_SESSION['nom'] = $utilisateur['nom'];
        $_SESSION['role'] = $utilisateur['role'];

        session_regenerate_id(true);

        // 🔥 REDIRECTION DIRECTE VERS LE DASHBOARD
        header("Location: /app/modules/tableau_bord/index.php");
        exit;

    } else {
        $_SESSION['erreur'] = "Identifiant ou mot de passe incorrect.";
        header("Location: /app/modules/auth/login.php");
        exit;
    }
}
