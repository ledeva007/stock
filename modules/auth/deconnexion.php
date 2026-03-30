<?php
require_once __DIR__ . '/../../config/session.php';

/* Détruire la session */
session_unset();
session_destroy();

header("Location: /stock_app/modules/auth/connexion.php");
exit;
