<section class="Header Header--Desktop">
  <div class="Header__wrapper">
    <section class="Header__section">
      <div class="Header__logo">
        <img src="<?php echo SITE_URI . "public/img/logo.png" ?>" alt="OhMyPet logo" class="Header__logo__img">
      </div>
    </section>
    <section class="Header__section">
      <nav class="Header__nav">
        <a href="<?php echo SITE_URI . "home/" ?>" class="Header__nav_link">Inicio</a>
        <a href="<?php echo SITE_URI . "store/" ?>" class="Header__nav_link">Tienda</a>
      </nav>
    </section>
    <section class="Header__section Header__section--right">
      <a href="<?php echo SITE_URI ."cart/"?>">
        <i class="fa fa-cart-plus Icon Icon--cart" data-cart-units="empty"></i>
      </a>
      <a href="<?php echo SITE_URI ."profile/"?>">
        <i class="fa fa-user Icon"></i>
      </a>
    </section>
  </div>
</section>