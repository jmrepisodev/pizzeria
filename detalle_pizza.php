<?php

require_once ("header_admin.php"); 

 //Formatea la fecha recibida en un campo de texto a un formato fecha d/m/y
 function formatearFecha($fecha){
    $fecha_formateada=date("d-m-Y", strtotime($fecha));
    return $fecha_formateada;
}

if(!empty($_GET["id_detalle"]) && filter_var($_GET["id_detalle"],FILTER_VALIDATE_INT) && !empty($_GET["id_pizza"]) && filter_var($_GET["id_pizza"],FILTER_VALIDATE_INT)){

    $id_detalle=$_GET["id_detalle"];
    $id_pizza=$_GET["id_pizza"];
   
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
        $stmt = $dbh->prepare("SELECT ingredientes.id, ingredientes.imagen, ingredientes.nombre FROM detalle_pizza, ingredientes WHERE 
             detalle_pizza.id_ingrediente=ingredientes.id AND detalle_pizza.id_detalle_pedido=?");
        $stmt->bindParam(1, $id_detalle);
        $stmt->execute();

         //devuelve un array bidireccional de datos
        $result = $stmt->fetchAll();
        // print_r($result);

        $num_filas=count($result);


    }catch(PDOException $e) {
        $errores[]= "Error: " . $e->getMessage();
    }

}else{
    $errores[]= "Error: No se ha podido acceder a los datos del pedido";
}

//cerramos los cursores
$stmt->closeCursor();
$stmt1->closeCursor();
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
            ?>

        <div class="border mb-3" >
            <div class="mx-auto" style="max-width:128px;">
                <img src="./img/logo_pizza.jpg" class="img-fluid p-3"  alt="logo">
            </div>
           
            <h3 class="text-center">Detalle pizza - ID: <?php isset($id_detalle) ? print $id_detalle : "" ?></h3>

            <div class="row p-3">
                <div class="col-sm-4">
                    <?php  if(isset($pizza) && $pizza!==false){  ?>
                    <div class="card border mb-3">
                        <div class="card-body text-center">
                                <div>
                                    <img src="<?php isset($pizza['imagen']) ? print $pizza['imagen'] : "" ?>" class="img-fluid card-img-top rounded mx-auto d-block mb-3" style="max-width: 220px;" alt="pizza">
                                    <h3 class="card-title"><?php if(isset($pizza['nombre'])){ print $pizza['nombre']; } ?></h3>
                                </div>

                                <div>
                                    <div class="mb-3">
                                    <p><?php if(isset($pizza['descripcion'])){ print $pizza['descripcion']; } ?></p>
                                    </div>
                                    
                                </div>
                        
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <div class="col-sm-8">
                <?php  if(isset($num_filas) && $num_filas>0 && $result!==false){ ?>
                    <table class="table table-bordered table-hover">
                        <h3>Ingredientes y extras</h3>
                            <thead class="table-dark">
                                <tr class="align-middle text-md-center">
                                    <th>id</th>
                                    <th>imagen</th>
                                    <th>nombre</th>
                                </tr>
                            </thead>
                        <tbody>
                        <?php foreach($result as $row){  ?>  
                            <tr class="align-middle text-md-center bg-white">
                                <td><?= $row['id'] ?></td>
                                <td><img src="<?php isset($row['imagen']) ? print $row['imagen'] : "" ?>" class="img-fluid" style="max-width: 50px;" alt="pizza"></td>
                                <td><?= $row['nombre'] ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php }else {
                                
                                echo '<div class="text-danger fs-3 mx-3"> *No contiene ingredientes extras </div>';
                        }
                ?>
                </div>
                
            </div>
    
        </div>
        

        
        <div class="mb-3">
            <a class="btn btn-success my-3 float-start" href="./pedidos.php">Volver</a>
        </div>

        
        
    </div>

<?php require_once ("footer_admin.php") ?>
