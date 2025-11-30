<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "db.php";

echo "<h3>Connexion BDD OK</h3>";

// Vérifier nom de la base (optionnel)
try {
    $res = $pdo->query("SELECT DATABASE()")->fetchColumn();
    echo "Base utilisée : <strong>" . htmlspecialchars($res) . "</strong><br>";
} catch (Exception $e) {
    echo "Erreur PDO: " . $e->getMessage();
    exit;
}

// Compter les publications
$count = $pdo->query("SELECT COUNT(*) FROM publication")->fetchColumn();
echo "Nombre total de lignes dans publication : <strong>$count</strong><br>";

// Lister quelques lignes
$rows = $pdo->query("SELECT id, title, picture, is_published FROM publication ORDER BY id DESC LIMIT 10")->fetchAll();
echo "<pre>"; print_r($rows); echo "</pre>";
