<?php

require_once '../db.php';
require_once '../config.php';

if (isset($_GET['a']) && $_GET['a'] === "delete") {

  if (!is_user_logged())
    return;

  if ($_SESSION['user']['id'] !== 1)
    return;

  if (isset($_GET['id'])) {

    try {
      delete_product($_GET['id']);
      server_says('002', 'check', 'eliminado');
    } catch (\Throwable $e) {
      server_says('000', 'error');
    }
  }
}

redirect_to('products/');