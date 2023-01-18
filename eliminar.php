<?php

 //Obtenemos los productos anteriores
if(isset($_COOKIE['cesta'])) {
    $cesta= unserialize($_COOKIE['cesta']);
    //$cesta=json_decode($_COOKIE['cesta']);
}else{
    header('Location: catalogo.php');
    exit;
}

if(isset($_GET['index']) && is_numeric($_GET['index'])){
   
    $index=$_GET['index'];

    //Elimina el artículo: método 1 
   // unset($cesta[$index]);
   // $cesta=array_values($cesta);

   //Elimina el artículo: método 2 
   array_splice($cesta,$index,1);


    //Establece o modifica la cookie. Una hora de duración (3600 segundos)
    setcookie('cesta', serialize($cesta), time() + 3600, "/");

    header('Location: ver_carrito.php');

}else{
    echo 'Error: se han encontrado errores';
}

?>