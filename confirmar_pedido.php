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

    $contador=0;
    //Obtenemos los productos anteriores
    if(isset($_COOKIE['cesta'])) {
        $cesta= unserialize($_COOKIE['cesta']);
        //$cesta=json_decode($_COOKIE['cesta']);

        foreach($cesta as $producto){
            $contador+=$producto['cantidad'];
        }

    // $contador=count($cesta);

    }else{
        $cesta=array();
    }

    /**
     * Retorna las id's de los ingredientes
     */
    function getIngredientes($ingredientes){
        
        foreach ($ingredientes as $key => $value) {
            $claves[]= $key;
            
        }

        return $claves;
    }



if(isset($_POST["submit"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

    if(empty($_POST['importe']) || empty($cesta)){
        $errores[] = "Error: No hay elementos en la cesta";
    }

    if(!isset($_POST['legals'])){
        $errores[] = "Error: Debe aceptar las condiciones legales";
    }
    
    if(!isset($_SESSION['id_usuario'])){
        $errores[] = "Error: ID usuario inválida";
    }
    
    if(empty($errores)){

        $id_usuario=$_SESSION['id_usuario'];
        $name=$_SESSION['username'];
        $rol=$_SESSION['rol'];

        $importe=$_POST["importe"];

      //!empty($_POST['modo_pago']) && !empty($_POST['modo_entrega']))
        $modo_pago=$_POST['modo_pago'];
        $modo_entrega=$_POST['modo_entrega'];
        $fecha= date('Y-m-d H:i:s');
        $comentarios=''; //valor por defecto

        require_once("conectar.php");


        if(!empty($cesta)){  //si hay prdductos en la cesta

            //Registramos la reserva
            try{
                $sth = $dbh->prepare('INSERT INTO pedidos(id_usuario, importe, fecha, modo_pago, modo_entrega, comentarios) VALUES (?, ?, ?, ?, ?, ?)');
                $sth->execute(array($id_usuario, $importe, $fecha, $modo_pago, $modo_entrega, $comentarios));

                $count= $sth->rowCount();

                //Devuelve el número de filas afectadas
                if($count>0){
                    //obtenemos el id del pedido
                    $id_pedido= $dbh->lastInsertId();
                    //Registramos los detalles del pedido
                    if(isset($id_pedido)){
                       
                                try{
                                     //insertamos los productos
                                    foreach ($cesta as $pizza) { 

                                        $id_pizza=$pizza['id'];
                                        $cantidad=$pizza['cantidad'];
                                        $id_talla=$pizza['id_talla'];

                                        //Insertamos todos los items en la base de datos
                                        $sth2 = $dbh->prepare('INSERT INTO detalle_pedido (id_pedido, id_pizza, cantidad, id_talla) VALUES (?, ?, ?, ?)');
                                        $sth2->execute(array($id_pedido, $id_pizza, $cantidad, $id_talla));

                                        $count2= $sth2->rowCount();

                                        if($count2>0 && !empty($pizza['ingredientes'])){ //si el producto contiene extras
                                            $id_detalle= $dbh->lastInsertId();

                                            $ingredientes =getIngredientes($pizza['ingredientes']);
                                            //cantida por defecto=1

                                            foreach ($ingredientes as $id_ingrediente) {  //insertamos los ingredientes extra en la base de datos   

                                                try{
                                                    $sth3 = $dbh->prepare('INSERT INTO detalle_pizza (id_detalle_pedido, id_ingrediente) VALUES (?, ?)');
                                                    $sth3->execute(array($id_detalle, $id_ingrediente));
                
                                                    $count3= $sth3->rowCount();
                                                }catch(PDOException $e) {
                                                    $errores[]= "Error1: " .$e->getMessage();
                                                }finally{
                                                    $sth3->closeCursor();
                                                }
            
                                            } 
                                        }

                                    } 

                                }catch(PDOException $e) {
                                    $errores[]= "Error: " .$e->getMessage();
                                }finally{
                                    $sth2->closeCursor();
                                } 


                    }

                    //Vaciamos la cesta
                    setcookie('cesta', "", time() - 3600, "/");

                }else{
                    $errores[] = "Error: No ha sido posible registrar el pedido";
                }

            }catch(PDOException $e) {
                $errores[]= "Error: " .$e->getMessage();
            }finally{
                $sth->closeCursor();
            } 



        }else{
            $errores[] = 'No hay artículos en la cesta';
        }     
        

    }
      

}else{
    $errores[] = "Ha ocurrido un error inesperado. No se ha podido completar el pedido";
 
}

if(empty($errores)){

    if(isset($id_pedido)){
        try{
            //ver motores bbdd disponibles
            //print_r(PDO::getAvailableDrivers());
    
            //Obtenemos los datos de la reserva
            $stmt = $dbh->prepare("SELECT *, detalle_pedido.id as id_detalle_pedido FROM detalle_pedido, pizzas, tallas WHERE detalle_pedido.id_pizza=pizzas.id AND detalle_pedido.id_talla=tallas.id AND detalle_pedido.id_pedido=?");
            $stmt->bindParam(1, $id_pedido);
            $stmt->execute();
            //devuelve una fila
            $result = $stmt->fetchAll();
            // print_r($result);
         
            $num_filas=count($result);
    
    
        }catch(PDOException $e) {
            $errores[]= "Error: " . $e->getMessage();
        }finally{
             //cerramos los cursores
            $stmt->closeCursor();
        }
    }
    

}

 //cerrar la conexión
 $dbh=null;



?>


<?php require_once ("header.php") ?>

<div class="container"> 
    <?php 
            if(!empty($errores)){
                echo '<div class="alert alert-danger m-3" role="alert">';
                foreach ($errores as $error){
                    echo "* $error"."<br>";
                }
                echo '</div>';

                echo '<a class="btn btn-success m-3" href="./ver_carrito.php">Volver</a>';

            }else{
                echo '<div class="alert alert-success m-3" role="alert">';
                echo "El pedido se ha registrado satisfactoriamente";
                echo '</div>';

                echo '<a class="btn btn-success m-3" href="./catalogo.php">Volver</a>';

            }    
           
    ?>

<div class="border mb-3" >
    <div class="mx-auto" style="max-width:128px;">
        <img src="./img/logo_pizza.jpg" class="img-fluid p-3"  alt="rent">
    </div>
    
    <h3 class="text-center">Detalle pedido - ID: <?php isset($id_pedido) ? print $id_pedido : "" ?></h3>

    <div class="row p-3">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr class="align-middle text-md-center">
                    <th>id</th>
                    <th colspan="2">Pizza</th>
                    <th>Tamaño</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Comentarios</th>
                    <th></th> 
                </tr>
            </thead>
            <tbody>
                <?php  if(isset($num_filas) && $num_filas>0 && $result!==false){foreach($result as $row){ ?>
                <tr class="align-middle text-md-center bg-white">
                    <td><?= $row['id_detalle_pedido'] ?></td>
                    <td><img src="<?php isset($row['imagen']) ? print $row['imagen'] : "" ?>" class="img-fluid" style="max-width: 50px;" alt="pizza"></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['talla'] ?></td>
                    <td><?= $row['precio'] ?> €</td>
                    <td><?= $row['cantidad'] ?></td>
                    <td>-</td>
                    <td><a class="btn btn-primary" href="./detalle_pizza.php?id_detalle=<?php print $row['id_detalle_pedido'] ?>&id_pizza=<?php echo $row['id_pizza'] ?>">Ver</a></td>
                </tr>
                <?php }}  ?>
            </tbody>
        </table>
    </div> 
    
</div>
</div>

<?php require_once ("footer.php") ?>