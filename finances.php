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
  $currentPage = 'finance';
  // on definit le titre a afficher dans l'onglet
  $titre = "Gestion des finances";
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

   

  
  </div>


</body>

</html>