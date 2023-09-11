<section class="Main">

  <?php

  if (!isset($_GET['page']))
    redirect_to();

  if (in_array($_GET['page'], ADMIN_PAGES)) {

    if (is_user_logged()) {

      require_once "inc/pages/admin/$_GET[page].php";

    }

    redirect_to();

  }

  if (!in_array($_GET['page'], PAGES)) {

    redirect_to('404/');

  } else {

    require_once "inc/pages/$_GET[page].php";

  } ?>

</section>