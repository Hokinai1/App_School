<?php
session_start();
require_once './includes/db.php';

$role = $_SESSION['user_role'] ?? 'user';

// Gestion des notifications
function setMessage($type, $msg)
{
    $_SESSION[$type] = $msg;
}

// Traitement Ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_teacher'])) {
    $nom = trim($_POST['nom']);
    $prenoms = trim($_POST['prenoms']);
    $sexe = $_POST['sexe'];
    $date_naissance = $_POST['date_naissance'];
    $contact = trim($_POST['contact']);
    $cni = trim($_POST['cni']);
    $email = trim($_POST['email']);
    $diplome_acad = trim($_POST['diplome_academique']);
    $diplome_prof = trim($_POST['diplome_professionnel']);
    $matieres = trim($_POST['matieres_enseignees']);
    $anciennete = intval($_POST['anciennete']);
    $date_arrivee = $_POST['date_arrivee'];
    $date_depart = $_POST['date_depart'] ?: null; // nullable

    $statut = ($date_depart === null || $date_depart === '') ? 'actif' : 'inactif';

    // Validation basique
    if ($nom === '' || $prenoms === '' || !$date_naissance || !$date_arrivee) {
        setMessage('error', 'Veuillez remplir les champs obligatoires (Nom, Prénoms, Date de naissance, Date d\'arrivée).');
    } else {
        $stmt = $pdo->prepare("INSERT INTO enseignants (nom, prenoms, sexe, date_naissance, contact, cni, email, diplome_academique, diplome_professionnel, matieres_enseignees, anciennete, statut, date_arrivee, date_depart)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $res = $stmt->execute([$nom, $prenoms, $sexe, $date_naissance, $contact, $cni, $email, $diplome_acad, $diplome_prof, $matieres, $anciennete, $statut, $date_arrivee, $date_depart]);
        if ($res) {
            setMessage('success', 'Enseignant ajouté avec succès.');
        } else {
            setMessage('error', 'Erreur lors de l\'ajout de l\'enseignant.');
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Traitement Modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_teacher'])) {
    $id = intval($_POST['id']);
    $nom = trim($_POST['nom']);
    $prenoms = trim($_POST['prenoms']);
    $sexe = $_POST['sexe'];
    $date_naissance = $_POST['date_naissance'];
    $contact = trim($_POST['contact']);
    $cni = trim($_POST['cni']);
    $email = trim($_POST['email']);
    $diplome_acad = trim($_POST['diplome_academique']);
    $diplome_prof = trim($_POST['diplome_professionnel']);
    $matieres = trim($_POST['matieres_enseignees']);
    $anciennete = intval($_POST['anciennete']);
    $date_arrivee = $_POST['date_arrivee'];
    $date_depart = $_POST['date_depart'] ?: null;

    $statut = ($date_depart === null || $date_depart === '') ? 'actif' : 'inactif';

    if ($nom === '' || $prenoms === '' || !$date_naissance || !$date_arrivee) {
        setMessage('error', 'Veuillez remplir les champs obligatoires.');
    } else {
        $stmt = $pdo->prepare("UPDATE enseignants SET nom=?, prenoms=?, sexe=?, date_naissance=?, contact=?, cni=?, email=?, diplome_academique=?, diplome_professionnel=?, matieres_enseignees=?, anciennete=?, statut=?, date_arrivee=?, date_depart=? WHERE id=?");
        $res = $stmt->execute([$nom, $prenoms, $sexe, $date_naissance, $contact, $cni, $email, $diplome_acad, $diplome_prof, $matieres, $anciennete, $statut, $date_arrivee, $date_depart, $id]);
        if ($res) {
            setMessage('success', 'Enseignant modifié avec succès.');
        } else {
            setMessage('error', 'Erreur lors de la modification.');
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Suppression
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM enseignants WHERE id=?");
    if ($stmt->execute([$id])) {
        setMessage('success', 'Enseignant supprimé avec succès.');
    } else {
        setMessage('error', 'Erreur lors de la suppression.');
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Récupérer enseignants
$enseignants = $pdo->query("SELECT * FROM enseignants ORDER BY nom ASC")->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/enseignants.css">
    <link rel="shortcut icon" href="./assets/icons/icon.png" type="image/x-icon">
</head>


</head>

<body>

    <?php
    $currentPage = 'dashboard';
    // on definit le titre a afficher dans l'onglet
    $titre = "Gestion des enseignants";
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




        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert-error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <div class="btn-teacher">
            <button onclick="document.getElementById('popup-add').style.display='flex'">+ Ajouter un enseignant</button>
            <button>Liste des enseignants</button>

        </div>

        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénoms</th>
                    <th>Sexe</th>
                    <!-- <th>Date de naissance</th> -->
                    <th>Contact</th>
                    <!-- <th>N° CNI</th> -->
                    <!-- <th>Email</th> -->
                    <th>Diplôme Académique</th>
                    <th>Diplôme Professionnel</th>
                    <th>Matières enseignées</th>
                    <th>Ancienneté (années)</th>
                    <th>Statut</th>
                    <!-- <th>Date d'arrivée</th>
            <th>Date de départ</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($enseignants) > 0): ?>
                    <?php foreach ($enseignants as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e['nom']) ?></td>
                            <td><?= htmlspecialchars($e['prenoms']) ?></td>
                            <td><?= htmlspecialchars($e['sexe']) ?></td>


                            <td><?= htmlspecialchars($e['contact']) ?></td>

                            <td><?= htmlspecialchars($e['diplome_academique']) ?></td>
                            <td><?= htmlspecialchars($e['diplome_professionnel']) ?></td>
                            <td><?= nl2br(htmlspecialchars($e['matieres_enseignees'])) ?></td>
                            <td><?= htmlspecialchars($e['anciennete']) ?></td>
                            <td class="statut-<?= $e['statut'] ?>">
                                <?= ($e['statut'] === 'actif') ? 'Actif' : 'Inactif' ?>
                            </td>

                            <td class="btns">
                                <button class="btn-edit" onclick='openEditPopup(<?= json_encode($e) ?>)'>Modifier</button>
                                <a href="?delete=<?= $e['id'] ?>" onclick="return confirm('Supprimer cet enseignant ?');">
                                    <button class="btn-danger">Supprimer</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="15">Aucun enseignant trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Popup ajout -->
        <div id="popup-add">
            <div class="popup-content">
                <button class="close-btn" onclick="document.getElementById('popup-add').style.display='none'">&times;</button>
                <h2>Ajouter un enseignant</h2>
                <form method="post">

                    <div class="inputs">
                        <div class="input-1">
                            <label>Nom * :</label>
                            <input type="text" name="nom" required>
                        </div>
                        <div class="input-1">
                            <label>Prénoms * :</label>
                            <input type="text" name="prenoms" required>
                        </div>
                        <div class="input-1">
                            <label>Sexe * :</label>
                            <select name="sexe" required>
                                <option value="">-- Sélectionnez --</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>

                    </div>

                    <label>Date de naissance * :</label>
                    <input type="date" name="date_naissance" required>



                    <div class="inputs">
                        <div class="input-1">
                            <label>Contact * :</label>
                            <input type="text" name="contact" required>
                        </div>
                        <div class="input-1">
                            <label>N° CNI * :</label>
                            <input type="text" name="cni" required>
                        </div>
                        <div class="input-1">
                            <label>Email :</label>
                            <input type="email" name="email">
                        </div>
                    </div>


                    <div class="inputs long">
                        <div class="input-1">
                            <label>Diplôme académique :</label>
                            <input type="text" name="diplome_academique">
                        </div>
                        <div class="input-1">
                            <label>Diplôme professionnel :</label>
                            <input type="text" name="diplome_professionnel">
                        </div>
                    </div>

                    <label>Matières enseignées :</label>
                    <input type="text" name="matieres_enseignees">

                    <div class="inputs">
                        <div class="input-1"></div>
                        <div class="input-1"></div>
                    </div>

                    <div class="inputs">
                        <div class="input-1">
                            <label>Ancienneté (années) :</label>
                            <input type="number" name="anciennete" min="0" value="0">
                        </div>
                        <div class="input-1">
                            <label>Date d'arrivée * :</label>
                            <input type="date" name="date_arrivee" required>
                        </div>
                        <div class="input-1">
                            <label>Date de départ :</label>
                            <input type="date" name="date_depart">
                        </div>
                    </div>

                    <button id="btn-add" type="submit" name="add_teacher">Enregistrer</button>
                </form>
            </div>
        </div>

        <!-- Popup modification -->
        <div id="popup-edit">
            <div class="popup-content">
                <button class="close-btn" onclick="document.getElementById('popup-edit').style.display='none'">&times;</button>
                <h2>Modifier un enseignant</h2>
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

                    <label>Date de naissance * :</label>
                    <input type="date" name="date_naissance" id="edit_date_naissance" required>

                    <label>Contact * :</label>
                    <input type="text" name="contact" id="edit_contact" required>

                    <label>N° CNI * :</label>
                    <input type="text" name="cni" id="edit_cni" required>

                    <label>Email :</label>
                    <input type="email" name="email" id="edit_email">

                    <label>Diplôme académique :</label>
                    <input type="text" name="diplome_academique" id="edit_diplome_academique">

                    <label>Diplôme professionnel :</label>
                    <input type="text" name="diplome_professionnel" id="edit_diplome_professionnel">

                    <label>Matières enseignées :</label>
                    <textarea name="matieres_enseignees" id="edit_matieres_enseignees" rows="3"></textarea>

                    <label>Ancienneté (années) :</label>
                    <input type="number" name="anciennete" id="edit_anciennete" min="0">

                    <label>Date d'arrivée * :</label>
                    <input type="date" name="date_arrivee" id="edit_date_arrivee" required>

                    <label>Date de départ :</label>
                    <input type="date" name="date_depart" id="edit_date_depart">

                    <button type="submit" name="edit_teacher">Modifier</button>
                </form>
            </div>
        </div>

        <script>
            function openEditPopup(enseignant) {
                document.getElementById('edit_id').value = enseignant.id;
                document.getElementById('edit_nom').value = enseignant.nom;
                document.getElementById('edit_prenoms').value = enseignant.prenoms;
                document.getElementById('edit_sexe').value = enseignant.sexe;
                document.getElementById('edit_date_naissance').value = enseignant.date_naissance;
                document.getElementById('edit_contact').value = enseignant.contact;
                document.getElementById('edit_cni').value = enseignant.cni;
                document.getElementById('edit_email').value = enseignant.email;
                document.getElementById('edit_diplome_academique').value = enseignant.diplome_academique;
                document.getElementById('edit_diplome_professionnel').value = enseignant.diplome_professionnel;
                document.getElementById('edit_matieres_enseignees').value = enseignant.matieres_enseignees;
                document.getElementById('edit_anciennete').value = enseignant.anciennete;
                document.getElementById('edit_date_arrivee').value = enseignant.date_arrivee;
                document.getElementById('edit_date_depart').value = enseignant.date_depart ?? '';
                document.getElementById('popup-edit').style.display = 'flex';
            }
        </script>

</body>

</html>