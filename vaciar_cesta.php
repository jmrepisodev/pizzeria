<?php
  //unset($_COOKIE['carrito']);
  setcookie('cesta', "", time() - 3600, "/");
  header('Location: ver_carrito.php');
  exit;

?>