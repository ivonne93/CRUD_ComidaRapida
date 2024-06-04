
<?php

include 'conexion.php';

session_start();

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
 }else{
    $user_id = '';
 };

//registrar usuario
 if(isset($_POST['register'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass'] );
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `usuario` WHERE nombre = ? AND correo = ?");
   $select_user->execute([$name, $email]);

   if($select_user->rowCount() > 0){
      $message[] = '¡El nombre de usuario o el correo electrónico ya existe!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirmar contraseña no coincide!';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `usuario`(nombre, correo, contrasena) VALUES(?,?,?)");
         $insert_user->execute([$name, $email, $cpass]);
         $message[] = 'registrado exitosamente, inicie sesión ahora por favor!';
      }
   }

}

//actualiza la cantidad de un artículo específico en el carrito de compras en la base de datos
if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE `carrito` SET cantidad = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = '¡Cantidad del carrito actualizada!';
}

//eliminar del carrito
if(isset($_GET['delete_cart_item'])){
   $delete_cart_id = $_GET['delete_cart_item'];
   $delete_cart_item = $conn->prepare("DELETE FROM `carrito` WHERE id = ?");
   $delete_cart_item->execute([$delete_cart_id]);
   header('location:index.php');
}

//salida del usuario
if(isset($_GET['logout'])){
   session_unset();
   session_destroy();
   header('location:index.php');
}

//agregar al carrito
if(isset($_POST['add_to_cart'])){

   if($user_id == ''){
      $message[] = '¡por favor ingresa primero!';
   }else{

      $pid = $_POST['pid'];
      $name = $_POST['name'];
      $price = $_POST['price'];
      $image = $_POST['image'];
      $qty = $_POST['qty'];
      $qty = filter_var($qty, FILTER_SANITIZE_STRING);

      $select_cart = $conn->prepare("SELECT * FROM `carrito` WHERE usuario_id = ? AND nombre = ?");
      $select_cart->execute([$user_id, $name]);

      if($select_cart->rowCount() > 0){
         $message[] = 'agregado al carrito!';
      }else{
         $insert_cart = $conn->prepare("INSERT INTO `carrito`(usuario_id, producto_id, nombre, precio, cantidad, imagen) VALUES(?,?,?,?,?,?)");
         $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
         $message[] = 'agregado al carrito!';
      }

   }

}

//orden
if(isset($_POST['order'])){
   if($user_id == ''){
       $message[] = '¡Por favor ingresa primero!';
   }else{
       $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
       $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
       $address = 'flat no.'.filter_var($_POST['flat'], FILTER_SANITIZE_STRING).', '.filter_var($_POST['street'], FILTER_SANITIZE_STRING).' - '.filter_var($_POST['pin_code'], FILTER_SANITIZE_STRING);
       $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
       $total_price = filter_var($_POST['total_price'], FILTER_SANITIZE_STRING);
       $total_products = filter_var($_POST['total_products'], FILTER_SANITIZE_STRING);

       $select_cart = $conn->prepare("SELECT * FROM `carrito` WHERE usuario_id = ?");
       $select_cart->execute([$user_id]);

       if($select_cart->rowCount() > 0){
           $insert_order = $conn->prepare("INSERT INTO `pedidos`(usuario_id, nombre, numero, metodo, direccion, total_productos, total_precio, fecha_pedido, estado_pago) VALUES(?,?,?,?,?,?,?, NOW(), 'pendiente')");
           $insert_order->execute([$user_id, $name, $number, $method, $address, $total_products, $total_price]);
           $delete_cart = $conn->prepare("DELETE FROM `carrito` WHERE usuario_id = ?");
           $delete_cart->execute([$user_id]);
           $message[] = 'Pedido realizado con éxito!';
       }else{
           $message[] = '¡Tu carrito está vacío!';
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
   <title>SaborVeloz</title>

   <!-- Link de iconos -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Link de hoja de estilos  -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
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

<!-- header section starts  -->
<header class="header">

   <section class="flex">

      <a href="#home" class="logo">SaborVeloz</a>

      <nav class="navbar">
         <a href="#home">inicio</a>
         <a href="#about">conócenos</a>
         <a href="#menu">menu</a>
         <a href="#order">pedido</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="order-btn" class="fas fa-box"></div>
         <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `carrito` WHERE usuario_id = ?");//id
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
         ?>
         <div id="cart-btn" class="fas fa-shopping-cart"><span>(<?= $total_cart_items; ?>)</span></div>

      </div>

   </section>

</header>
<!-- header section ends -->


<!--menu del usurio-->
<div class="user-account">

   <section>

      <div id="close-account"><span>cerrar</span></div>

      <div class="user">
         <?php
            $select_user = $conn->prepare("SELECT * FROM `usuario` WHERE id = ?");
            $select_user->execute([$user_id]);
            if($select_user->rowCount() > 0){
               while($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)){
                  echo '<p>Bienvenido! <span>'.$fetch_user['nombre'].'</span></p>';
                  echo '<a href="index.php?logout" class="btn">cerrar sesión</a>';
               }
            }else{
               echo '<p><span>¡No has iniciado sesión ahora!</span></p>';
            }
         ?>
      </div>

      <div class="display-orders">
         <?php
            $select_cart = $conn->prepare("SELECT * FROM `carrito` WHERE usuario_id = ?");
            $select_cart->execute([$user_id]);
            if($select_cart->rowCount() > 0){
               while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                  echo '<p>'.$fetch_cart['nombre'].' <span>('.$fetch_cart['precio'].' x '.$fetch_cart['cantidad'].')</span></p>';
               }
            }else{
               echo '<p><span>¡Tu carrito esta vacío!</span></p>';
            }
         ?>
      </div>

      <div class="flex">

         <form action="user_login.php" method="post">
            <h3>Inicia sesión</h3>
            <input type="email" name="email" required class="box" placeholder="introduce tu correo electrónico" maxlength="50">
            <input type="password" name="pass" required class="box" placeholder="ingresa tu contraseña" maxlength="20">
            <input type="submit" value="inicia sesión" name="login" class="btn">
         </form>

         <form action="" method="post">
            <h3>regístrate ahora</h3>
            <input type="text" name="name" oninput="this.value = this.value.replace(/\s/g, '')" required class="box" placeholder="ingrese su nombre de usuario" maxlength="20">
            <input type="email" name="email" required class="box" placeholder="ingrese su correo" maxlength="50">
            <input type="password" name="pass" required class="box" placeholder="ingrese su contraseña" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" required class="box" placeholder="confirme su contraseña" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="registrate ahora" name="register" class="btn">
         </form>

      </div>

   </section>

</div>

<!--my-orders-->
<div class="my-orders">

   <section>

      <div id="close-orders"><span>cerrar</span></div>

      <h3 class="title"> mis pedidos </h3>

      <?php
         $select_orders = $conn->prepare("SELECT * FROM `pedidos` WHERE usuario_id = ?");
         $select_orders->execute([$user_id]);
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){   
      ?>
      <div class="box">
         <p> fecha de pedido : <span><?= $fetch_orders['fecha_pedido']; ?></span> </p>
         <p> nombre : <span><?= $fetch_orders['nombre']; ?></span> </p>
         <p> numero : <span><?= $fetch_orders['numero']; ?></span> </p>
         <p> direccion : <span><?= $fetch_orders['direccion']; ?></span> </p>
         <p> método de pago : <span><?= $fetch_orders['metodo']; ?></span> </p>
         <p> total productos : <span><?= $fetch_orders['total_productos']; ?></span> </p>
         <p> total precio : <span>$<?= $fetch_orders['total_precio']; ?>/-</span> </p>
         <p> estado de pago : <span style="color:<?php if($fetch_orders['estado_pago'] == 'pending'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_orders['estado_pago']; ?></span> </p>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no a ordenado todavía!</p>';
      }
      ?>

   </section>

</div>

<!--shopping-cart-->
<div class="shopping-cart">

   <section>

      <div id="close-cart"><span>cerrar</span></div>

      <?php
         $grand_total = 0;
         $total_products = ''; // Inicializar la variable para evitar el error
         $select_cart = $conn->prepare("SELECT * FROM `carrito` WHERE usuario_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
              $sub_total = ($fetch_cart['precio'] * $fetch_cart['cantidad']);
              $grand_total += $sub_total; 
      ?>
      <div class="box">
         <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('¿eliminar este artículo del carrito?');"></a>
         <img src="img_subidas/<?= $fetch_cart['imagen']; ?>" alt="">
         <div class="content">
          <p> <?= $fetch_cart['nombre']; ?> <span>(<?= $fetch_cart['precio']; ?> x <?= $fetch_cart['cantidad']; ?>)</span></p>
          <form action="" method="post">
             <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
             <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['cantidad']; ?>" onkeypress="if(this.value.length == 2) return false;">
               <button type="submit" class="fas fa-edit" name="update_qty"></button>
          </form>
         </div>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty"><span>¡Tu carrito esta vacío!</span></p>';
      }
      ?>

      <div class="cart-total"> total general : <span>$<?= $grand_total; ?>/-</span></div>

      <a href="#order" class="btn">ordenar ahora</a>

   </section>

</div>





<!--SLIDER 1-->
<div class="header-content container">

<div class="swiper mySwiper-1">
    <div class="swiper-wrapper">

        <div class="swiper-slide">
            <div class="slider">
               <div class="slider-txt">
                 <h1>Hamburguesa</h1>
                 <p>Sumérgete en una experiencia gastronómica incomparable con nuestra Hamburguesa Sabor Supremo. 
                    Cada bocado es una explosión de sabor y textura, diseñada para satisfacer tus antojos más exigentes.
                 </p>
                 <div class="botones">
                     <a href="#order" class="btn-1">Comprar</a>
                     <a href="#menu" class="btn-1">Menu</a>
                 </div>
               </div>
               <div class="slider-img">
                  <img src="img/food1.png" alt="">
               </div>
            </div>
        </div>   
        <div class="swiper-slide">
            <div class="slider">
               <div class="slider-txt">
                 <h1>Burritos</h1>
                 <p>Deliciosa carne asada marinada en especias tradicionales, acompañada de frijoles negros, 
                    arroz esponjoso, queso fundido, y guacamole fresco, todo envuelto en una tortilla de harina suave y calentita.
                 </p>
                 <div class="botones">
                     <a href="#order" class="btn-1">Comprar</a>
                     <a href="#menu" class="btn-1">Menu</a>
                 </div>
               </div>
               <div class="slider-img">
                  <img src="img/food4.png" alt="">
               </div>
            </div>
        </div>    
        <div class="swiper-slide">
            <div class="slider">
               <div class="slider-txt">
                 <h1>Pizzas</h1>
                 <p>Una auténtica delicia italiana, con salsa de tomate fresco, mozzarella de búfala, hojas de albahaca
                   y un toque de aceite de oliva virgen extra, sobre una base de masa artesanal crujiente.
                 </p>
                 <div class="botones">
                     <a href="#order" class="btn-1">Comprar</a>
                     <a href="#menu" class="btn-1">Menu</a>
                 </div>
               </div>
               <div class="slider-img">
                  <img src="img/food7.png" alt="">
               </div>
            </div>
        </div>   
     
    </div>
    <div class="swiper-button-next"></div><!--flechas de a lado de la imagen derecha-->
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
</div>

</div>




<!-- about section starts  -->
<section class="about" id="about">

   <h1 class="heading">conócenos</h1>

   <div class="box-container">

      <div class="box">
         <img src="img/about-1.svg" alt="">
         <h3>hecho con amor</h3>
         <p> Creemos que la comida rápida no tiene por qué ser impersonal ni carente de calidad. Nuestro compromiso es ofrecerte platos deliciosos y de calidad.</p>
         <a href="#menu" class="btn">nuestro menú</a>
      </div>

      <div class="box">
         <img src="img/about-2.svg" alt="">
         <h3>entrega en 30 minutos</h3>
         <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Illum quae amet beatae magni numquam facere sit. Tempora vel laboriosam repudiandae!</p>
         <a href="#menu" class="btn">nuestro menú</a>
      </div>

      <div class="box">
         <img src="img/about-3.svg" alt="">
         <h3>compartir con amigos</h3>
         <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Illum quae amet beatae magni numquam facere sit. Tempora vel laboriosam repudiandae!</p>
         <a href="#menu" class="btn">nuestro menú</a>
      </div>

   </div>

</section>
<!-- about section ends -->


<!-- menu section starts  -->
<section id="menu" class="menu">

   <h1 class="heading">nuestro menú</h1>

   <div class="box-container">

      <?php
         $select_products = $conn->prepare("SELECT * FROM `productos`");
         $select_products->execute();
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){    
      ?>
      <div class="box">
         <div class="price">$<?= $fetch_products['precio'] ?>/-</div>
         <img src="img_subidas/<?= $fetch_products['imagen'] ?>" alt="">
         <div class="name"><?= $fetch_products['nombre'] ?></div>
         <form action="" method="post">
            <input type="hidden" name="pid" value="<?= $fetch_products['id'] ?>">
            <input type="hidden" name="name" value="<?= $fetch_products['nombre'] ?>">
            <input type="hidden" name="price" value="<?= $fetch_products['precio'] ?>">
            <input type="hidden" name="image" value="<?= $fetch_products['imagen'] ?>">
            <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
            <input type="submit" class="btn" name="add_to_cart" value="añadir al carrito">
         </form>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Aún no se han añadido productos!</p>';
      }
      ?>

   </div>

</section>
<!-- menu section ends -->



<!-- order section starts  -->
<section class="order" id="order">

   <h1 class="heading">ordenar ahora</h1>

   <form action="" method="post">

   <div class="display-orders">

   <?php
         $grand_total = 0;
         $cart_item = array(); // Inicializar el array
         $total_products = ''; // Inicializar la variable para evitar el error
         $select_cart = $conn->prepare("SELECT * FROM `carrito` WHERE usuario_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
              $sub_total = ($fetch_cart['precio'] * $fetch_cart['cantidad']);
              $grand_total += $sub_total; 
              $cart_item[] = $fetch_cart['nombre'].' ( '.$fetch_cart['precio'].' x '.$fetch_cart['cantidad'].' ) - ';
              $total_products = implode($cart_item);
              echo '<p>'.$fetch_cart['nombre'].' <span>('.$fetch_cart['precio'].' x '.$fetch_cart['cantidad'].')</span></p>';
            }
         }else{
            echo '<p class="empty"><span>¡Tu carrito esta vacío!</span></p>';
         }
      ?>

   </div>

      <div class="grand-total"> total general : <span>$<?= $grand_total; ?>/-</span></div>

      <input type="hidden" name="total_products" value="<?= $total_products; ?>">
      <input type="hidden" name="total_price" value="<?= $grand_total; ?>">

      <div class="flex">
         <div class="inputBox">
            <span>nombre :</span>
            <input type="text" name="name" class="box" required placeholder="introduzca su nombre" maxlength="20">
         </div>
         <div class="inputBox">
            <span>número :</span>
            <input type="number" name="number" class="box" required placeholder="ingresa tu número" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;">
         </div>
         <div class="inputBox">
            <span>método de pago</span>
            <select name="method" class="box">
               <option value="cash on delivery">cash on delivery</option>
               <option value="credit card">credit card</option>
               <option value="paytm">paytm</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>dirección 01 :</span>
            <input type="text" name="flat" class="box" required placeholder="e.g. flat no." maxlength="50">
         </div>
         <div class="inputBox">
            <span>dirección 02 :</span>
            <input type="text" name="street" class="box" required placeholder="e.g. street name." maxlength="50">
         </div>
         <div class="inputBox">
            <span>codigo pin:</span>
            <input type="number" name="pin_code" class="box" required placeholder="e.g. 123456" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;">
         </div>
      </div>

      <input type="submit" value="order now" class="btn" name="order">

   </form>

</section>
<!-- order section ends -->


<!--horario-->
<section class="horario">
        <div class="horario-info container">

         <h1 class="heading">Horario</h1>

         <div class="horario-txt">
            <div class="txt">
                <h4>Dirección</h4>
                <p>
                    Calle 123, Ciudad de México
                </p>
                <p>
                   Entre Avenida Principal y Calle Secundaria
                </p>
            </div>
            <div class="txt">
                <h4>Horario</h4>
                <p>
                    Lunes a Viernes de: 9 am a 8 pm
                </p>
                <p>
                    Sábados a Domingos de: 11 am a 7 pm
                </p>
            </div>
            <div class="txt">
                <h4>Teléfono</h4>
                <p>
                    (555) 123-4567
                </p>
                <p>
                    (555) 123-4567
                </p>
            </div>
            <div class="txt">
                <h4>Redes Sociales</h4>
                <div class="socials">
                    <a href="#">
                        <div class="social">
                           <img src="img/s1.svg" alt="">
                        </div>
                    </a>
                    <a href="#">
                        <div class="social">
                           <img src="img/s2.svg" alt="">
                        </div>
                    </a>
                    <a href="#">
                        <div class="social">
                           <img src="img/s3.svg" alt="">
                        </div>
                    </a>
                 </div> 
            </div>
         </div>

        </div>
    </section>

    <!-- MAPA-->
    <section class="mapa">

        <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d240864.08481701676!2d-99.14361265000001!3d19.3907336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85ce0026db097507%3A0x54061076265ee841!2sCiudad%20de%20M%C3%A9xico%2C%20CDMX!5e0!3m2!1ses!2smx!4v1714339528790!5m2!1ses!2smx" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe><!--cmabiamos el width= 100%-->

    </section>

    <div class="credit">
    &copy; <?= date('Y'); ?> por <span>Brenda, Diseñadora Web</span> | ¡Todos los derechos reservados!
    </div>



<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js" ></script><!--libreria swiper-->

<script src="js/script.js"></script>

</body>
</html>