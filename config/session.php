<?php
/* =========================================
   FICHIER : session.php
   RÔLE    : Gestion des sessions
========================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

