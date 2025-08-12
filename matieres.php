<?php
session_start();
require_once './includes/db.php';

// Récupération rôle utilisateur
$role = $_SESSION['user_role'] ?? 'user';

// ----------------------
// Ajout matière
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $nom = trim($_POST['nom']);
    $coef_6 = intval($_POST['coef_6']);
    $coef_5 = intval($_POST['coef_5']);
    $coef_4 = intval($_POST['coef_4']);
    $coef_3 = intval($_POST['coef_3']);

    if ($nom !== '') {
        $stmt = $pdo->prepare("INSERT INTO matieres (nom, coef_6, coef_5, coef_4, coef_3) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$nom, $coef_6, $coef_5, $coef_4, $coef_3])) {
            $_SESSION['success'] = "✅ Matière ajoutée avec succès.";
        } else {
            $_SESSION['error'] = "❌ Erreur lors de l'ajout de la matière.";
        }
    } else {
        $_SESSION['error'] = "❌ Le nom de la matière est obligatoire.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ----------------------
// Modification matière
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_subject'])) {
    $id = intval($_POST['id']);
    $nom = trim($_POST['nom']);
    $coef_6 = intval($_POST['coef_6']);
    $coef_5 = intval($_POST['coef_5']);
    $coef_4 = intval($_POST['coef_4']);
    $coef_3 = intval($_POST['coef_3']);

    if ($nom !== '') {
        $stmt = $pdo->prepare("UPDATE matieres SET nom=?, coef_6=?, coef_5=?, coef_4=?, coef_3=? WHERE id=?");
        if ($stmt->execute([$nom, $coef_6, $coef_5, $coef_4, $coef_3, $id])) {
            $_SESSION['success'] = "✅ Matière modifiée avec succès.";
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la modification de la matière.";
        }
    } else {
        $_SESSION['error'] = "❌ Le nom de la matière est obligatoire.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ----------------------
// Suppression matière
// ----------------------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($pdo->prepare("DELETE FROM matieres WHERE id=?")->execute([$id])) {
        $_SESSION['success'] = "✅ Matière supprimée avec succès.";
    } else {
        $_SESSION['error'] = "❌ Erreur lors de la suppression.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ----------------------
// Récupération matières
// ----------------------
$stmt = $pdo->query("SELECT * FROM matieres ORDER BY nom ASC");
$matieres = $stmt->fetchAll();
$total_matieres = count($matieres);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./css/style.css">
  

</head>

<style>
    body { font-family: Arial; margin: 20px 50px; background-color: red; }
    table { border-collapse: collapse; width: 100%; max-width: 1000px; margin-bottom: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center;}
    th { background-color: #f0f0f0; } 
    button { padding: 6px 12px; cursor: pointer; border-radius: 5px; }
    .btn-danger { background: #e74c3c; color: white; border: none; }
    .btn-edit { background: #3498db; color: white; border: none; }
    #popup-form, #popup-edit {
        display: none; position: fixed; top:0; left:0; right:0; bottom:0;
        background: rgba(0,0,0,0.5); justify-content: center; align-items: center;
    }
    .popup-content {
        background: white; padding: 20px; border-radius: 6px; min-width: 300px;
    }
    .popup-content label { display: block; margin-top: 10px; }
    .popup-content input { width: 100%; padding: 6px; box-sizing: border-box; }
    .close-btn { background: #e74c3c; color: white; border: none; padding: 4px 8px; float: right; cursor: pointer; }
    .alert-success { background: #2ecc71; color: white; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    .alert-error { background: #e74c3c; color: white; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    .btn-title{display: flex; margin: 10px 0; justify-content: space-between;}
    .btn-title p{background-color: lightpink; padding: 10px; border-radius:10px; margin-right: 10%;}
    .btn-title  button{background-color: #3498db; border-radius: 12px; border: none; color: #fff;font-size: 18px;}
</style>
</head>
<body>

 <?php
  $currentPage = 'matières';
  // on definit le titre a afficher dans l'onglet
  $titre = "Gestion des matières";
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
    <div class="dashboard">
      <h1><?= $titre ?></h1>
      <p>Bienvenue ! Voici un aperçu des activité de votre école.</p>
    </div>
   <div class=""></div>

<!-- Notifications -->
<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert-error"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="btn-title">
  <button onclick="document.getElementById('popup-form').style.display='flex'"> + Ajouter une matière</button>
<p>Total matières : <strong><?= $total_matieres ?></strong></p>
</div>


<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Coef 6ème</th>
            <th>Coef 5ème</th>
            <th>Coef 4ème</th>
            <th>Coef 3ème</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($total_matieres > 0): ?>
            <?php foreach ($matieres as $matiere): ?>
                <tr>
                    <td><?= htmlspecialchars($matiere['nom']) ?></td>
                    <td><?= $matiere['coef_6'] ?></td>
                    <td><?= $matiere['coef_5'] ?></td>
                    <td><?= $matiere['coef_4'] ?></td>
                    <td><?= $matiere['coef_3'] ?></td>
                    <td>
                        <button class="btn-edit" onclick='openEditPopup(<?= json_encode($matiere) ?>)'>Modifier</button>
                        <a href="?delete=<?= $matiere['id'] ?>" onclick="return confirm('Supprimer cette matière ?');">
                            <button class="btn-danger">Supprimer</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">Aucune matière trouvée.</td></tr>
        <?php endif; ?>
    </tbody>
</table>



<!-- Popup ajout -->
<div id="popup-form">
    <div class="popup-content">
        <button class="close-btn" onclick="document.getElementById('popup-form').style.display='none'">&times;</button>
        <h2>Ajouter matière</h2>
        <form method="post">
            <label>Nom :</label>
            <input type="text" name="nom" required>
            <label>Coef 6ème :</label>
            <input type="number" name="coef_6" value="1" required>
            <label>Coef 5ème :</label>
            <input type="number" name="coef_5" value="1" required>
            <label>Coef 4ème :</label>
            <input type="number" name="coef_4" value="1" required>
            <label>Coef 3ème :</label>
            <input type="number" name="coef_3" value="1" required>
            <button type="submit" name="add_subject">Enregistrer</button>
        </form>
    </div>
</div>

<!-- Popup modification -->
<div id="popup-edit">
    <div class="popup-content">
        <button class="close-btn" onclick="document.getElementById('popup-edit').style.display='none'">&times;</button>
        <h2>Modifier matière</h2>
        <form method="post">
            <input type="hidden" name="id" id="edit_id">
            <label>Nom :</label>
            <input type="text" name="nom" id="edit_nom" required>
            <label>Coef 6ème :</label>
            <input type="number" name="coef_6" id="edit_coef_6" required>
            <label>Coef 5ème :</label>
            <input type="number" name="coef_5" id="edit_coef_5" required>
            <label>Coef 4ème :</label>
            <input type="number" name="coef_4" id="edit_coef_4" required>
            <label>Coef 3ème :</label>
            <input type="number" name="coef_3" id="edit_coef_3" required>
            <button type="submit" name="edit_subject">Modifier</button>
        </form>
    </div>
</div>

<script>
function openEditPopup(matiere) {
    document.getElementById('edit_id').value = matiere.id;
    document.getElementById('edit_nom').value = matiere.nom;
    document.getElementById('edit_coef_6').value = matiere.coef_6;
    document.getElementById('edit_coef_5').value = matiere.coef_5;
    document.getElementById('edit_coef_4').value = matiere.coef_4;
    document.getElementById('edit_coef_3').value = matiere.coef_3;
    document.getElementById('popup-edit').style.display = 'flex';
}
</script>

</body>
</html>
