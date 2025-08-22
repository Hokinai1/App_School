<?php
session_start();
require_once './includes/db.php';

$role = $_SESSION['user_role'] ?? 'user';

// Gestion des notifications
function setMessage($type, $msg) {
    $_SESSION[$type] = $msg;
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Filtre par classe
$classe_filter = $_GET['classe'] ?? '';

// Récupérer toutes les classes pour formulaire et filtre
$classes = $pdo->query("SELECT id, nom_classe FROM classe ORDER BY nom_classe ASC")->fetchAll(PDO::FETCH_ASSOC);
$classIds = array_column($classes, 'id'); // pour validation

// Traitement Ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_eleve'])) {
    $nom = trim($_POST['nom']);
    $prenoms = trim($_POST['prenoms']);
    $sexe = $_POST['sexe'];
    $classe = intval($_POST['classe']);
    $statut = $_POST['statut'];
    $adresse = trim($_POST['adresse']);
    $contact = trim($_POST['contact']);

    if ($nom === '' || $prenoms === '' || $sexe === '' || !in_array($classe, $classIds)) {
        setMessage('error', 'Veuillez remplir correctement tous les champs obligatoires et choisir une classe valide.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO eleves (nom, prenoms, sexe, classe, statut, adresse, contact) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $res = $stmt->execute([$nom, $prenoms, $sexe, $classe, $statut, $adresse, $contact]);
        setMessage($res ? 'success' : 'error', $res ? 'Élève ajouté avec succès.' : 'Erreur lors de l\'ajout.');
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Traitement Modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_eleve'])) {
    $id = intval($_POST['id']);
    $nom = trim($_POST['nom']);
    $prenoms = trim($_POST['prenoms']);
    $sexe = $_POST['sexe'];
    $classe = intval($_POST['classe']);
    $statut = $_POST['statut'];
    $adresse = trim($_POST['adresse']);
    $contact = trim($_POST['contact']);

    if ($nom === '' || $prenoms === '' || $sexe === '' || !in_array($classe, $classIds)) {
        setMessage('error', 'Veuillez remplir correctement tous les champs obligatoires et choisir une classe valide.');
    } else {
        $stmt = $pdo->prepare("UPDATE eleves SET nom=?, prenoms=?, sexe=?, classe=?, statut=?, adresse=?, contact=? WHERE id=?");
        $res = $stmt->execute([$nom, $prenoms, $sexe, $classe, $statut, $adresse, $contact, $id]);
        setMessage($res ? 'success' : 'error', $res ? 'Élève modifié avec succès.' : 'Erreur lors de la modification.');
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Suppression
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM eleves WHERE id=?");
    setMessage($stmt->execute([$id]) ? 'success' : 'error', $stmt->execute([$id]) ? 'Élève supprimé avec succès.' : 'Erreur lors de la suppression.');
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Total des élèves
$totalElevesStmt = $pdo->query("SELECT COUNT(*) as total FROM eleves" . ($classe_filter ? " WHERE classe=" . intval($classe_filter) : ""));
$totalEleves = $totalElevesStmt->fetch()['total'];

// Récupérer élèves avec jointure sur classe
$sql = "SELECT e.*, c.nom_classe FROM eleves e LEFT JOIN classe c ON e.classe = c.id";
if ($classe_filter) $sql .= " WHERE e.classe=" . intval($classe_filter);
$sql .= " ORDER BY e.nom ASC LIMIT $limit OFFSET $offset";
$eleves = $pdo->query($sql)->fetchAll();

// Pagination
$totalPages = ceil($totalEleves / $limit);
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
    $titre = "Gestion des élèves";
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
        <p>les élèves inscrit ...</p>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert-error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="btn-teacher">
        <button onclick="document.getElementById('popup-add').style.display='flex'">+ Ajouter un élève</button>
        <form method="get" style="display:inline-block">
            <select name="classe" onchange="this.form.submit()">
                <option value="">-- Filtrer par classe --</option>
                <?php foreach($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $classe_filter==$c['id']?'selected':'' ?>>
                        <?= htmlspecialchars($c['nom_classe']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <p>Total des élèves : <strong><?= $totalEleves ?></strong></p>
        <button onclick="window.print()">Imprimer la liste</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénoms</th>
                <th>Sexe</th>
                <th>Classe</th>
                <th>Statut</th>
                <th>Adresse</th>
                <th>Contact</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($eleves) > 0): ?>
                <?php foreach($eleves as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['nom']) ?></td>
                        <td><?= htmlspecialchars($e['prenoms']) ?></td>
                        <td><?= htmlspecialchars($e['sexe']) ?></td>
                        <td><?= htmlspecialchars($e['nom_classe']) ?></td>
                        <td><?= htmlspecialchars($e['statut']) ?></td>
                        <td><?= htmlspecialchars($e['adresse']) ?></td>
                        <td><?= htmlspecialchars($e['contact']) ?></td>
                        <td class="btns">
                            <button class="btn-edit" onclick='openEditPopup(<?= json_encode($e) ?>)'>Modifier</button>
                            <a href="?delete=<?= $e['id'] ?>" onclick="return confirm('Supprimer cet élève ?');">
                                <button class="btn-danger">Supprimer</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Aucun élève trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        for($i=1;$i<=$totalPages;$i++){
            $link = "?page=$i" . ($classe_filter?"&classe=".urlencode($classe_filter):"");
            echo "<a href='$link' class='".($i==$page?'active':'')."'>$i</a> ";
        }
        ?>
    </div>

    <!-- Popup ajout -->
    <div id="popup-add">
        <div class="popup-content">
            <button class="close-btn" onclick="document.getElementById('popup-add').style.display='none'">&times;</button>
            <h2>Ajouter un élève</h2>
            <form method="post">
                <label>Nom * :</label>
                <input type="text" name="nom" required>
                <label>Prénoms * :</label>
                <input type="text" name="prenoms" required>
                <label>Sexe * :</label>
                <select name="sexe" required>
                    <option value="">-- Sélectionnez --</option>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
                <label>Classe * :</label>
                <select name="classe" required>
                    <option value="">-- Sélectionnez --</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom_classe']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Statut :</label>
                <select name="statut">
                    <option value="nouveau">Nouveau</option>
                    <option value="doublant">Doublant</option>
                </select>
                <label>Adresse :</label>
                <input type="text" name="adresse">
                <label>Contact :</label>
                <input type="text" name="contact">
                <button type="submit" name="add_eleve">Enregistrer</button>
            </form>
        </div>
    </div>

    <!-- Popup modification -->
    <div id="popup-edit">
        <div class="popup-content">
            <button class="close-btn" onclick="document.getElementById('popup-edit').style.display='none'">&times;</button>
            <h2>Modifier un élève</h2>
            <form method="post">
                <input type="hidden" name="id" id="edit_id">
                <label>Nom * :</label>
                <input type="text" name="nom" id="edit_nom" required>
                <label>Prénoms * :</label>
                <input type="text" name="prenoms" id="edit_prenoms" required>
                <label>Sexe * :</label>
                <select name="sexe" id="edit_sexe" required>
                    <option value="">-- Sélectionnez --</option>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
                <label>Classe * :</label>
                <select name="classe" id="edit_classe" required>
                    <option value="">-- Sélectionnez --</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom_classe']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Statut :</label>
                <select name="statut" id="edit_statut">
                    <option value="nouveau">Nouveau</option>
                    <option value="doublant">Doublant</option>
                </select>
                <label>Adresse :</label>
                <input type="text" name="adresse" id="edit_adresse">
                <label>Contact :</label>
                <input type="text" name="contact" id="edit_contact">
                <button type="submit" name="edit_eleve">Modifier</button>
            </form>
        </div>
    </div>

    <script>
    function openEditPopup(eleve) {
        document.getElementById('edit_id').value = eleve.id;
        document.getElementById('edit_nom').value = eleve.nom;
        document.getElementById('edit_prenoms').value = eleve.prenoms;
        document.getElementById('edit_sexe').value = eleve.sexe;
        document.getElementById('edit_classe').value = eleve.classe;
        document.getElementById('edit_statut').value = eleve.statut;
        document.getElementById('edit_adresse').value = eleve.adresse;
        document.getElementById('edit_contact').value = eleve.contact;
        document.getElementById('popup-edit').style.display = 'flex';
    }
    </script>
</div>
</body>
</html>
