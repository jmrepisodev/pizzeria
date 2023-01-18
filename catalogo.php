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

  

    require_once ("conectar.php");

    try{
        $stmt = $dbh->prepare("SELECT * FROM pizzas");
        $stmt->execute();

        $pizzas = $stmt->fetchAll();
        // print_r($pizzas);

        $num_filas=count($pizzas);

    }catch(PDOException $e) {
        $errores[]= $e->getMessage();
    }


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
        
            <div class="row">
            
                <div> <!-- catalogo pizzas -->
                    <h1 class="text-center">Elige tu pizza</h1>
                    <?php

                    $columns = 3;     
                    echo '<div class="row p-3">';
                    if(isset($num_filas) && $num_filas>0 && $pizzas!==false){for ($i = 0; $i < count($pizzas); $i++) {
                            if($i % $columns === 0 && $i > 0) { //si hay más de 3 columnas, pasamos a la siguiente fila
                                echo '</div> 
                                <div class="row p-3">';
                            }
                        if($pizzas[$i]['id']==1){
                        echo '<div class="col-sm-4 mb-3"> 
                                    <div class="card bg-secondary mx-auto" style="max-width: 20rem; height:auto;"> 
                                        <img class="card-img-top img-fluid" src="'.$pizzas[$i]['imagen'].'" alt="pizza">
                                        <div class="card-body"> 
                                            <h3 class="card-title">'.$pizzas[$i]['nombre'].'</h3>
                                            <p class="card-text">'.$pizzas[$i]['descripcion'].'</p>';

                                        echo '<form action="./crea_tu_pizza.php" method="post">
                                                <input type="hidden" name="id_pizza" value="'.$pizzas[$i]['id'].'">
                                                <button name="submit" class="btn btn-danger p-2" style="max-width:100%;">
                                                    <i class="fa fa-pen me-2"></i>
                                                    Crea tu pizza
                                                </button>
                                            </form>
                                        </div> 
                                    </div>
                                </div>';
                        }else{
                            echo '<div class="col-sm-4 mb-3"> 
                                    <div class="card mx-auto" style="max-width: 20rem; height:auto;"> 
                                        <img class="card-img-top img-fluid" src="'.$pizzas[$i]['imagen'].'" alt="pizza">
                                        <div class="card-body"> 
                                            <h3 class="card-title">'.$pizzas[$i]['nombre'].'</h3>
                                            <p class="card-text">'.$pizzas[$i]['descripcion'].'</p>';
                                            
                                    
                                        echo '<form action="./carrito.php" method="post">
                                                <div class="row">
                                                    <div class="col-sm-9">
                                                        <select name="id_talla" class="form-select mb-3" style="max-width:100%;" aria-label="Default select">';
                                                        try{
                                                            $stmt2 = $dbh->prepare("SELECT * FROM tallas WHERE id_pizza=?");
                                                            $stmt2->execute(array($pizzas[$i]['id']));
                                                    
                                                            $tallas = $stmt2->fetchAll();
                                                            // print_r($pizzas);
                                                    
                                                            $num_filas2=count($tallas);
                                                    
                                                        }catch(PDOException $e) {
                                                            $errores[]= $e->getMessage();
                                                        }
                                                            if(isset($num_filas2) && $num_filas2>0 && $tallas!==false){ foreach($tallas as $t){
                                                    echo   '<option value="'.$t['id'].'">'.$t['talla'].' '.$t['precio'].' €</option>';
                                                            }}
                                                echo    '</select>
                                                    </div>

                                                    <div class="col-sm-3 mb-3">
                                                        <input type="number" name="cantidad" value="1" class="form-control align-middle text-center" style="max-width:100%;" size="1" maxlength="4">
                                                    </div>
                                                </div> 

                                                <input type="hidden" name="id_producto" value="'.$pizzas[$i]['id'].'">
                                                <input type="hidden" name="nombre" value="'.$pizzas[$i]['nombre'].'">
                                                <input type="hidden" name="descripcion" value="'.$pizzas[$i]['descripcion'].'">
                            
                                                <button name="submit" class="btn btn-danger p-2" style="max-width:100%;">
                                                    <i class="fa fa-cart-plus me-2"></i>
                                                    Agregar al carrito
                                                </button>
                                            </form>
                                        </div> 
                                    </div>
                                </div>';

                        }
                            
                    } }
                    echo '</div>';
                        
                        ?>
                </div> <!-- catalogo pizzas -->

                

            </div>


    </div>

       
<?php require_once ("footer.php") ?>