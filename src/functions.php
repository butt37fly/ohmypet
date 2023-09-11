<?php

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