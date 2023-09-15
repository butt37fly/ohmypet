<?php

custom_session_start();

$id = $_SESSION['user']['id'];
$name = $_SESSION['user']['name'];
$last_name = $_SESSION['user']['last_name'];
$email = $_SESSION['user']['email'];

$orders = get_orders($id); ?>

<section class="Profile">
  <aside class="Profile__aside">
    <div class="Profile__header">
      <h1 class="Profile__name">
        <?php echo trim("$name $last_name") ?>
      </h1>
      <h3 class="Profile__email">
        <i class="fa fa-envelope Icon"></i>
        <?php echo $email ?>
      </h3>
    </div>
    <div class="Profile__actions">

      <?php if ($id === 1): ?>

        <a href="<?php echo SITE_URI . "products/" ?>" class="Button Button--orange">Productos</a>
        <a href="<?php echo SITE_URI . "categories/" ?>" class="Button Button--orange">Categorías</a>

      <?php endif; ?>

      <a href="<?php echo SITE_URI . "c/account/logout/" ?>" class="Button Button--black">Cerrar sesión</a>
    </div>
  </aside>
  <section class="Profile__info">

    <?php if (!empty($orders)): ?>

      <h1 class="Profile__title">Tus ordenes</h1>

      <?php require_once "inc/parts/orders.php" ?>

    <?php else: ?>

      <h3 class="Profile__title">Parece que aún no tienes ordenes registradas.</h3>

    <?php endif; ?>

  </section>
</section>