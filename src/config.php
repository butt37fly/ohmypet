<?php
require_once "functions.php";

define('SITE_TITLE', 'OhMyPet');

define('SITE_URI', 'http://localhost/ohmypet/');
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] ."/ohmypet/");
define('IMG_DIR', ROOT_PATH .'public/img/');

define('PAGES', ['home', 'search', 'store', 'login', 'signup' ]);
define('ADMIN_PAGES', ['profile', 'products']);

define('MAX_UPLOAD_SIZE', 1500000);
define('PERMITTED_IMG_TYPE', array('image/jpeg', 'image/jpg', 'image/png', 'image/webp'));