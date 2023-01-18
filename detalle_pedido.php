<?php

require_once ("header_admin.php");

 //Formatea la fecha recibida en un campo de texto a un formato fecha d/m/y
 function formatearFecha($fecha){
    $fecha_formateada=date("d-m-Y", strtotime($fecha));
    return $fecha_formateada;
}

if(!empty($_GET["id_pedido"]) && filter_var($_GET["id_pedido"],FILTER_VALIDATE_INT)){

    $id_pedido=$_GET["id_pedido"];


    require_once 'conectar.php';


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

   

}else{
    $errores[]= "Error: ID pedido no válido.";
}


//cerrar la conexión
$dbh=null;


?>




    <div class="container">
            <?php 
               
                if(!empty($errores)){
                    echo '<div class="alert alert-danger" role="alert">';
                    foreach ($errores as $error){
                        echo "* $error"."<br>";
                    }
                    echo '</div>';
        
                }  
                
                if(isset($_GET['registrado']) && $_GET['registrado']!==false){
                    echo '<div class="alert alert-success" role="alert">';
                        echo "El pedido se ha registrado satisfactoriamente";
                    echo '</div>';
        
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
        

        
        <div class="mb-3">
            <a class="btn btn-success my-3 float-start" href="./pedidos.php">Volver</a>
        </div>

        
        
    </div>

<?php require_once ("footer_admin.php") ?>
