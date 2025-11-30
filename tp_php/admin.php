<?php
session_start();
require_once 'config.php';
require_once 'Publication.php';
require_once 'functions.php';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifierToken($_POST['csrf_token'])) {
        $id = (int)$_POST['id'];
        
        // Supprimer
        if (isset($_POST['supprimer'])) {
            $sql = "SELECT picture FROM publication WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $pub = $stmt->fetch();
            
            if ($pub) {
                $imagePath = 'uploads/' . $pub['picture'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                
                $sql = "DELETE FROM publication WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $id]);
            }
        }
        
        // Accepter
        if (isset($_POST['accepter'])) {
            $sql = "UPDATE publication SET is_published = 1 WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
        }
    }
}

// Récupérer toutes les publications
$sql = "SELECT * FROM publication ORDER BY datetime DESC";
$stmt = $pdo->query($sql);
$publications = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pub = new Publication();
    $pub->setId($row['id']);
    $pub->setTitle($row['title']);
    $pub->setPicture($row['picture']);
    $pub->setDescription($row['description']);
    $pub->setDatetime($row['datetime']);
    $pub->setIsPublished($row['is_published']);
    $publications[] = $pub;
}

$token = genererToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration</title>
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
            max-width: 1200px; 
            margin: 30px auto; 
            padding: 20px; 
            background: white; 
            border-radius: 8px; 
        }
        h1 { 
            color: #7d8c5e; 
            text-align: center; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        table thead { 
            background: #7d8c5e; 
            color: white; 
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        .img { 
            width: 60px; 
            height: 60px; 
            object-fit: cover; 
            border-radius: 5px; 
        }
        .badge { 
            padding: 5px 10px; 
            border-radius: 3px; 
            font-size: 12px; 
            color: white; 
        }
        .badge-success { 
            background: #2ecc71; 
        }
        .badge-warning { 
            background: #f39c12; 
        }
        .btn { 
            padding: 8px 15px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            color: white; 
            margin-right: 5px; 
        }
        .btn-success { 
            background: #2ecc71; 
        }
        .btn-danger { 
            background: #e74c3c; 
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="publier.php">Publier</a>
        <a href="admin.php" class="active">Admin</a>
    </nav>

    <div class="container">
        <h1>Administration</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publications as $pub): ?>
                    <tr>
                        <td><?php echo $pub->getId(); ?></td>
                        <td>
                            <img src="uploads/<?php echo $pub->getPicture(); ?>" class="img">
                        </td>
                        <td><?php echo $pub->getTitle(); ?></td>
                        <td><?php echo substr($pub->getDescription(), 0, 50); ?>...</td>
                        <td><?php echo $pub->getDatetime(); ?></td>
                        <td>
                            <?php if ($pub->getIsPublished()): ?>
                                <span class="badge badge-success">Publié</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Signalé</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$pub->getIsPublished()): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                    <input type="hidden" name="id" value="<?php echo $pub->getId(); ?>">
                                    <button type="submit" name="accepter" class="btn btn-success">Accepter</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                    <input type="hidden" name="id" value="<?php echo $pub->getId(); ?>">
                                    <button type="submit" name="supprimer" class="btn btn-danger" 
                                            onclick="return confirm('Supprimer ?')">Supprimer</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>