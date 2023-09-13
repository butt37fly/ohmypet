<?php

require_once '../db.php';
require_once '../config.php';

if (isset($_GET['a']) && $_GET['a'] === "logout") {

  custom_session_start();

  if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
  }
}

if (isset($_POST['login'])) {

  $form_input_names = [
    'email' => 'Correo electrónico',
    'password' => 'Contraseña'
  ];

  foreach ($_POST as $name => $content) {
    $input_name = $form_input_names[$name] ?? $name;

    if (empty(trim($content))) {
      server_says('001', 'error', $input_name);
      redirect_to('login/');
    }
  }

  $email = trim($_POST['email']);
  $email = filter_var($email, FILTER_VALIDATE_EMAIL);
  $password = $_POST['password'];

  if (!$email) {
    server_says('007', 'error');
    redirect_to('login/');
  }

  if (!validate_input($password, 'password')) {
    server_says('007', 'error');
    redirect_to('login/');
  }

  $user = login_user(['email' => $email, 'password' => $password]);

  if (empty($user)) {
    server_says('007', 'error');
    redirect_to('login/');
  }

  $user['logged'] = true;

  custom_session_start();

  if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = $user;
  }

  redirect_to('profile/');

}

if (isset($_POST['signin'])) {

  $form_input_names = [
    'name' => 'Nombre',
    'email' => 'Correo electrónico',
    'password' => 'Contraseña',
    'password_rep' => 'Repetir contraseña'
  ];

  foreach ($_POST as $name => $content) {

    if (key_exists($name, $form_input_names)) {

      $input_name = $form_input_names[$name];

      if (empty(trim($content))) {
        server_says('001', 'error', $input_name);
        redirect_to('signup/');
      }
    }

  }

  $name = trim($_POST['name']);
  $last_name = trim($_POST['last_name']);
  $email = trim($_POST['email']);
  $email = filter_var($email, FILTER_VALIDATE_EMAIL);
  $password = $_POST['password'];
  $password_rep = $_POST['password_rep'];

  # Validate user inputs

  if (!$email) {
    server_says('002', 'error');
    redirect_to('signup/');
  }

  if (!validate_input($password, 'password')) {
    server_says('005', 'error');
    redirect_to('signup/');
  }

  if ($password !== $password_rep) {
    server_says('003', 'error');
    redirect_to('signup/');
  }
  # Hash the user password

  $secure_password = password_hash($password, PASSWORD_BCRYPT);

  # Verify the user exists in the db

  if (exist_term($email, 'email', 'users')) {
    server_says('004', 'error', 'email');
    redirect_to('signup/');
  }

  # Try create the new user account

  try {
    insert_values([
      'name' => $name,
      'last_name' => $last_name,
      'email' => $email,
      "password" => $secure_password,
      "reg_date" => date("Y-m-d")
    ], 'users');
    server_says('001', 'check');
    redirect_to('login/');

  } catch (\Throwable $e) {
    server_says('000', 'error');
    redirect_to('signup/');
  }

}

redirect_to('');