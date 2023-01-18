<?php

session_start();

// Si el usuario no está logueado se redirige a login.php
if (!isset($_SESSION['login']) || !isset($_SESSION['id_usuario'])) {
	header('Location: login.php');
	exit(); // termina la ejecución del script

}else{ //si la sesión está iniciada...

        $id_usuario=$_SESSION['id_usuario'];
        $username=$_SESSION['username'];
        $rol=$_SESSION['rol'];


        // Establece tiempo de vida de la sesión en segundos (10 minutos)
        $tiempoLimite = 600; 
        // Comprueba si $_SESSION["timeout"] está establecida
        if(isset($_SESSION["timeout"])){
            // Calcula el tiempo de vida de la sesión (TTL = Time To Live)= hora actual - hora inicio
            $sessionTTL = time() - $_SESSION["timeout"];
            if($sessionTTL > $tiempoLimite){
                session_unset();
                session_destroy();
                header("Location: logout.php");
                //Termina la ejecución del script
                exit(); 
            }
        }

        //Actualiza la hora de inicio de sesión
        $_SESSION["timeout"] = time();
  
}


    //Obtenemos los productos anteriores
    if(isset($_COOKIE['cesta'])) {
        $cesta= unserialize($_COOKIE['cesta']);
        //$cesta=json_decode($_COOKIE['cesta']);

        $contador=count($cesta);

    }else{
        $cesta=array();
    }


    /**
     * Comprueba si ya existe el producto en la cesta
     */
    function exists($id, $size){
        $indice=null;
        if(!empty($_COOKIE['cesta'])) {
            $cesta= unserialize($_COOKIE['cesta']);

            for ($i=0; $i<count($cesta); $i++){
                if($cesta[$i]['id']==$id && $cesta[$i]['talla']==$size && empty($cesta[$i]['ingredientes'])){
                    $indice=$i;
                }
            }
        }
        
        return $indice;
    }

    /**
     * Filtra los datos
     */
    function filtrado($datos){
        $datos = trim($datos); // Elimina espacios antes y después de los datos
        $datos = stripslashes($datos); // Elimina backslashes \
        $datos = htmlspecialchars($datos); // Traduce caracteres especiales en entidades HTML
        return $datos;
    }

   

    $contador=0;

require_once ("conectar.php");

if(isset($_POST["submit"]) && $_SERVER["REQUEST_METHOD"] == "POST") {


        if(isset($_POST['id_producto'], $_POST['cantidad'], $_POST['id_talla']) 
           && filter_var($_POST["id_producto"],FILTER_VALIDATE_INT) && filter_var($_POST["id_talla"],FILTER_VALIDATE_INT) && is_numeric($_POST['cantidad']) && $_POST['cantidad']>0){

            $id_pizza=filtrado($_POST['id_producto']);
           //obtenemos los datos del producto, según su id
            try{
                $stmt = $dbh->prepare("SELECT * FROM pizzas WHERE id=?");
                $stmt->bindParam(1, $id_pizza);
                $stmt->execute();

                $pizza = $stmt->fetch();
                // print_r($pizzas);

                $num_filas=count($pizza);

            }catch(PDOException $e) {
                $errores[]= $e->getMessage();
            }

            //Obtenemos la talla y el precio, según id_talla
            try{
                $stmt2 = $dbh->prepare("SELECT * FROM tallas WHERE id=?");
                $stmt2->execute(array($_POST["id_talla"]));
        
                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
              
                if($row>0 && $row!==false){
                    $talla=$row['talla'];
                    $precio=$row['precio'];

                }else{
                    $errores[]="Error: no se ha podido añadir el producto al carrito";
                }

            }catch(PDOException $e) {
                $errores[]= $e->getMessage();
            }
            
            $indice=exists($_POST['id_producto'], $talla);

            if(!empty($cesta)  && isset($indice) && is_numeric($indice)){  //si ya estaá en la cesta lo actualizamos

                $cesta[$indice]['cantidad']=$cesta[$indice]['cantidad']+$_POST['cantidad'];


            }else{ //introducimos el nuevo producto en la cesta

               
                if(empty($errores)){

                    $index = count($cesta);

                    $cesta[$index]['id']=$id_pizza;
                    $cesta[$index]['nombre']=$pizza['nombre'];
                    $cesta[$index]['descripcion']=$pizza['descripcion'];
                    $cesta[$index]['imagen']=$pizza['imagen'];
                    $cesta[$index]['id_talla']=filtrado($_POST['id_talla']);
                    $cesta[$index]['cantidad']=filtrado($_POST['cantidad']);
                    $cesta[$index]['talla']=$talla;
                    $cesta[$index]['precio']=$precio;

                     //si existen ingredientes extras, los añadimos a la pizza
                    if(!empty($_POST['ingredientes'])){
                        //Guardamos el array de ingredientes
                        $cesta[$index]['ingredientes']=$_POST["ingredientes"];
                    }


                }
                
            }

        
           
          // var_dump($cesta);
           $contador=count($cesta);

            //Establece o modifica la cookie. Una hora de duración (3600 segundos)
             setcookie('cesta', serialize($cesta), time() + 3600, "/");
            // setcookie('cesta', json_encode($cesta), time() + 3600, "/"); 

        }else{
            $errores[]="Error: no se ha podido añadir el producto al carrito";
        }



       
    
}

//header('Location:'.$_SERVER['HTTP_REFERER']); //Regresa a la página anterior
 header('Location: catalogo.php');

?>