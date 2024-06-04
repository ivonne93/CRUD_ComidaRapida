<?php
// Check if the admin is logged in by checking the session variable
if(isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
} else {
    // Redirect to login page if admin is not logged in
    header("Location: admin_login.php");
    exit();
}



 if(isset($message)){
   
    foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

<header class="header">

   <section class="flex">
      <a href="admin_page.php" class="logo">Panel de <span>Administrador</span></a>

      <nav class="navbar">
         <a href="admin_page.php">Inicio</a>
         <a href="admin_products.php">Productos</a>
         <a href="admin_orders.php">Pedidos</a>
         <a href="admin_accounts.php">Administrador</a>
         <a href="users_accounts.php">Usuario</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile"><!--es el meunu para el usario donde pondra su nombre-->
      <?php
       // Prepara una consulta para seleccionar el perfil del administrador basado en su ID
       $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
       $select_profile->execute([$admin_id]);
       // Obtiene los datos del perfil del administrador como un array asociativo
       $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        ?>

         <p><?= $fetch_profile['nombre']; ?></p>
         <a href="admin_profile_update.php" class="btn-2">Actualizar Perfil</a>
         <a href="logout.php" class="delete-btn">cerrar sesión</a>
         <div class="flex-btn">
            <a href="admin_login.php" class="option-btn">acceso</a>
            <a href="admin_register.php" class="option-btn">registro</a>
         </div>
      </div>
   </section>

</header>
