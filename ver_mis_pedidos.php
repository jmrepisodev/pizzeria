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

    //Formatea la fecha recibida en un campo de texto a un formato fecha d/m/y
    function formatearFecha($fecha){
        $fecha_formateada=date("d-m-Y", strtotime($fecha));
        return $fecha_formateada;
    }



if(!empty($_SESSION['id_usuario']) && filter_var($_SESSION['id_usuario'],FILTER_VALIDATE_INT)){

    $id_usuario=$_SESSION['id_usuario'];

    require_once 'conectar.php';
    
    try{
        //ver motores bbdd disponibles
        //print_r(PDO::getAvailableDrivers());

        //Obtenemos los datos del pedido
        $stmt = $dbh->prepare("SELECT *, pedidos.id as id_pedido FROM usuarios, pedidos WHERE usuarios.id=pedidos.id_usuario AND usuarios.id=? ORDER BY pedidos.id;");
        $stmt->execute(array($id_usuario));
        //devuelve un array de resultados
        $result= $stmt->fetchAll(PDO::FETCH_ASSOC);

        $num_filas=$stmt->rowCount();
        


    }catch(PDOException $e) {
        $errores[]= "Error: " . $e->getMessage();
    }

}else{
    $errores[]= "Error: No existen datos de pedido";
}

//cerrar la conexión
$dbh=null;


?>


<?php require_once ("header.php") ?>

    <div class="container">
        <?php 
           
            if(!empty($errores)){
                echo '<div class="alert alert-danger" role="alert">';
                foreach ($errores as $error){
                    echo "* $error"."<br>";
                }
                echo '</div>';
    
            }    
        ?>


        <div class="row mb-3">
            <h3 class="text-center">Mis pedidos</h3>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr class="align-middle text-md-center">
                        <th>id</th>
                        <th>Usuario</th>
                        <th>telefono</th>
                        <th>Fecha</th>
                        <th>Importe</th>
                        <th>Modo pago</th>
                        <th></th>   
                    </tr>
                </thead>
                <tbody>
                <?php if(isset($num_filas) && $num_filas>0 && $result!==false){foreach($result as $row){ ?>
                        <tr class="align-middle text-md-center bg-white">
                            <td data-titulo="id"><?= $row['id_pedido'] ?></td>
                            <td data-titulo="Usuario"><?= $row['username'] ?></td>
                            <td data-titulo="Teléfono"><?= $row['telefono'] ?></td>
                            <td data-titulo="Fecha"><?php print formatearFecha($row['fecha']) ?></td>
                            <td data-titulo="Importe"><?= $row['importe'] ?> €</td>
                            <td data-titulo="Medio pago"><?= $row['modo_pago'] ?></td>   
                            <td><a class="btn btn-primary" href="./mi_detalle_pedido.php?id_pedido=<?php echo $row['id_pedido'] ?>">Ver</a></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div> 
        <div class="mb-3">
             <a class="btn btn-success my-3 float-start" href="./catalogo.php">Volver al catálogo</a>
        </div>
        
        
    </div>

<?php require_once ("footer.php") ?>
