<?php

include 'conexion.php';

session_start();

$admin_id = $_SESSION['admin_id'];//lo sacamos admin_login.php

if(!isset($admin_id)){// Verifica si $admin_id no está definido (no hay sesión de administrador activa)
   header('location:admin_login.php');
}


if(isset($_POST['update_payment'])){

   $order_id = $_POST['order_id']; // Obtiene el ID del pedido del formulario
   $payment_status = $_POST['payment_status']; // Obtiene el nuevo estado del pago del formulario
   $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING); 

   // Prepara la consulta para actualizar el estado del pago del pedido en la base de datos
   $update_payment = $conn->prepare("UPDATE `pedidos` SET estado_pago = ? WHERE id = ?");
   $update_payment->execute([$payment_status, $order_id]);

   // Añade un mensaje para indicar que el estado del pago se ha actualizado
   $message[] = 'estado de pago actualizado!';
}



if(isset($_GET['delete'])){

   $delete_id = $_GET['delete']; // Obtiene el ID del pedido a eliminar de la URL

   // Prepara la consulta para eliminar el pedido de la base de datos
   $delete_order = $conn->prepare("DELETE FROM `pedidos` WHERE id = ?");
   $delete_order->execute([$delete_id]);

   header('location:admin_orders.php');
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>pedidos</title>

      <!-- LINK DE ICONOS -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- LINK HOJA DE ESTILO -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="orders">

<h1 class="heading">pedidos realizados</h1>

<div class="box-container">

<?php
   // Prepara la consulta para seleccionar todos los pedidos de la base de datos
   $select_orders = $conn->prepare("SELECT * FROM `pedidos`");
   $select_orders->execute();
   // Verifica si hay al menos un pedido en la base de datos
   if($select_orders->rowCount() > 0){
      while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
?>
   <div class="box">
      <!-- Muestra los detalles del pedido -->
      <p> fecha de pedido : <span><?= $fetch_orders['fecha_pedido']; ?></span> </p>
      <p> nombre : <span><?= $fetch_orders['nombre']; ?></span> </p>
      <p> numero : <span><?= $fetch_orders['numero']; ?></span> </p>
      <p> direccion : <span><?= $fetch_orders['direccion']; ?></span> </p>
      <p> total productos : <span><?= $fetch_orders['total_productos']; ?></span> </p>
      <p> total precio : <span><?= $fetch_orders['total_precio']; ?></span> </p>
      <p> metodo de pago : <span><?= $fetch_orders['estado_pago']; ?></span> </p>
      <form action="" method="post">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <select name="payment_status" class="select">
            <option selected disabled><?= $fetch_orders['estado_pago']; ?></option>
            <option value="pending">pendiente</option>
            <option value="completed">acompletado</option>
         </select>
        <div class="flex-btn">
         <input type="submit" value="update" class="option-btn" name="update_payment">
         <!-- Enlace para eliminar el pedido, con confirmación -->
         <a href="admin_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('¿eliminar este pedido?');">eliminar</a>
        </div>
      </form>
   </div>
   <?php
         }   
      }else{
         echo '<p class="empty">¡Aún no se han realizado pedidos!</p>';
      }
   ?>

</div>

</section>



<script src="js/admin_script.js"></script>

</body>
</html>