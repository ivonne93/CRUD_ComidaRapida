<?php

include 'conexion.php';

session_start();

$admin_id = $_SESSION['admin_id'];//lo sacamos admin_login.php

if(!isset($admin_id)){// Verifica si $admin_id no está definido (no hay sesión de administrador activa)
   header('location:admin_login.php');
}



if(isset($_POST['add_product'])){ 

   
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING); 
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);

   // Obtiene el nombre y el tamaño del archivo de imagen
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'img_subidas/'.$image;

   // Prepara la consulta para verificar si el nombre del producto ya existe en la base de datos
   $select_product = $conn->prepare("SELECT * FROM `productos` WHERE nombre = ?");
   $select_product->execute([$name]);

   // Verifica si el nombre del producto ya existe en la base de datos
   if($select_product->rowCount() > 0){
      $message[] = '¡El nombre del producto ya existe!'; 
   }else{
      // Verifica si el tamaño de la imagen es superior a 2MB
      if($image_size > 2000000){
         $message[] = '¡El tamaño de la imagen es demasiado grande!'; 
      }else{
         // Inserta el nuevo producto en la base de datos y mueve la imagen a la carpeta de imágenes
         $insert_product = $conn->prepare("INSERT INTO `productos`(nombre, precio, imagen) VALUES(?,?,?)");
         $insert_product->execute([$name, $price, $image]);
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = '¡Nuevo producto agregado!'; 
      }
   }
}



if(isset($_GET['delete'])){ 

   // Obtiene el ID del producto a eliminar
   $delete_id = $_GET['delete'];

   // Obtiene el nombre del archivo de imagen del producto a eliminar
   $delete_product_image = $conn->prepare("SELECT imagen FROM `productos` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);

   // Elimina el archivo de imagen del servidor
   unlink('img_subidas/'.$fetch_delete_image['imagen']);

   // Elimina el producto de la tabla 'products'
   $delete_product = $conn->prepare("DELETE FROM `productos` WHERE id = ?");
   $delete_product->execute([$delete_id]);

   // Elimina cualquier entrada relacionada en la tabla 'carrito'
   $delete_cart = $conn->prepare("DELETE FROM `carrito` WHERE id = ?");
   $delete_cart->execute([$delete_id]);

   header('location:admin_products.php');
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>productos</title>

   <!-- LINK DE ICONOS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- LINK HOJA DE ESTILO -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php include 'admin_header.php' ?>

<section class="add-products">

   <h1 class="heading">Agregar producto</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <input type="text" class="box" required maxlength="100" placeholder="ingrese el nombre del producto" name="name">
      <input type="number" min="0" class="box" required max="9999999999" placeholder="ingrese el precio del producto" onkeypress="if(this.value.length == 10) return false;" name="price"><!--el limit eson 10 caracteres-->
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required><!-- Campo de entrada para subir la imagen del producto -->
      <input type="submit" value="Agregar producto" class="btn" name="add_product">
   </form>

</section>




<section class="show-products"><!--MOSTRAR LOS PRODUCTOS-->

   <h1 class="heading">productos añadidos</h1>

   <div class="box-container">

   <?php
      // Prepara la consulta para seleccionar todos los productos de la tabla `products`
      $select_products = $conn->prepare("SELECT * FROM `productos`");
      $select_products->execute(); 

      // Verifica si hay productos en la base de datos
      if($select_products->rowCount() > 0){
         
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <div class="box">
      <!-- Muestra el precio del producto -->
      <div class="price">$<span><?= $fetch_products['precio']; ?></span>/-</div>
      <!-- Muestra la imagen del producto -->
      <img src="img_subidas/<?= $fetch_products['imagen']; ?>" alt="">
      <!-- Muestra el nombre del producto -->
      <div class="name"><?= $fetch_products['nombre']; ?></div>
      <div class="flex-btn">
         
         <a href="admin_product_update.php?update=<?= $fetch_products['id']; ?>" class="option-btn">actualizar</a>
    
         <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">eliminar</a>
      </div>
   </div>
   <?php
         }
      }else{
         // Mensaje de advertencia si no hay productos agregados aún
         echo '<p class="empty">¡Aún no se han añadido productos!</p>';
      }
   ?>
   
   </div>

</section>

<!--LINK JS-->
<script src="js/admin_script.js"></script>

</body>
</html>