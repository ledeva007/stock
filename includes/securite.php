<?php
/* =========================================
   FICHIER : securite.php
   RÔLE    : Sécurité et contrôle d'accès
========================================= */

require_once __DIR__ . '/../config/session.php';

/* Vérifie si l'utilisateur est connecté */
function utilisateur_connecte() {
    return isset($_SESSION['id_utilisateur']);
}

/* Oblige la connexion */
function exiger_connexion() {
    if (!utilisateur_connecte()) {
        header("Location: /modules/auth/connexion.php");
        exit;
    }
}

/* Vérifie le rôle */
function exiger_role($roles_autorises = []) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles_autorises)) {
        die("Accès refusé : droits insuffisants.");
    }
}