<?php 

require_once ("header_admin.php"); 

require_once 'conectar.php';

//Formatea la fecha recibida en un campo de texto a un formato fecha d/m/y
function formatearFecha($fecha){
    $fecha_formateada=date("d-m-Y", strtotime($fecha));
    return $fecha_formateada;
}


//Muestra todas las reservas

try{
    //ver motores bbdd disponibles
    //print_r(PDO::getAvailableDrivers());

    //Obtenemos los datos de la reserva
    $stmt = $dbh->prepare("SELECT *, pedidos.id as id_pedido FROM usuarios, pedidos WHERE usuarios.id=pedidos.id_usuario ORDER BY pedidos.id;");
    $stmt->execute();
    //devuelve un array bidireccional de datos
    $result = $stmt->fetchAll();
   // print_r($result);

    $num_filas=count($result);


}catch(PDOException $e) {
    $errores[]= "Error: " . $e->getMessage();
}

//cerramos los cursores
$stmt->closeCursor();
//cerrar la conexión
$dbh=null;

?>

<div class="container">
    <?php 
            if(!empty($errores)){
                echo '<div class="alert alert-danger m-3" role="alert">';
                foreach ($errores as $error){
                    echo "* $error"."<br>";
                }
                echo '</div>';

                echo '<a class="btn btn-success m-3" href="./admin.php">Volver</a>';

            }    

        ?>
    
            <!-- Tabla Reservas-->
        <div class="row mb-3">
            <h3 class="text-center text-uppercase">Pedidos</h3>
            <table class="table-responsive table table-bordered table-hover">
                <thead class="table-dark">
                    <tr class="align-middle text-md-center">
                        <th>id</th>
                        <th>Usuario</th>
                        <th>telefono</th>
                        <th>Fecha</th>
                        <th>Importe</th>
                        <th>Medio pago</th>
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
                            <td><a class="btn btn-primary" href="./detalle_pedido.php?id_pedido=<?php echo $row['id_pedido'] ?>">Ver</a></td>
                        </tr>
                        <?php } } ?>
                </tbody>
            </table>
        </div>
</div>

<?php  require_once ("footer_admin.php"); ?>