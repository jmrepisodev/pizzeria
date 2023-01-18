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

    
    $num_productos=0;
    $subtotal = 0;
    $IVA=0;
    $total=0;
    $contador=0;

    //Obtenemos los productos anteriores
if(isset($_COOKIE['cesta'])) {
    $cesta= unserialize($_COOKIE['cesta']);
    //$cesta=json_decode($_COOKIE['cesta']);

    $contador=count($cesta);

}else{
    $cesta=array();
}

if(!empty($_POST["id_pizza"]) && filter_var($_POST["id_pizza"],FILTER_VALIDATE_INT)){

    $id_pizza=$_POST["id_pizza"];

    $_SESSION['id_pizza']=$id_pizza;

}else if (isset($_SESSION['id_pizza'])){
        $id_pizza=$_SESSION['id_pizza'];
    
}else{
    $errores[]= "Error: ID inválida";
    exit();
}
   
    require_once 'conectar.php';

    try{
        //ver motores bbdd disponibles
        //print_r(PDO::getAvailableDrivers());

        //Obtenemos los datos de la reserva
        $stmt1 = $dbh->prepare("SELECT * FROM pizzas WHERE id=?");
        $stmt1->bindParam(1, $id_pizza);
        $stmt1->execute();

         //devuelve un array bidireccional de datos
        $pizza = $stmt1->fetch();
        // print_r($result);

    }catch(PDOException $e) {
        $errores[]= "Error: " . $e->getMessage();
    }



    try{
        //ver motores bbdd disponibles
        //print_r(PDO::getAvailableDrivers());

        //Obtenemos los datos de la reserva
        $stmt = $dbh->prepare("SELECT * FROM ingredientes");
        $stmt->execute();

         //devuelve un array bidireccional de datos
        $ingredientes = $stmt->fetchAll();
        // print_r($result);

        $num_filas=count($ingredientes);


    }catch(PDOException $e) {
        $errores[]= "Error: " . $e->getMessage();
    }


    //cerramos los cursores
    $stmt1->closeCursor();
    $stmt->closeCursor();

   


?>



<?php require_once ("header.php") ?>

    <div class="container">
        
        <?php 
            if(!empty($errores)){
                echo '<div class="alert alert-danger" role="alert my-3">';
                foreach ($errores as $error){
                    echo "* $error"."<br>";
                }
                echo '</div>';

            }    
        ?>

          
            
                <div> <!-- catalogo resultados -->
                    <a class="btn btn-primary m-3" href="./catalogo.php">Volver</a>
                    
                    <div class="row">
                            <h1 class="text-center mb-4">Elige tu pizza a tu gusto</h1>
                            
                            <div class="col-sm-6 mb-3"> 
                            <?php if(isset($pizza) && $pizza!==false){ ?>
                                <div class="card border mx-auto" style="max-width: 24rem; height:auto;">
                                    <img class="p-3 card-img-top img-fluid" src="<?php print $pizza['imagen'] ?>" alt="fruta">
                                    <div class="card-body">
                                        <h3 class="card-title"><?php print $pizza['nombre'] ?></h3>
                                        <p class="card-text"><?php print $pizza['descripcion'] ?></p>
                                        
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                       

                            <div class="col-sm-6 mb-3">
                                    <form class="border p-3 fs-4" action="./carrito.php" method="post">
                                        <div class="form-group row">
                                            <label for="talla" class="col-sm-4 col-form-label align-middle">Tamaño</label>
                                            <select name="id_talla" id="talla" class="form-select form-select-lg col-sm-8 align-middle mb-3" style="max-width:50%;" aria-label="Default select">';
                                                <?php   
                                                    require_once 'conectar.php';  
                                                    try{
                                                        $stmt2 = $dbh->prepare("SELECT * FROM tallas WHERE id_pizza=?");
                                                        $stmt2->execute(array($pizza['id']));

                                                        $tallas = $stmt2->fetchAll();
                                                        // print_r($tallas);

                                                        $num_filas2=count($tallas);

                                                    }catch(PDOException $e) {
                                                        $errores[]= $e->getMessage();
                                                    }

                                                        
                                                        //cerrar la conexión
                                                        $dbh=null;

                                                        if(isset($num_filas2) && $num_filas2>0 && $tallas!==false){ foreach($tallas as $t){?>
                                                        <option value="<?php print $t['id'] ?>"><?php print $t['talla']. ' '.$t['precio'] ?> €</option>';
                                            <?php   }} ?>
                                            </select>

                                        </div>
                                        
                                        <div class="form-group row mb-4">
                                            <label for="cantidad" class="col-sm-4 col-form-label align-middle">Cantidad</label>
                                            <input type="number" name="cantidad" value="1" class="form-control col-sm-8 align-middle text-center" id="cantidad" style="max-width:25%;" size="1" maxlength="4">
                                        </div>
                                        <hr>
                                        <h3 class="mb-3">Añade tus ingredientes favoritos:</h3>
                                        <?php   
                                        if(isset($num_filas) && $num_filas>0 && $ingredientes!==false){for ($i = 0; $i < count($ingredientes); $i++) {
                                        echo '<div class="form-check mb-3 align-middle ">
                                                <input name="ingredientes['.$ingredientes[$i]['id'].']" class="form-check-input" type="checkbox" value="'.$ingredientes[$i]['nombre'].'" id="chk-'.$ingredientes[$i]['id'].'">
                                                <label class="form-check-label" for="chk-'.$ingredientes[$i]['id'].'">
                                                    <img src="'.$ingredientes[$i]['imagen'].'" alt="imagen" class="img-fluid" style="max-width: 40px;"> '.$ingredientes[$i]['nombre'].' '.$ingredientes[$i]['precio'].' €
                                                </label>
                                            </div>';
                                        } }
                                        ?> 
                                        <input type="hidden" name="id_producto" value="<?php print $pizza['id'] ?>">
                                        <input type="hidden" name="nombre" value="<?php print $pizza['nombre'] ?>">
                                        <input type="hidden" name="descripcion" value="<?php print $pizza['descripcion'] ?>">
                                        <button name="submit" class="btn btn-danger p-2"><i class="fa fa-cart-plus me-2"></i>
                                             Agregar al carrito
                                        </button>
                                    </form>
                                        
                        </div>
                    </div>
                   
                   
                
                </div> <!-- catalogo resultados -->

                

       


    </div>

       
<?php require_once ("footer.php") ?>