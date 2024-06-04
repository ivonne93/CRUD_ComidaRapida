<?php

include 'conexion.php';


session_start();

$admin_id = $_SESSION['admin_id'];//lo sacamos admin_login.php

if(!isset($admin_id)){// Verifica si $admin_id no está definido (no hay sesión de administrador activa)
   header('location:admin_login.php');
}



if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `usuario` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:users_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>cuentas de usuario</title>


    <!-- LINK DE ICONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- LINK HOJA DE ESTILO -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="accounts">

   <h1 class="heading">cuentas de usuario</h1>

   <div class="box-container">

   <?php
      $select_accounts = $conn->prepare("SELECT * FROM `usuario`");// Prepara una consulta SQL para seleccionar todas las filas de la tabla 'user'
      $select_accounts->execute();
      if($select_accounts->rowCount() > 0){// Verifica si la consulta ha devuelto alguna fila
         while($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)){   
   ?>
   <div class="box">
      <p> id de usuario: <span><?= $fetch_accounts['id']; ?></span> </p>
      <p> nombre de usuario : <span><?= $fetch_accounts['nombre']; ?></span> </p>
      <p> correo : <span><?= $fetch_accounts['correo']; ?></span> </p>
      <a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>" onclick="return confirm('eliminar esta cuenta?')" class="delete-btn">eliminar</a>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No hay cuentas disponibles!</p>';
      }
   ?>

   </div>

</section>



<script src="js/admin_script.js"></script>

</body>
</html>