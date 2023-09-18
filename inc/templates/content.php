<section class="Main">

  <?php

  if (!isset($_GET['page']))
    redirect_to();

  if (in_array($_GET['page'], ADMIN_PAGES)) {

    if (!is_user_logged()) {

      redirect_to('login/');

    } else {

      require_once "inc/pages/admin/$_GET[page].php";

    }
  }

  if (!in_array($_GET['page'], PAGES)) {

    require_once "inc/pages/404.php";

  } else {

    if ($_GET['page'] == 'login' || $_GET['page'] == 'signup') {

      if (is_user_logged()) {

        redirect_to('profile/');

      }

    }

    require_once "inc/pages/$_GET[page].php";

  } ?>

</section>