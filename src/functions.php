<?php

require_once "db.php";

/**
 * Inicia una sesión sólo en caso de que esta no se haya iniciado con anterioridad
 */
function custom_session_start(): void
{
  if (session_status() !== PHP_SESSION_ACTIVE)
    session_start();
}

/**
 * Verifica si el usuario ha iniciado sesión 
 * 
 * @return bool
 */
function is_user_logged(): bool
{
  custom_session_start();

  return isset($_SESSION['user']) && isset($_SESSION['user']['logged']);
}

/**
 * Redirecciona al usuario a alguna página del sitio web
 * 
 * @param $page Ruta parcial del redireccionamiento, por defecto redirige al Inicio
 */
function redirect_to(string $page = "home/"): void
{
  $url = SITE_URI . "$page";

  header("Location: $url");
  die();
}

/**
 * 
 */
function check_password($password, $data)
{
  global $pdo;

  $query = "SELECT password FROM users WHERE $data[trigger] = :param";
  $sth = $pdo->prepare($query);
  $sth->bindParam('param', $data['value']);
  $sth->execute();

  $result = $sth->fetch();
  $hash = $result->password;

  if (!$hash)
    return false;

  return password_verify($password, $hash);
}

/**
 * 
 */
function server_says($code, $type, $value = "")
{
  $server_codes = array(
    "error" => [
      "000" => "Algo ha salido mal, inténtalo de nuevo.",
      "001" => "El campo <b>$value</b> está vacío.",
      "002" => "Debe ingresar un email válido.",
      "003" => "Las contraseñas no coinciden.",
      "004" => "Este <b>$value</b> ya se encuentra registrado.",
      "005" => "La contraseña debe contener mínimo <b>8</b> carácteres, sólo puedes usar letras, números y los símbolos: .-_@!#$",
      "006" => "El correo y la contraseña no coinciden."
    ],
    "check" => [
      "001" => "La cuenta se ha creado exitosamente"
    ]
  );

  $msg = $server_codes[$type][$code] ?? $value;

  custom_session_start();

  if (!isset($_SESSION['server_says'])) {
    $_SESSION['server_says'] = [];
  }

  if (in_array($msg, $_SESSION['server_says']))
    return;

  $_SESSION['server_says'] = ['msg' => $msg, "type" => $type];

}

/**
 * 
 */
function validate_input($input, $type)
{

  if ($type == "password") {
    $is_lengthy = strlen($input) >= 8;
    $is_password = preg_match('/^([a-zA-Z(0-9).-_@!#$]){8,}$/', $input);

    return $is_lengthy && $is_password;
  }

  if ($type == "slug") {
    $is_slug = preg_match('/^([a-z(0-9)-]){3,}$/', $input);

    return $is_slug;
  }

}

/**
 * 
 */
function exist_term($input, $term, $table, $where = null)
{
  global $pdo;

  $query = "SELECT $term FROM $table WHERE $term = :input";

  if ($where !== null) {
    $query .= " AND id = $where";
  }

  $sth = $pdo->prepare($query);
  $sth->bindValue(':input', $input);
  $sth->execute();
  $result = $sth->fetch();

  return $result;
}

/**
 * 
 */
function insert_values($data, $where): void
{
  global $pdo;

  $params = "";
  $values = "";
  $count = 0;

  foreach ($data as $key => $value) {

    $params .= "$key";
    $values .= ":value$count";

    $count += 1;

    if ($count !== count($data)) {
      $params .= ", ";
      $values .= ", ";
    }
  }

  $query = "INSERT INTO $where ( $params ) VALUES ( $values )";
  $sth = $pdo->prepare($query);

  $count = 0;

  foreach ($data as $key => $value) {
    $sth->bindParam(":value$count", $data[$key]);
    $count += 1;
  }

  $sth->execute();

}

# --- Account ---


/**
 * 
 */
function login_user($data)
{
  global $pdo;

  $output = [];

  $query = "SELECT id, name, last_name, email, password FROM users WHERE email = :email";
  $sth = $pdo->prepare($query);
  $sth->bindValue(':email', $data['email']);

  $sth->execute();
  $result = $sth->fetch();

  $is_correct_password = password_verify($data['password'], $result->password);

  if ($is_correct_password) {
    $output = [
      'id' => $result->id,
      'name' => $result->name,
      'last_name' => $result->last_name,
      'email' => $result->email
    ];
  }

  return $output;
}

# --- Get ---

function get_orders(int $id): array
{


  return [];
}

function get_categories(int $id = null): array
{
  global $pdo;

  $result = [];

  $query = "SELECT * FROM categories";

  if ($id !== null && $id !== 0)
    $query .= " WHERE id = $id";

  $sth = $pdo->prepare($query);

  try {
    $sth->execute();
    $result = $sth->fetchAll();
  } finally {
    return $result;
  }
}

function get_pets(int $id = null): array
{
  global $pdo;

  $result = [];

  $query = "SELECT * FROM pets";

  if ($id !== null && $id !== 0)
    $query .= " WHERE id = $id";

  $sth = $pdo->prepare($query);

  try {
    $sth->execute();
    $result = $sth->fetchAll();
  } finally {
    return $result;
  }
}

function get_products(): array
{
  global $pdo;

  $result = [];

  $query = "SELECT p.id, c.id as category_id, c.name as category_name, pet.id as pet_id, pet.name as pet_name, p.title, p.slug, p.price, p.thumb, p.post_date 
    FROM products p
    INNER JOIN categories as c
    ON c.id = category_id
    INNER JOIN pets as pet
    ON pet.id = pet_id";
  $sth = $pdo->prepare($query);

  try {
    $sth->execute();
    $result = $sth->fetchAll();
  } finally {
    return $result;
  }
}

# --- Display ---

function display_server_msg()
{
  custom_session_start();

  if (!isset($_SESSION['server_says']))
    return;

  $content = $_SESSION['server_says']['msg'];
  $type = $_SESSION['server_says']['type'];

  echo "<div class='Notice Notice--$type'><span class='Notice__content'>$content</spa n></div>";

  unset($_SESSION['server_says']);
}