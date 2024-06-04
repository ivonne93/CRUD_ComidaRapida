<?php

include 'conexion.php';

session_start();

$admin_id = $_SESSION['admin_id']; // Obtiene el ID del administrador almacenado en la sesión actual.

if(!isset($admin_id)){ // Verifica si el ID del administrador no está establecido en la sesión.
   header('location:admin_login.php'); 

}


if(isset($_POST['update'])){ 

    $name = $_POST['name']; // Obtiene el nuevo nombre del formulario
    $name = filter_var($name, FILTER_SANITIZE_STRING); 
 
    $update_profile_name = $conn->prepare("UPDATE `admin` SET nombre = ? WHERE id = ?"); // Prepara la consulta para actualizar el nombre en la base de datos
    $update_profile_name->execute([$name, $admin_id]); 
 
    $prev_pass = $_POST['prev_pass']; 
    $old_pass = sha1($_POST['old_pass']); 
    $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING); 
    $new_pass = sha1($_POST['new_pass']); 
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING); 
    $confirm_pass = sha1($_POST['confirm_pass']); 
    $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING); 
    $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709'; // Valor SHA1 para una cadena vacía
 
    if($old_pass != $empty_pass){ // Verifica si la contraseña antigua no está vacía
       if($old_pass != $prev_pass){ // Verifica si la contraseña antigua ingresada coincide con la almacenada
          $message[] = '¡La contraseña anterior no coincide!'; 
       }elseif($new_pass != $confirm_pass){ // Verifica si la nueva contraseña coincide con la confirmación
          $message[] = 'confirmar contraseña no coincide!'; 
       }else{
          if($new_pass != $empty_pass){ // Verifica si la nueva contraseña no está vacía
             $update_admin_pass = $conn->prepare("UPDATE `admin` SET contrasena = ? WHERE id = ?"); // Prepara la consulta para actualizar la contraseña
             $update_admin_pass->execute([$confirm_pass, $admin_id]); 
             $message[] = '¡Contraseña actualizada exitosamente!'; 
          }else{
             $message[] = '¡Por favor ingrese una nueva contraseña!'; // Mensaje de error si la nueva contraseña está vacía
          }
       }
    }else{
       $message[] = 'por favor ingrese la contraseña anterior';
    }
}
 
?>


<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Actualización de Perfil</title>

     <!-- LINK DE ICONOS -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- LINK HOJA DE ESTILO -->
<link rel="stylesheet" href="css/admin_style.css">


</head>
<body>

<?php include 'admin_header.php' ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Actualización de Perfil</h3>
      <input type="hidden" name="prev_pass" value="<?= $fetch_profile['contrasena']; ?>">
      <input type="text" name="name" value="<?= $fetch_profile['nombre']; ?>" required placeholder="enter your username" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="ingrese la contraseña anterior" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')"><!--Su propósito es eliminar cualquier espacio en blanco que el usuario pueda escribir en el campo de entrada-->
      <input type="password" name="new_pass" placeholder="ingrese la nueva contraseña" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="confirme la nueva contraseña" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" class="btn" name="update">
   </form>

</section>



<script src="js/admin_script.js"></script>

</body>
</html>