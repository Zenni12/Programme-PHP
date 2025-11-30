<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

$erreur = '';
$succes = '';

// Si le formulaire est envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifierToken($_POST['csrf_token'])) {
        $title = securiser($_POST['title']);
        $description = securiser($_POST['description']);
        
        // Vérifier les champs
        if (empty($title)) {
            $erreur = "Le titre est obligatoire";
        } elseif (!isset($_FILES['picture']) || $_FILES['picture']['error'] !== 0) {
            $erreur = "L'image est obligatoire";
        } elseif (strpos($_FILES['picture']['type'], 'image/') !== 0) {
            $erreur = "Le fichier doit être une image";
        } else {
            // Enregistrer l'image
            $extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
            $nomImage = uniqid() . '.' . $extension;
            
            if (!is_dir('uploads')) {
                mkdir('uploads');
            }
            
            move_uploaded_file($_FILES['picture']['tmp_name'], 'uploads/' . $nomImage);
            
            // Enregistrer en base
            date_default_timezone_set('Europe/Paris');
            $datetime = date('Y-m-d H:i');
            
            $sql = "INSERT INTO publication (title, picture, description, datetime, is_published) 
                    VALUES (:title, :picture, :description, :datetime, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $title,
                ':picture' => $nomImage,
                ':description' => $description,
                ':datetime' => $datetime
            ]);
            
            $succes = "Publication créée !";
        }
    }
}

$token = genererToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publier</title>
    <style>
        body { 
            font-family: Arial; 
            margin: 0; 
            background: #f5f5f5; 
        }
        nav { 
            background: #7d8c5e; 
            padding: 15px; 
            text-align: center; 
        }
        nav a { 
            color: white; 
            text-decoration: none; 
            padding: 10px 20px; 
            margin: 0 5px; 
            border-radius: 5px; 
        }
        nav a:hover { 
            background: #6b7851; 
        }
        nav a.active { 
            background: #5d6b47; 
        }
        .container { 
            max-width: 600px; 
            margin: 30px auto; 
            padding: 20px; 
            background: white; 
            border-radius: 8px; 
        }
        h1 { 
            color: #7d8c5e; 
            text-align: center; 
        }
        .message { 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 5px; 
            text-align: center; 
        }
        .erreur { 
            background: #e74c3c; 
            color: white; 
        }
        .succes { 
            background: #2ecc71; 
            color: white; 
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold; 
            color: #7d8c5e; 
        }
        input[type="text"], 
        input[type="file"], 
        textarea { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
        }
        textarea { 
            resize: vertical; 
        }
        button { 
            background: #7d8c5e; 
            color: white; 
            padding: 12px; 
            border: none; 
            border-radius: 5px; 
            width: 100%; 
            font-size: 16px; 
            cursor: pointer; 
        }
        button:hover { 
            background: #6b7851; 
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="publier.php" class="active">Publier</a>
        <a href="admin.php">Admin</a>
    </nav>

    <div class="container">
        <h1>Créer une publication</h1>

        <?php if ($erreur): ?>
            <div class="message erreur"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <?php if ($succes): ?>
            <div class="message succes"><?php echo $succes; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

            <div class="form-group">
                <label>Titre *</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Image *</label>
                <input type="file" name="picture" accept="image/*" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5"></textarea>
            </div>

            <button type="submit">Publier</button>
        </form>
    </div>
</body>
</html>