<?php
require_once "functions.php";

define('SITE_TITLE', 'OhMyPet');

define('SITE_URI', 'http://localhost/ohmypet/');
define('ROOT_PATH', getcwd() . '/');

define('PAGES', ['home', 'search', 'store', 'login', 'signup' ]);
define('ADMIN_PAGES', ['profile', 'categories', 'products']);

define('MAX_UPLOAD_SIZE', 1500000);