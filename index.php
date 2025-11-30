<?php
session_start();
require_once 'config.php';
require_once 'Publication.php';
require_once 'functions.php';

// Si on signale une publication
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signaler'])) {
    if (verifierToken($_POST['csrf_token'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE publication SET is_published = 0 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
}

// Récupérer les publications publiées
$sql = "SELECT * FROM publication WHERE is_published = 1 ORDER BY datetime DESC";
$stmt = $pdo->query($sql);
$publications = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pub = new Publication();
    $pub->setId($row['id']);
    $pub->setTitle($row['title']);
    $pub->setPicture($row['picture']);
    $pub->setDescription($row['description']);
    $pub->setDatetime($row['datetime']);
    $publications[] = $pub;
}

$token = genererToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
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
        .grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 20px; 
            margin-top: 20px; 
        }
        .card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            overflow: hidden; 
        }
        .card img { 
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
        }
        .card-body { 
            padding: 15px; 
        }
        .card h2 { 
            color: #7d8c5e; 
            font-size: 20px; 
            margin-bottom: 10px; 
        }
        .card p { 
            color: #666; 
            line-height: 1.5; 
        }
        .card-footer { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-top: 15px; 
            padding-top: 15px; 
            border-top: 1px solid #eee; 
        }
        .date { 
            color: #999; 
            font-size: 14px; 
        }
        .btn-danger { 
            background: #e74c3c; 
            color: white; 
            padding: 8px 15px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        .no-content { 
            text-align: center; 
            color: #999; 
            padding: 40px; 
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="active">Accueil</a>
        <a href="publier.php">Publier</a>
        <a href="admin.php">Admin</a>
    </nav>

    <div class="container">
        <h1>Publications</h1>

        <div class="grid">
            <?php if (empty($publications)): ?>
                <p class="no-content">Aucune publication.</p>
            <?php else: ?>
                <?php foreach ($publications as $pub): ?>
                    <div class="card">
                        <img src="uploads/<?php echo $pub->getPicture(); ?>" alt="">
                        <div class="card-body">
                            <h2><?php echo $pub->getTitle(); ?></h2>
                            <?php if ($pub->getDescription()): ?>
                                <p><?php echo $pub->getDescription(); ?></p>
                            <?php endif; ?>
                            <div class="card-footer">
                                <span class="date"><?php echo $pub->getDatetime(); ?></span>
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                                    <input type="hidden" name="id" value="<?php echo $pub->getId(); ?>">
                                    <button type="submit" name="signaler" class="btn-danger">Signaler</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>