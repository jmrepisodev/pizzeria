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
   
    //Obtenemos el artículo de la cesta
    $index=$_GET['index'];
    $articulo=$cesta[$index];
  
    if(isset($_GET['plus']) || isset($_GET['minus'])){

        if($_GET['plus']){

            $articulo['cantidad']= $articulo['cantidad']+1;
            //Actualizamos la cesta
            $cesta[$index]=$articulo;
   
        }
    
        if($_GET['minus'] &&  $articulo['cantidad']>0){

            $articulo['cantidad']= $articulo['cantidad']-1;
             //Actualizamos la cesta
             $cesta[$index]=$articulo;

             //si la cantidad es 0, eliminamos el artículo de la cesta
            if(  $articulo['cantidad']==0){
                 //Elimina el artículo: método 2 
                array_splice($cesta,$index,1);
            }
        }


        //Establece o modifica la cookie. Una hora de duración (3600 segundos)
        setcookie('cesta', serialize($cesta), time() + 3600, "/");
    

    } //error

   
    if(isset($_GET['update']) && !empty($_GET['cantidad']) && is_numeric($_GET['cantidad'])){

        $articulo['cantidad']= $_GET['cantidad'];
        //Actualizamos la cesta
        $cesta[$index]=$articulo;

         //si la cantidad es 0, eliminamos el artículo de la cesta
         if(  $articulo['cantidad']==0){
            //Elimina el artículo: método 2 
           array_splice($cesta,$index,1);
       }
    }


    header('Location: ver_carrito.php');


}else{
    echo 'Error: se han encontrado errores';
}

?>