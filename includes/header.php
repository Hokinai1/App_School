
<?php
session_start();

// Récupération des infos depuis la session
$username = $_SESSION['username'];
$role = $_SESSION['user_role'] ?? 'user';

?>


<link rel="stylesheet" href="./css/style.css" />
 <link rel="shortcut icon" href=".../assets/icons/icon.png" type="image/x-icon">
 <!-- on declare une variable titre qui aura le titre de chaque page . au cas ou la page n'existe pas elle affiche par defaut le nom Accueil -->
 <title> <?= $titre ?? "Accueil" ?> </title> 

<style>
  .annee{
background-color: #fff;
    padding: 5px;
    border-radius: 8px;
  }
  span{
    color: #1565C0;
    background-color: #fff;
    padding: 5px;
    border-radius: 8px;
  }
</style>

      <div class="left">
        <div><strong>CPL Yendoube</strong></div>
        <div id="date"></div>
      </div>
      <div class="annee">
        <span>Année académique : </span>
        <span>2024 - 2025</span>
      </div>
      <div class="right" id="profile">
        
        <img src="../assets/images/profil.jpg"  />
        <p>Rôle : <strong class="user"><?= htmlspecialchars($role) ?></strong> |</p>
        <span>Connecté :</span>
        <span><?= htmlspecialchars($username) ?>bbb</span>
        <div class="dropdown" id="dropdown">
          <a href="#" onclick="confirmerDeconnexion()">Déconnexion</a>
        </div>
      </div>



      <script>

        document.getElementById("date").textContent =
      new Date().toLocaleDateString("fr-FR", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
      });
    document.getElementById("profile").addEventListener("click", function() {
      const dropdown = document.getElementById("dropdown");
      dropdown.style.display =
        dropdown.style.display === "block" ? "none" : "block";
    });


        function confirmerDeconnexion() {
        let confirmation = confirm("Voulez-vous vraiment vous déconnecter ?");
        if (confirmation) {
            window.location.href = "logout.php";
        }
        // Sinon, ne rien faire
    }
      </script>
    