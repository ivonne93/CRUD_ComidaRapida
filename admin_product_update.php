<?php

include 'conexion.php';

session_start();

$admin_id = $_SESSION['admin_id'];//lo sacamos admin_login.php

if(!isset($admin_id)){// Verifica si $admin_id no está definido (no hay sesión de administrador activa)
   header('location:admin_login.php');
}




if(isset($_POST['update_product'])){

  
   $pid = $_POST['pid'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);

   // Obtiene la información de la imagen actual y la nueva imagen si se ha subido una
   $old_image = $_POST['old_image'];
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'img_subidas/'.$image;

   // Actualiza el nombre y precio del producto en la base de datos
   $update_product = $conn->prepare("UPDATE `productos` SET nombre = ?, precio = ? WHERE id = ?");
   $update_product->execute([$name, $price, $pid]);

   $message[] = '¡Producto actualizado exitosamente!';



   if(!empty($image)){
      // Comprueba si el tamaño de la nueva imagen es mayor que 2MB
      if($image_size > 2000000){   
         $message[] = '¡El tamaño de la imagen es demasiado grande!';
      }else{
         // Actualiza la imagen del producto en la base de datos
         $update_image = $conn->prepare("UPDATE `productos` SET imagen = ? WHERE id = ?");
         $update_image->execute([$image, $pid]);
         
         // Mueve la nueva imagen al directorio correspondiente y elimina la antigua
         move_uploaded_file($image_tmp_name, $image_folder);
         unlink('img_subidas/'.$old_image);

         $message[] = 'imagen actualizada exitosamente!';
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
   <title>actualizar producto</title>

   <!-- LINK DE ICONOS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- LINK HOJA DE ESTILO -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="update-product">

   <h1 class="heading">actualizar producto</h1>

   <?php
   // Obtiene el ID del producto a actualizar de la URL
   $update_id = $_GET['update'];

   // Prepara y ejecuta una consulta para seleccionar el producto con el ID especificado
   $select_products = $conn->prepare("SELECT * FROM `productos` WHERE id = ?");
   $select_products->execute([$update_id]);

   // Verifica si el producto existe
   if($select_products->rowCount() > 0){
      
      while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>

   <form action="" enctype="multipart/form-data" method="post">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="old_image" value="<?= $fetch_products['imagen']; ?>">
      <img src="img_subidas/<?= $fetch_products['imagen']; ?>" alt="">
      <input type="text" class="box" required maxlength="100" placeholder="ingrese el nombre del producto" name="name" value="<?= $fetch_products['nombre']; ?>">
      <input type="number" min="0" class="box" required max="9999999999" placeholder="ingrese el precio del producto" onkeypress="if(this.value.length == 10) return false;" name="price" value="<?= $fetch_products['precio']; ?>">
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
      <div class="flex-btn">
         <input type="submit" value="update product" class="btn-2" name="update_product">
         <a href="admin_products.php" class="option-btn">regresar</a>
      </div>
   </form>

   <?php
         }
      }else{
         echo '<p class="empty">¡No se encontró ningún producto!</p>';
      }
   ?>

</section>




<script src="js/admin_script.js"></script>

</body>
</html>