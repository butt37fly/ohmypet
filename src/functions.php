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
      "006" => "El correo y la contraseña no coinciden.",
      "007" => "Extensión no soportada, intenta cargar un archivo de tipo: jpeg, jpg, png o webp.",
      "008" => "Archivo demasiado grande, el límite de subida es de <b>$value</b>."
    ],
    "check" => [
      "001" => "La cuenta se ha creado exitosamente",
      "002" => "El producto se ha <b>$value</b> correctamente"
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

/**
 * 
 */
function remove_accents($term)
{
  $term = str_replace(
    array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
    array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
    $term
  );

  $term = str_replace(
    array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
    array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
    $term
  );

  $term = str_replace(
    array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
    array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
    $term
  );

  $term = str_replace(
    array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
    array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
    $term
  );

  $term = str_replace(
    array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
    array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
    $term
  );

  $term = str_replace(
    array('ñ', 'Ñ', 'ç', 'Ç'),
    array('n', 'N', 'c', 'C'),
    $term
  );

  return $term;
}

/**
 * 
 */
function create_slug($term)
{

  $slug = trim($term);
  $slug = remove_accents($slug);
  $slug = strtolower($slug);
  $slug = preg_replace('/([^a-z0-9-\s])+/', '', $slug);
  $slug = preg_replace('/\s+/', '-', $slug);

  return $slug;
}

/**
 * 
 */
function upload_img($file): string
{
  $file_type = $file['type'];
  $file_name = basename($file['name']);
  $file_temp = $file['tmp_name'];
  $file_size = $file['size'];

  list($base, $extension) = explode('.', $file_name);

  $file_name = "$base-" . date('Y-m-d-H-i-s') . ".$extension";
  $file_path = IMG_DIR . "products/$file_name";

  if (!in_array($file_type, PERMITTED_IMG_TYPE)) {
    server_says('007', 'error');
    redirect_to('products/');
  }

  if ($file_size > MAX_UPLOAD_SIZE) {
    server_says('008', 'error', MAX_UPLOAD_SIZE / 1000000 . "mb");
    redirect_to('products/');
  }

  if (file_exists($file_path)) {
    $file_name = "$base-" . date('Y-m-d-H-i-s') . ".$extension";
    $file_path = IMG_DIR . "products/$file_name";
  }

  move_uploaded_file($file_temp, $file_path);
  return $file_name;
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

function get_products( $id = null ): array
{
  global $pdo;

  $result = [];

  $query = "SELECT p.id, c.id as category_id, c.name as category_name, pet.id as pet_id, pet.name as pet_name, p.title, p.slug, p.price, p.thumb, p.post_date 
    FROM products p
    INNER JOIN categories as c
    ON c.id = category_id
    INNER JOIN pets as pet
    ON pet.id = pet_id";

  if ($id !== null && $id !== 0)
  $query .= " WHERE p.id = $id";

  $sth = $pdo->prepare($query);

  try {
    $sth->execute();
    $result = $sth->fetchAll();
  } finally {
    return $result;
  }
}

# --- Delete ---
/**
 * 
 */
function delete_product(int $id): void
{
  global $pdo;

  $query = "DELETE FROM products WHERE id = :value";
  $sth = $pdo->prepare($query);
  $sth->bindParam(':value', $id);
  $sth->execute();

  return;
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