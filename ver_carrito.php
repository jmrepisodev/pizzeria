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

        foreach($cesta as $producto){
            $contador+=$producto['cantidad'];
        }

        //$contador=count($cesta);

    }else{
        $cesta=array();
    }

  

    require_once ("conectar.php");

   


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
                <div> <!-- cesta -->
                    <div class="panel panel-default bg-white border p-3 my-5 mx-3"> 
                        <div class="panel-heading d-flex justify-content-between">
                            <div style="max-width:40%">
                                <h2>Mi cesta</h2>
                                <p> <?php print date('d-m-Y'); ?></p>
                                <a class="fs-5" href="./catalogo.php">Continuar comprando</a>
                            </div>
                            <div style="max-width:40%">
                                <a class='btn btn-danger' href='./vaciar_cesta.php'><i class='fas fa-trash me-2'></i>Vaciar cesta</a>
                            </div>
                        </div>
                        <hr>
                        <div class="panel-body p-3"> <!-- panel-body -->
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th scope="col">Artículo</th>
                                        <th scope="col">Precio</th>
                                        <th scope="col">Cantidad</th>
                                        <th>Extras</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($cesta)){ 
                                        $index=0;
                                        foreach ($cesta as $producto) { ?>
                                    <tr>
                                        <td data-titulo=""><img src="<?php print $producto['imagen'] ?>" alt="producto" class="img-fluid" style="max-width: 50px;"></td>
                                        <td data-titulo="Artículo"><?php print $producto['nombre'] ?> <br> <?php print $producto['talla'] ?> </td>
                                        <td data-titulo="Precio"><?php print $producto['precio']. ' €'; ?></td>
                                        <td data-titulo="Cantidad"><?php print $producto['cantidad'] ?></td>
                                        <td data-titulo="Extras"><?php isset($producto['ingredientes']) ? print implode(', ', $producto['ingredientes']): ""; ?> </td>
                                        <td data-titulo="Subtotal"><?php $subtotal=$producto['precio']*$producto['cantidad']; print $subtotal.' €' ?></td>
                                        <td>
                                            <a class="btn btn-success btn-sm" href="./editar.php?index=<?php print $index; ?>&plus=true; ?>"><i class="fa fa-plus"></i></a>
                                            <a class="btn btn-warning btn-sm" href="./editar.php?index=<?php print $index; ?>&minus=true; ?>"><i class="fa fa-minus"></i></a>
                                            <a class="btn btn-danger btn-sm" href="./eliminar.php?index=<?php print $index; ?>"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                        $total += $subtotal;
                                        $index++;
                                        } }else{
                                            print '<span class="fw-bold text-danger"> *No tienes artículos en la cesta </span>';
                                        }  ?>
                                </tbody>
                                <tfoot>
                                    <tr><td colspan="9"></td></tr>
                                    <tr class="fw-bold">
                                        <td>IVA 10%</td>
                                        <td ><?php $IVA=$total*0.10;  print $IVA.' €'; ?></td>
                                    </tr>
                                    <tr class="fw-bold fs-3">
                                        <td>Total</td>
                                        <td ><?php $total=$total+$IVA;  print $total.' €'; ?></td>
                                    </tr>
                                </tfoot>

                            </table>
                        
                            
                        </div><!-- panel-body -->
                        <div class="panel-footer p-3">
                            <form action="./confirmar_pedido.php" method="post">
                                <div class="row mb-3">

                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold" for="modo_entrega">Engrega:</label>
                                            <select class="form-select" style="max-width:60%;" name="modo_entrega" id="modo_entrega">
                                                <option value="local">Local</option>
                                                <option value="recoger">Recoger</option>
                                                <option value="domicilio">Domicilio</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6"> 
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Modo de pago:</label> <br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="modo_pago" value="cash" id="cash" checked>
                                                <label class="form-check-label" for="cash">
                                                    Efectivo
                                                </label>
                                            </div>
                                            
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="modo_pago" value="credit card" id="credit">
                                                <label class="form-check-label" for="credit">
                                                    Tarjeta crédito
                                                </label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="modo_pago" value="transferencia" id="transferencia">
                                                <label class="form-check-label" for="transferencia">
                                                    Transferencia
                                                </label>
                                            </div>
                                        </div>
                                        
                                        
                                    </div>

                                </div>

                                <div class="row mb-4">
                                    <div class="form-check form-check-inline ms-3">
                                        <input class="form-check-input" type="checkbox" name="legals" id="legals">
                                        <label class="form-check-label" for="legals">
                                            Acepto los términos y condiciones legales
                                        </label>
                                    </div>
                                </div>

                                <input type="hidden" name="importe" value="<?php if(isset($subtotal)){ print $subtotal;} ?>">
                                <button class='btn btn-success p-2' name="submit"><i class='fas fa-cart-plus me-2'></i>Confirmar pedido</button>
                               
                            </form>
                           
                        </div>
                    </div>
                </div> <!-- cesta -->

            </div>


    </div>

       
<?php require_once ("footer.php") ?>