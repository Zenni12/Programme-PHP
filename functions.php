<?php
// Générer un token CSRF
function genererToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifier le token CSRF
function verifierToken($token) {
    if (isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token']) {
        return true;
    }
    return false;
}

// Sécuriser les données
function securiser($data) {
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>