<?php

include 'conexion.php';

session_start();

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
 }else{
    $user_id = '';
 };

include 'index.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>pagina de busqueda</title>
   
    <!-- Link de iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Link de hoja de estilos  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
   



<!--search form section starts-->
<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search_box" placeholder="search here..." maxlength="100" class="box" required>
      <button type="submit" class="fas fa-search" name="search_btn"></button>
   </form>
</section>
<!--search form section ends-->


<section class="products" style="padding-top: 0; min-height:100vh;">
   <!-- Sección de productos, se ajusta el padding y la altura mínima -->

   <div class="box-container">
      <!-- Contenedor para las cajas de productos -->

      <?php
      // Comprueba si el formulario de búsqueda ha sido enviado
      if(isset($_POST['search_box']) OR isset($_POST['search_btn'])){
         // Recoge el valor del cuadro de búsqueda
         $search_box = $_POST['search_box'];
         
         // Prepara una consulta SQL para buscar productos cuyo nombre contenga el valor del cuadro de búsqueda
         $select_products = $conn->prepare("SELECT * FROM `productos` WHERE nombre LIKE '%{$search_box}%'"); 
         $select_products->execute(); 

         // Comprueba si se encontraron productos
         if($select_products->rowCount() > 0){
            // Si se encontraron productos, recorre cada producto
            while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
      ?>

 
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      
      <input type="hidden" name="name" value="<?= $fetch_product['nombre']; ?>">

      <input type="hidden" name="price" value="<?= $fetch_product['precio']; ?>">
    
 
      <input type="hidden" name="image" value="<?= $fetch_product['imagen']; ?>">
   
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
        
      <!-- Imagen del producto -->
      <img src="img_subidas/<?= $fetch_product['imagen']; ?>" alt="">
      
      <!-- Nombre del producto -->
      <div class="nombre"><?= $fetch_product['nombre']; ?></div>
      
      <!-- Contenedor flexible para el precio y la cantidad -->
      <div class="flex">

         <div class="precio"><span>$</span><?= $fetch_product['precio']; ?><span>/-</span></div>
         <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">producto no encontrado!</p>';
      }
   }
   ?>

   </div>

</section>

</body>
</html>
