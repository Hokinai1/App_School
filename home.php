<?php
session_start();

require_once './includes/db.php';



// // Sécurité : redirige si l'utilisateur n'est pas connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: index.php'); // ou login.php selon ton fichier
//     exit();
// }

// Récupération rôle utilisateur
$role = $_SESSION['user_role'] ?? 'user';

?>


<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./css/style.css">
  

</head>

<body>

  <?php
  $currentPage = 'dashboard';
  // on definit le titre a afficher dans l'onglet
  $titre = "Tableau de bord";
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
      <h1>Tableau de bord</h1>
      <p>Bienvenue ! Voici un aperçu des activité de votre école.</p>
    </div>
    <div class="cards">
      <div class="card">Total Élèves: <strong>320</strong></div>
      <div class="card">Total Enseignants: <strong>25</strong></div>
      <div class="card">Total Classes: <strong>15</strong></div>
      <div class="card">Total Recettes: <strong>45 000 FCFA</strong></div>
      <div class="card">Total Dépenses: <strong>2000 FCFA</strong></div>
    </div>

   

    <!-- Tables -->
    <div class="tables">
      <div class="table-section">
        <h2>10 derniers élèves inscrits</h2>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nom & prénoms</th>
              <th>Sexe</th>
              <th>Classe</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Durand</td>
              <td>M</td>
              <td>6ème</td>
            </tr>
            <tr>
              <td>2</td>
              <td>Martin</td>
              <td>F</td>
              <td>3ème</td>
            </tr>
            <tr>
              <td>3</td>
              <td>Lemoine</td>
              <td>F</td>
              <td>4ème</td>
            </tr>
            <tr>
              <td>4</td>
              <td>Petit</td>
              <td>M</td>
              <td>5ème</td>
            </tr>
            <tr>
              <td>5</td>
              <td>Moreau</td>
              <td>F</td>
              <td>3ème</td>
            </tr>

          </tbody>
        </table>
      </div>

      <div class="table-section">
        <h2>Liste des classes</h2>
        <table>
          <thead>
            <tr>
              <th>Classe</th>
              <th>Nombre d'élèves</th>
              <th>Titulaire</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>6ème</td>
              <td>22</td>
              <td>DJABO</td>
            </tr>
            <tr>
              <td>5ème</td>
              <td>15</td>
              <td>KOMBATE</td>
            </tr>
            <tr>
              <td>4ème</td>
              <td>5</td>
              <td>KOURAWILIKI</td>
            </tr>
            <tr>
              <td>3ème</td>
              <td>25</td>
              <td>LAMBONI</td>
            </tr>

          </tbody>
        </table>
      </div>
    </div>
  </div>

 
</body>

</html>