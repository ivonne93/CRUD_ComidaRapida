<?php 
include 'conexion.php';

session_start();


if(isset($_POST['login'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE nombre = ? AND contrasena = ?");// Prepara una consulta para seleccionar el administrador con el nombre de usuario y la contraseña proporcionados
   $select_admin->execute([$name, $pass]);
   $row = $select_admin->fetch(PDO::FETCH_ASSOC);// Obtiene la primera fila del resultado como un array asociativo

   if($select_admin->rowCount() > 0){   // Verifica si se encontró un administrador con las credenciales proporcionadas
      $_SESSION['admin_id'] = $row['id'];  // Guarda el ID del administrador en la sesión
      header('location:admin_page.php'); 
   }else{
      $message[] = '¡Nombre de usuario o contraseña incorrecta!';
   }

}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inicio de sesion de administrador</title>

    <!-- LINK DE ICONOS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- LINK HOJA DE ESTILO -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
<!--MENSAJE -->
<?php
   
   if(isset($message)){
    
      foreach($message as $message){
         
         echo '
         <div class="message">
            <!-- Muestra el mensaje -->
            <span>'.$message.'</span>
            <!-- Ícono con una X que permite cerrar el mensaje, usando JavaScript para eliminar el elemento -->
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>



<section class="form-container">

   <form action="" method="post">
      <h3>Inicio de sesión</h3>
      <p>usuario por defecto = <span>admin</span> <br>& contaseña = <span>111</span></p>
      <input type="text" name="name" required placeholder="Ingrese su nombre de usuario" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Ingresa tu contraseña" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Inicia sesión ahora" class="btn" name="login">
   </form>

</section>
    
</body>
</html>