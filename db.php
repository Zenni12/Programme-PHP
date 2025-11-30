<?php
$host = "localhost";
$dbname = "tp_final";   
$user = "root";
$pass = "";


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch(Exception $e) {
    die("Erreur BDD : " . $e->getMessage());
}
