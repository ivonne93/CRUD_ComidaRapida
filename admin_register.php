
<?php

include 'conexion.php';

session_start();


if(isset($_POST['register'])){ 

   $name = $_POST['name']; 
   $name = filter_var($name, FILTER_SANITIZE_STRING); 
   $pass = sha1($_POST['pass']); 
   $pass = filter_var($pass, FILTER_SANITIZE_STRING); 
   $cpass = sha1($_POST['cpass']); 
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING); 

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE nombre = ?"); // Prepara la consulta para verificar si el nombre de usuario ya existe
   $select_admin->execute([$name]); 

   if($select_admin->rowCount() > 0){ // Verifica si el nombre de usuario ya existe en la base de datos
      $message[] = '¡el nombre de usuario ya existe!'; 
   }else{
      if($pass != $cpass){ // Verifica si la contraseña y su confirmación coinciden
         $message[] = '¡Confirme que la contraseña no coincide!'; 
      }else{
         $insert_admin = $conn->prepare("INSERT INTO `admin`(nombre, contrasena) VALUES(?,?)"); // Prepara la consulta para insertar el nuevo administrador
         $insert_admin->execute([$name, $cpass]); 
         $message[] = '¡Nuevo administrador registrado exitosamente!'; 
      }
   }
}

?>



<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>registro de administrador</title>

       <!-- LINK DE ICONOS -->
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

      <!-- LINK HOJA DE ESTILO -->
      <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php' ?>


<section class="form-container">

   <form action="" method="post">
      <h3>Registrate ahora</h3>
      <input type="text" name="name" required placeholder="Ingrese su nombre de usuario" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Ingresa tu contraseña" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirmar la contraseña" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Registrate ahora" class="btn" name="register">
   </form>

</section>


<script src="js/admin_script.js"></script>

</body>
</html>