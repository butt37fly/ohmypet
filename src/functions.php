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
 * @return bool Devuelve `true` si el usuario ha iniciado sesión, de lo contrario devuelve `false`
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
 * Verifica si la contraseña ingresada por el usuario corresponde con la registrada para su perfil en la db
 * 
 * @param $password Input del usuario
 * @param $data Información necesaria para la consulta del usuario
 * - `trigger` =>  Nombre de la columna en la db que se usará para relacionar la información 
 * - `value` => Valor del `triger` 
 *  
 * @return bool Devuelve `true` si la contraseña coincide, de lo contrario devuelve `false`
 */
function check_password(string $password, array $data): bool
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
 * Función general para enviar una alerta del servidor hacia el cliente usando `$_SESSION`.
 * 
 * @param $code Código del mensaje 
 * - `000` => Algo ha salido mal, inténtalo de nuevo.
 * @param $type Tipo de mensaje
 * - `error`, `check`
 * @param $value Algunos mensajes necesitan el valor de `$value` para dar información más específica al usuario, opcionalmente, si `$code` no está registrado, se renderizará el contenido de `$value`,
 * - `001`: El campo <b>`$value`</b> está vacío.
 * - `custom`: ¡Este es un mensaje personalizado!
 */
function server_says(string $code, string $type, string $value = ""): void
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
 * Valida que el `$input` del usuario sea de tipo `$type`
 * 
 * @param $input Datos enviados por el usuario
 * @param $type Tipo del input que deberá validar
 * - `password`
 * 
 * @return bool Devuelve `true` si la validación es correcta, de lo contrario devuelve `false`
 */
function validate_input(string $input, string $type): bool
{

  if ($type == "password") {
    $is_lengthy = strlen($input) >= 8;
    $is_password = preg_match('/^([a-zA-Z(0-9).-_@!#$]){8,}$/', $input);

    return $is_lengthy && $is_password;
  }

  return false;
}

/**
 * Valida si un término existe en la db
 * 
 * @param $input Término a consultar
 * @param $term Nombre de la columna en la que se consultará
 * @param $table Nombre de la tabla en la que se consultará
 * @param $where Por defecto `null`, si se especifica un `int`, verificará que el `$input` también coincida con el id `$where`
 * 
 * @return stdClass|bool Devuelve un `stdClass` con el valor almacenado en `$term` si este existe, de lo contrario devuelve `false` 
 */
function exist_term($input, $term, $table, $where = null): stdClass|bool
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
 * Función general para insertar datos en la db.
 * La función debería ser ejecutada dentro de un bloque `try`.
 * 
 * @param $data Datos a insertar
 * - [`Nombre de la columna` => "Valor correspondiente"]
 * @param $where Nombre de la tabla donde se guardarán los datos
 * 
 */
function insert_values(array $data, string $where): void
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
 * Función general para actualizar datos en la db.
 * La función debería ser ejecutada dentro de un bloque `try`.
 * 
 * @param $data Datos a actualizar
 * - [`Nombre de la columna` => "Valor correspondiente"]
 * @param $where Nombre de la tabla donde se guardarán los datos
 * 
 */
function update_values(array $data, string $where): void
{
  global $pdo;

  $params = "";
  $count = 0;

  foreach ($data['values'] as $key => $value) {
    $params .= "$key = :value$count";
    $count += 1;

    if ($count < count($data['values'])) {
      $params .= ", ";
    }
  }

  $count = 0;
  $params = trim($params);
  $query = "UPDATE $where SET $params WHERE id = $data[id]";
  $sth = $pdo->prepare($query);

  foreach ($data['values'] as $key => $value) {
    $sth->bindParam(":value$count", $data['values'][$key]);
    $count += 1;
  }

  $sth->execute();

}

/**
 * Remueve los acentos de una cadena de texto
 * 
 * @param $term Elemento a procesar
 */
function remove_accents(string $term): string
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
 * Crea un slug (Números, letras minúsuclas y -) a partir de una cadena de texto
 * 
 * @param $term Elemento a procesar
 */
function create_slug(string $term): string
{

  $slug = trim($term);
  $slug = remove_accents($slug);
  $slug = strtolower($slug);
  $slug = preg_replace('/([^a-z0-9-\s])+/', '', $slug);
  $slug = preg_replace('/\s+/', '-', $slug);

  return $slug;
}

/**
 * Asigna un nombre aleatorio al fichero y sube la imágen al servidor en la carpeta `public/img/products/`
 * La función debería ser ejecutada dentro de un bloque `try`
 * 
 * @param $file Información del fichero a ser procesado
 * 
 * @return string Devuelve el nombre final del archivo
 */
function upload_img(array $file): string
{
  $file_type = $file['type'];
  $file_name = basename($file['name']);
  $file_temp = $file['tmp_name'];
  $file_size = $file['size'];

  if (!in_array($file_type, PERMITTED_IMG_TYPE)) {
    server_says('007', 'error');
    redirect_to('products/');
  }

  $file_extension = str_replace('image/', '.', $file_type);
  $file_name = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 10);
  $file_name = "$file_name-" . date('Y-m-d-H-i-s') . $file_extension;

  $file_path = IMG_DIR . "products/$file_name";

  if ($file_size > MAX_UPLOAD_SIZE) {
    server_says('008', 'error', MAX_UPLOAD_SIZE / 1000000 . "mb");
    redirect_to('products/');
  }

  move_uploaded_file($file_temp, $file_path);
  return $file_name;
}

# --- Account ---

/**
 * Compara la información del usuario con los datos registrados en la db
 * 
 * @param $data Credenciales de inicio de sesión dadas por el usuario
 * - `email` => "foo@foo.com"
 * - `password` => "foo123_"
 * 
 * @return array Devuelve un `array` con la información del usuario si las credenciales son correctas, de lo contrario devuelve un `array` vacío
 */
function login_user(array $data): array
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

/**
 * 
 */
function get_orders(int $id): array
{


  return [];
}

/**
 * Obtiene las categorías de productos registradas en la db
 * 
 * @param $id Si el `$id` es especificado, obtendrá únicamente la categoría que coincida con este
 * 
 * @return array Devuelve un `array` con la información de la/las categorías consultadas, devulve un `array` vacío en caso de que ocurra un error o no se encuentre la categoría con el id `$id`  
 */
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

/**
 * Obtiene las mascotas registradas en la db
 * 
 * @param $id Si el `$id` es especificado, obtendrá únicamente la mascota que coincida con este
 * 
 * @return array Devuelve un `array` con la información de la/las mascotas consultadas, devulve un `array` vacío en caso de que ocurra un error o no se encuentre la mascota con el id `$id`  
 */
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

/**
 * Obtiene los productos registrados en la db
 * 
 * @param $id Si el `$id` es especificado, obtendrá únicamente el producto que coincida con este
 * 
 * @return array Devuelve un `array` con la información de el/los productos consultadas, devulve un `array` vacío en caso de que ocurra un error o no se encuentre el producto con el id `$id`  
 */
function get_products(int $id = null): array
{
  global $pdo;

  $result = [];

  $query = "SELECT p.id, c.id as category_id, c.name as category_name, pet.id as pet_id, pet.name as pet_name, p.title, p.slug, p.price, p.amount, p.thumb, p.post_date 
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

/**
 * Obtiene el ícono correspondiente para cada item de los productos
 * 
 * @param $type Tipo de ícono
 * - `pet`, `category`
 * @param $id Id de la mascota/categoría 
 * @param $pet_id En caso de que el `$type` sea `category`, se debe especificar el `$pet_id`, ya que el ícono dependerá del tipo de mascota
 * 
 * @return string Devuelve un elemento `html` con la clase correspondiente al ícono, devuelve un `string` vacío si este ícono no existe.
 */
function get_product_icon(string $type, int $id, int $pet_id = null): string
{
  $icons = [
    "pet" => [
      1 => "dog",
      2 => "cat"
    ],
    "category" => [
      1 => [
        1 => "pump-soap",
        2 => "pump-soap",
      ],
      2 => [
        1 => "bone",
        2 => "fish",
      ],
      3 => [
        1 => "futbol",
        2 => "baseball",
      ],
      4 => [
        1 => "heart-circle-plus",
        2 => "heart-circle-plus",
      ]
    ]
  ];
  $tag = "<i class='fa fa-:class Icon'></i>";

  if ($type === "pet") {
    if (isset($icons[$type][$id])) {
      return str_replace(':class', $icons[$type][$id], $tag);
    }
  }

  if ($type === "category") {
    if (isset($icons[$type][$id])) {
      if (isset($icons[$type][$id][$pet_id])) {
        return str_replace(':class', $icons[$type][$id][$pet_id], $tag);
      }
    }
  }

  return "";
}

# --- Delete ---
/**
 * Elimina un producto de la base de datos
 * 
 * @param $id Id del producto a eliminar
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

/**
 * Renderiza losm mensajes que haya enviado el servidor hacia el cliente
 */
function display_server_msg(): void
{
  custom_session_start();

  if (!isset($_SESSION['server_says']))
    return;

  $content = $_SESSION['server_says']['msg'];
  $type = $_SESSION['server_says']['type'];

  echo "<div class='Notice Notice--$type'><span class='Notice__content'>$content</spa n></div>";

  unset($_SESSION['server_says']);
}

/**
 * Renderiza las cards de los productos
 * 
 * @param $products Array con la información de los productos obtenida con la función `get_products`
 */
function display_products(array $products): void
{

  foreach ($products as $product):
    $amount = $product->amount > 0 ? "Disponible ($product->amount)" : "Agotado"; ?>

    <article class="Card Card--<?php echo $product->category_id ?>">
      <div class="Card__wrapper">
        <section class="Card__side Card__side--front" data-action="flip">
          <div class="Card__content">
            <div class="Card__thumb">
              <img src="<?php echo SITE_URI . "public/img/products/$product->thumb" ?>" alt="<?php echo $product->title ?>"
                class="Card__img">
            </div>
            <h2 class="Card__title Card__title--shorted">
              <?php echo $product->title ?>
            </h2>
            <div class="Card__info Card__info--row">
              <span class="Card__item">
                <i class="fa-solid fa-sack-dollar"></i>
                <?php echo "$$product->price/u" ?>
              </span>
              <span class="Card__item">
                <i class="fa-solid fa-bag-shopping"></i>
                <?php echo $amount ?>
              </span>
            </div>
          </div>
        </section>
        <section class="Card__side Card__side--back">
          <div class="Card__content Card__content--filled">
            <h2 class="Card__title">
              <?php echo $product->title ?>
            </h2>
            <div class="Card__info">
              <span class="Card__item Card__item--big">
                <?php echo get_product_icon('pet', $product->pet_id); ?>
                <?php echo "Para: <b>$product->pet_name</b>" ?>
              </span>
              <span class="Card__item Card__item--big">
                <?php echo get_product_icon('category', $product->category_id, $product->pet_id); ?>
                <?php echo "Categoría: <b>$product->category_name</b>" ?>
              </span>
              <span class="Card__item Card__item--big">
                <i class="fa-solid fa-sack-dollar"></i>
                <?php echo "$$product->price/u" ?>
              </span>
              <span class="Card__item Card__item--big">
                <i class="fa-solid fa-bag-shopping"></i>
                <?php echo $amount ?>
              </span>
            </div>

            <?php if ($product->amount > 0): ?>

              <form class="Add-to-cart" action="<?php echo SITE_URI . "c/cart/" ?>" method="POST">
                <input type="hidden" name="id" value="<?php echo $product->id ?>">
                <label for="amount-<?php echo $product->id ?>">Cantidad</label>
                <div class="Form__field Form__field--row">
                  <input class="Form__input Form__input--add-to-cart" type="number" name="amount"
                    id="amount-<?php echo $product->id ?>" min="1" max="<?php echo $product->amount ?>">
                  <input class="Button" type="submit" value="Añadir">
                </div>
              </form>

            <?php endif; ?>

            <section class="Card__actions">
              <button class="Button Card__action" data-action="share"
                data-tooltip="Haz click para copiar el enlace de este producto"
                data-link="<?php echo SITE_URI . "store/product/$product->slug/" ?>">
                <i class="fa-solid fa-share-from-square"></i>
              </button>
              <button class="Button" data-action="flip" data-tooltip="Haz click para regresar">
                <i class="fa-solid fa-rotate-left"></i>
              </button>
            </section>
          </div>
        </section>
      </div>
    </article>

  <?php endforeach;

}