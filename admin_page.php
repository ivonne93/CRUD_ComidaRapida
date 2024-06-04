<?php

include 'conexion.php';

session_start();

$admin_id = $_SESSION['admin_id'];//lo sacamos admin_login.php

if(!isset($admin_id)){// Verifica si $admin_id no está definido (no hay sesión de administrador activa)
   header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Tablero</title>

    <!-- LINK DE ICONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- LINK HOJA DE ESTILO -->
    <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="dashboard">

   <h1 class="heading">Tablero</h1>

   <div class="box-container">

      <div class="box">
       <?php
        $total_pendings = 0; 

        // Prepara la consulta para seleccionar todas las órdenes con estado de pago "pending"
        $select_pendings = $conn->prepare("SELECT * FROM `pedidos` WHERE estado_pago = ?");
        $select_pendings->execute(['pending']); 

        // Verifica si hay resultados de órdenes pendientes
        if($select_pendings->rowCount() > 0){

            while($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)){
                // Suma el precio total de cada orden pendiente al total acumulado
                $total_pendings += $fetch_pendings['total_precio'];
            }
        }
      ?>
         <h3>$<?= $total_pendings; ?>/-</h3>
         <p>total de pendientes</p>
         <a href="admin_orders.php" class="btn">ver pedidos</a>
      </div>


      <div class="box">
      <?php
      $total_completes = 0; 

       // Prepara la consulta para seleccionar todas las órdenes con estado de pago "completed"
      $select_completes = $conn->prepare("SELECT * FROM `pedidos` WHERE estado_pago = ?");
      $select_completes->execute(['completed']); 

      // Verifica si hay resultados de órdenes completadas
      if($select_completes->rowCount() > 0){
   
         while($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)){
               // Suma el precio total de cada orden completada al total acumulado
               $total_completes += $fetch_completes['total_precio'];
          }
       }
      ?>
         <h3>$<?= $total_completes; ?>/-</h3>
         <p>pedidos completados</p>
         <a href="admin_orders.php" class="btn">ver pedidos</a>
      </div>


      <div class="box">
       <?php
      // Prepara la consulta para seleccionar todas las órdenes de la tabla `orders`
      $select_orders = $conn->prepare("SELECT * FROM `pedidos`");
      $select_orders->execute(); 
      $number_of_orders = $select_orders->rowCount(); // Cuenta el número de filas (órdenes) obtenidas
      ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>pedidos realizados</p>
         <a href="admin_orders.php" class="btn">ver pedidos</a>
      </div>


      <div class="box">
        <?php
        // Prepara la consulta para seleccionar todos los productos de la tabla `products`
        $select_products = $conn->prepare("SELECT * FROM `productos`");
        $select_products->execute(); 
        $number_of_products = $select_products->rowCount(); // Cuenta el número de filas (productos) obtenidas
        ?>
         <h3><?= $number_of_products; ?></h3>
         <p>productos añadidos</p>
         <a href="admin_products.php" class="btn">ver productos</a>
      </div>


      <div class="box">
      <?php
      // Preparación de la consulta para seleccionar todos los usuarios de la tabla `user`
      $select_users = $conn->prepare("SELECT * FROM `usuario`");
      $select_users->execute(); // Ejecución de la consulta
      $number_of_users = $select_users->rowCount(); // Conteo del número de usuarios obtenidos
      ?>
         <h3><?= $number_of_users; ?></h3>
         <p>usuarios </p>
         <a href="users_accounts.php" class="btn">Ver usuarios</a>
      </div>


      <div class="box">
         <?php
            $select_admins = $conn->prepare("SELECT * FROM `admin`");//selecciona todos los administradores
            $select_admins->execute();
            $number_of_admins = $select_admins->rowCount()//conteo del número de administradores obtenidos.
         ?>
         <h3><?= $number_of_admins; ?></h3>
         <p>administradores</p>
         <a href="admin_accounts.php" class="btn">Ver administradores</a>
      </div>

   </div>

</section>



<script src="js/admin_script.js"></script>

</body>
</html>