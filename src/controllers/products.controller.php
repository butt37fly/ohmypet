<?php

require_once '../db.php';
require_once '../config.php';

if (isset($_GET['a']) && $_GET['a'] === "delete") {

  if (!is_user_logged())
    return;

  if ($_SESSION['user']['id'] !== 1)
    return;

  if (isset($_GET['id'])) {

    $product = get_products($_GET['id']);

    if (empty($product)) {
      server_says('000', 'error');
      redirect_to('products/');
    }

    $img = $product[0]->thumb;
    $img_path = IMG_DIR . "products/$img";

    try {
      delete_product($_GET['id']);
      if ($img !== "placeholder.png" && is_writable($img_path))
        unlink($img_path);

      server_says('002', 'check', 'eliminado');

    } catch (\Throwable $e) {
      server_says('000', 'error');
    }
  }
}

if (isset($_POST['submit'])) {

  if ($_POST['submit'] === "Guardar") {

    $form_input_names = [
      'title' => 'Título',
      'price' => 'Precio',
      'pet' => 'Mascota',
      'category' => 'Categoría'
    ];

    foreach ($_POST as $name => $content) {

      if (key_exists($name, $form_input_names)) {

        $input_name = $form_input_names[$name];

        if (empty(trim($content))) {
          server_says('001', 'error', $input_name);
          redirect_to('products/');
        }
      }

    }

    $title = trim($_POST['title']);
    $slug = create_slug($title);
    $price = $_POST['price'];
    $pet = $_POST['pet'];
    $category = $_POST['category'];

    $default_img = "placeholder.png";

    if (!exist_term($pet, 'id', 'pets')) {
      server_says('custom', 'error', 'Parece que este tipo de mascota no existe.');
      redirect_to('products/');
    }

    if (!exist_term($category, 'id', 'categories')) {
      server_says('custom', 'error', 'Parece que esta categoría no existe.');
      redirect_to('products/');
    }

    if (isset($_FILES['thumb']) && !empty($_FILES['thumb']['name'])) {

      $img = upload_img($_FILES['thumb']);
      $img = empty($img) ? $default_img : $img;

    } else {

      $img = $default_img;

    }

    try {
      insert_values([
        'category_id' => $category,
        'pet_id' => $pet,
        'title' => $title,
        "slug" => $slug,
        "price" => $price,
        "thumb" => $img,
        "post_date" => date("Y-m-d")
      ], 'products');
      server_says('002', 'check', 'creado');
      redirect_to('products/');

    } catch (\Throwable $e) {
      server_says('000', 'error');
      redirect_to('products/');
    }

  }

  if ($_POST['submit'] === "Editar") {

    $form_input_names = [
      'title' => 'Título',
      'price' => 'Precio',
      'pet' => 'Mascota',
      'category' => 'Categoría'
    ];

    foreach ($_POST as $name => $content) {

      if (key_exists($name, $form_input_names)) {

        $input_name = $form_input_names[$name];

        if (empty(trim($content))) {
          server_says('001', 'error', $input_name);
          redirect_to('products/');
        }
      }

    }

    if (!isset($_POST['id']) || empty($_POST['id'])) {
      server_says('000', 'error');
      redirect_to('products/');
    }

    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $slug = create_slug($title);
    $price = $_POST['price'];
    $pet = $_POST['pet'];
    $category = $_POST['category'];

    $data = ['id' => $id, 'values' => []];

    if (exist_term($title, 'title', 'products')) {
      server_says('004', 'error', 'nombre de producto');
      redirect_to('products/');
    }

    $data['values']['title'] = $title;
    $data['values']['slug'] = $slug;

    if (!exist_term($price, 'price', 'products', $id))
      $data['values']['price'] = $price;

    if (!exist_term($pet, 'pet_id', 'products', $id))
      $data['values']['pet_id'] = $pet;

    if (!exist_term($category, 'category_id', 'products', $id))
      $data['values']['category_id'] = $category;

    if (isset($_FILES['thumb']) && !empty($_FILES['thumb']['name'])) {

      $img = upload_img($_FILES['thumb']);

      if (!empty($img)) {
        $data['values']['thumb'] = $img;
      }
    }

    try {
      update_values($data, 'products');
      server_says('002', 'check', 'actualizado');
    } catch (\Throwable $th) {
      server_says('000', 'error');
    }

    redirect_to('products/');
  }
}

redirect_to('products/');