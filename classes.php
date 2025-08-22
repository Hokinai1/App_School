<?php
session_start();
require_once './includes/db.php';

$role = $_SESSION['user_role'] ?? 'user';

// Gestion des messages
function setMessage($type, $msg) {
    $_SESSION[$type] = $msg;
}

// Traitement Ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_classe'])) {
    $nom_classe = trim($_POST['nom_classe']);
    if ($nom_classe === '') {
        setMessage('error', 'Veuillez entrer un nom de classe.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO classe (nom_classe) VALUES (?)");
        $res = $stmt->execute([$nom_classe]);
        setMessage($res ? 'success' : 'error', $res ? 'Classe ajoutée avec succès.' : 'Erreur lors de l\'ajout.');
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Traitement Modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_classe'])) {
    $id = intval($_POST['id']);
    $nom_classe = trim($_POST['nom_classe']);
    if ($nom_classe === '') {
        setMessage('error', 'Veuillez entrer un nom de classe.');
    } else {
        $stmt = $pdo->prepare("UPDATE classe SET nom_classe=? WHERE id=?");
        $res = $stmt->execute([$nom_classe, $id]);
        setMessage($res ? 'success' : 'error', $res ? 'Classe modifiée avec succès.' : 'Erreur lors de la modification.');
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Suppression
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Vérifier si des élèves utilisent cette classe
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) as nb FROM eleves WHERE classe=?");
    $stmtCheck->execute([$id]);
    $nb = $stmtCheck->fetch()['nb'];

    if ($nb > 0) {
        setMessage('error', 'Impossible de supprimer cette classe : elle est utilisée par des élèves.');
    } else {
        $stmt = $pdo->prepare("DELETE FROM classe WHERE id=?");
        $res = $stmt->execute([$id]);
        setMessage($res ? 'success' : 'error', $res ? 'Classe supprimée avec succès.' : 'Erreur lors de la suppression.');
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Récupérer toutes les classes
$classes = $pdo->query("SELECT * FROM classe ORDER BY nom_classe ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./css/style.css">
<link rel="stylesheet" href="./css/enseignants.css">
<link rel="shortcut icon" href="./assets/icons/icon.png" type="image/x-icon">

</head>
<body>

<?php
    $currentPage = 'eleves';
    // on definit le titre a afficher dans l'onglet
    $titre = "Gestion des classes";
    ?>
    <header>
        <?php
        // insertion du sidebar
        @include('./includes/header.php');
        ?>
    </header>
    <div class="sidebar">
        <?php
        // insertion du sidebar
        @include('./includes/sidebar.php');
        ?>
    </div>

<div class="main">
   <h1><?= $titre ?></h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert-error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="btn-teacher">
        <button onclick="document.getElementById('popup-add').style.display='flex'">+ Ajouter une classe</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom de la classe</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($classes) > 0): ?>
                <?php foreach($classes as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nom_classe']) ?></td>
                        <td class="btns">
                            <button class="btn-edit" onclick='openEditPopup(<?= json_encode($c) ?>)'>Modifier</button>
                            <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('Supprimer cette classe ?');">
                                <button class="btn-danger">Supprimer</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2">Aucune classe trouvée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Popup ajout -->
    <div id="popup-add">
        <div class="popup-content">
            <button class="close-btn" onclick="document.getElementById('popup-add').style.display='none'">&times;</button>
            <h2>Ajouter une classe</h2>
            <form method="post">
                <label>Nom de la classe * :</label>
                <input type="text" name="nom_classe" required>
                <button type="submit" name="add_classe">Enregistrer</button>
            </form>
        </div>
    </div>

    <!-- Popup modification -->
    <div id="popup-edit">
        <div class="popup-content">
            <button class="close-btn" onclick="document.getElementById('popup-edit').style.display='none'">&times;</button>
            <h2>Modifier une classe</h2>
            <form method="post">
                <input type="hidden" name="id" id="edit_id">
                <label>Nom de la classe * :</label>
                <input type="text" name="nom_classe" id="edit_nom_classe" required>
                <button type="submit" name="edit_classe">Modifier</button>
            </form>
        </div>
    </div>

    <script>
    function openEditPopup(classe) {
        document.getElementById('edit_id').value = classe.id;
        document.getElementById('edit_nom_classe').value = classe.nom_classe;
        document.getElementById('popup-edit').style.display = 'flex';
    }
    </script>

</div>
</body>
</html>
