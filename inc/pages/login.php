<section class="Form-container">
  <div class="Form-container__header ">
    <h2>Iniciar sesión</h2>
    <h5>¿Aún no tienes una cuenta? haz click <a href="<?php echo SITE_URI ."signup/" ?>">aquí</a>.</h5>
  </div>
  <form class="Form" method="POST" action="<?php echo SITE_URI ."c/account/"?>">
    <div class="Form__section">
      <div class="Form__field">
        <label for="email">Ingresa tu correo electrónico</label>
        <input class="Form__input" id="email" type="email" name="email" placeholder="user@ohmypet.com" autocomplete="email" required autocomplete="email"> 
      </div>
    </div>
    <div class="Form__section">
      <div class="Form__field">
        <label for="password">Ingresa tu contraseña</label>
        <input class="Form__input" id="password" type="password" name="password" autocomplete="current-password" required autocomplete="password"> 
      </div>
    </div>
    <div class="Form__section">
      <input class="Button" type="submit" name="login" value="Continuar"/>
    </div>
  </form>

  <?php display_server_msg(); ?>

</section>