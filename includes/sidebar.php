<?php
session_start();


//  Sécurité : rediriger si non connecté
// if (!isset($_SESSION['username'])) {
//     header("Location: index.php"); // redirige vers login
//     exit();
// }

// Récupération des infos depuis la session
$username = $_SESSION['username'];
$role = $_SESSION['user_role'] ?? null;

// $infos = $pdo->query("SELECT * FROM infos LIMIT 1")->fetch(PDO::FETCH_ASSOC);

?>




<style>
    .popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
    }

    .popup-content {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    .popup .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
        color: #888;
    }

    .popup button {
        padding: 8px 16px;
        border: none;
        background: #007BFF;
        color: #fff;
        border-radius: 5px;
        cursor: pointer;
    }

    .popup button:hover {
        background: #0056b3;
    }

    .brand {
        /* font-weight: bold; */
        /* background-color: #fef3e2; */
        background-color: hsla(204, 39%, 87%, 1.00);
        padding: 5px;
        border-radius: 15px;
        text-align: center;
        font-size: 25px;
        /* color: #f78c1f; */
        color: hsl(202, 55%, 16%);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        font-family: 'PlusJakartaSans-Medium';
        margin-bottom: 30px;
    }

    .brand:hover {
        background-color: hsla(203, 27%, 61%, 1.00)
    }

    .popup-content p {
        padding: 10px 0;
    }

    a {
        text-decoration: none;
        color: #fff;
        font-size: 17px;
        text-align: left;
    }

    ul li:hover {
        background: #F4F6F8;
    }

    a:hover {
        color: #1565C0;
        font-weight: bold;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar li {
        padding: 12px 20px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .active {
        
        font-weight: bold;
       background: #F4F6F8;
        border-bottom-left-radius: 10px;
        border-top-left-radius: 10px;
        /* border-right: 3px solid #1565C0; */
        a{
           color: #1565C0;
        }
    }
</style>





<!-- <h1 style="cursor:pointer;" class="brand" onclick="document.getElementById('popupEntreprise').style.display='flex'">
  <?= htmlspecialchars($infos['nom_entreprise']) ?>
</h1> -->

<ul>
    <li class="<?= ($currentPage === 'dashboard') ? 'active' : '' ?>"><a href="home.php"> Tableau de bord</a></li>
    <hr>
    <br>
    <li class="<?= ($currentPage === 'teachers') ? 'active' : '' ?>"><a href="enseignants.php">Gestion enseignants</a></li>
    <li class="<?= ($currentPage === 'classe') ? 'active' : '' ?>"><a href="classes.php">Gestion des classes</a></li>
    <li class="<?= ($currentPage === 'matières') ? 'active' : '' ?>"><a href="matieres.php"> Gestion des matières</a></li>
    <li class="<?= ($currentPage === 'finance') ? 'active' : '' ?>"><a href="#"> Gestion des finances</a></li>
    <li class="<?= ($currentPage === 'notes') ? 'active' : '' ?>"><a href="#"> Gestion des notes</a></li>
    <li class="<?= ($currentPage === 'bulletin') ? 'active' : '' ?>"><a href="#"> Gestion des bulletins</a></li>

        <li class="<?= ($currentPage === 'parametre') ? 'active' : '' ?>"><a href="#"> Configurations</a></li>
            <?php if ($role === 'admin'): ?>
    <?php endif; ?> 
                <div class=""></div> <br> <br> <br>
                <br>
                <hr>
    <li onclick=" confirmerDeconnexion()" class="<?= ($currentPage === 'deconnexion') ? 'active' : '' ?> "><a href="#"> Déconnexion</a></li>




    <!-- POPUP INFOS ENTREPRISE -->
    <!--  -->




    <script>
        function confirmerDeconnexion() {
            let confirmation = confirm("Voulez-vous vraiment vous déconnecter ?");
            if (confirmation) {
                window.location.href = "logout.php";
            }
            // Sinon, ne rien faire
        }
    </script>