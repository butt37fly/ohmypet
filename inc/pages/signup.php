<section class="Form-container">
  <div class="Form-container__header">
    <h2>Crear cuenta</h2>
    <h5>¿Ya tienes una? haz click <a href="<?php echo SITE_URI ."login/" ?>">aquí</a> para iniciar sesión.</h5>
  </div>
  <form class="Form" method="POST" action="<?php echo SITE_URI ."c/account/" ?>">
    <div class="Form__section">
      <div class="Form__field">
        <label for="name">¿Cuál es tu primer nombre?</label>
        <input class="Form__input" id="name" type="text" name="name" placeholder="Tu nombre" required> 
      </div>
      <div class="Form__field">
        <label for="last_name">¿Cuál es tu apellido?</label>
        <input class="Form__input" id="last_name" type="text" name="last_name" placeholder="Tu apellido"> 
      </div>
    </div>
    <div class="Form__section">
      <div class="Form__field">
        <label for="email">¿Cuál es tu dirección de correo electrónico?</label>
        <input class="Form__input" id="email" type="email" name="email" placeholder="user@ohmypet.com" required> 
      </div>
    </div>
    <div class="Form__section">
      <div class="Form__field">
        <label for="password">Crea una contraseña segura</label>
        <input class="Form__input" id="password" type="password" name="password" required> 
      </div>
      <div class="Form__field">
        <label for="password_rep">Verifica tu contraseña</label>
        <input class="Form__input" id="password_rep" type="password" name="password_rep" required> 
      </div>
    </div>
    <div class="Form__section">
      <div class="Form__field Form__field--tips">
        <ul class="Form__tips">
          <li class="Form__tips__items">
            <i class="fa-sharp fa-solid fa-check-circle"></i>
            <p>Las contraseñas coinciden.</p>
          </li>
          <li class="Form__tips__items">
            <i class="fa-sharp fa-solid fa-check-circle"></i>
            <p>Tu contraseña tiene 8 carácteres o más.</p>
          </li>
          <li class="Form__tips__items">
            <i class="fa-sharp fa-solid fa-check-circle"></i>
            <p>Utiliza letras, números y carácteres especiales (.-_@!#$).</p>
          </li>
        </ul>
      </div>
    </div>
    <div class="Form__section">
      <input class="Button" type="submit" name="signin" value="Continuar"/>
    </div>
  </form>

  <?php display_server_msg(); ?>

</section>