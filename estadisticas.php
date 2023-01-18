<?php 

require_once ("header_admin.php"); 

require_once 'conectar.php';

// --------Muestra las estadísticas--------

//Calcula las estadísticas generales en base a los datos almacenados en la base de datos
try{
    //consulta la media de días reservados, mayor número de días reservados, menor número de días, total de reservas, total días reservados y coche más reservado para cada usuario
    $stmt = $dbh->prepare("SELECT COUNT(DISTINCT pedidos.id) as total_pedidos, AVG(DISTINCT pedidos.importe) as importe_medio, 
    (SELECT nombre FROM pizzas WHERE id= (SELECT id_pizza FROM detalle_pedido GROUP BY id_pizza ORDER BY COUNT(id_pizza) DESC LIMIT 1)) as mas_pedido, 
    (SELECT nombre FROM pizzas WHERE id= (SELECT id_pizza FROM detalle_pedido GROUP BY id_pizza ORDER BY COUNT(id_pizza) ASC LIMIT 1)) as menos_pedido 
    FROM pedidos, detalle_pedido, pizzas WHERE detalle_pedido.id_pizza=pizzas.id AND detalle_pedido.id_pedido=pedidos.id");
    $stmt->execute();

    $result = $stmt->fetchAll();
    // print_r($result);
    
    //$num_filas=count($result);
    $num_filas=$stmt->rowCount();


}catch(PDOException $e) {
    $errores[]= $e->getMessage();
} 


//cerramos los cursores
$stmt->closeCursor();

   
$dbh=null; //cierra las conexiones

?>

    <div class="container p-3">

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

          <!-- Tabla Estadísticas -->
        
        <div class="row mb-3">
            <h3 class="text-center text-uppercase">Estadísticas generales</h3>

                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr class="align-middle text-md-center">
                            <th>Nº pedidos</th>
                            <th>Lo más pedido</th>
                            <th>Lo menos pedido</th>
                            <th>Importe medio</th>   
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($num_filas) && $num_filas>0 && $result!==false){foreach($result as $row){ ?>
                            <tr class="align-middle text-md-center bg-white">
                                <td data-titulo="Nº pedidos"><?= $row['total_pedidos'] ?></td>
                                <td data-titulo="Más pedido"><?= $row['mas_pedido'] ?></td>
                                <td data-titulo="Menos pedido"><?= $row['menos_pedido'] ?></td>
                                <td data-titulo="Importe medio"><?= $row['importe_medio'] ?></td>                           
                            </tr>
                        <?php }} ?>
                    </tbody>
                </table> 
        </div>


    </div>

    


  <?php  require_once ("footer_admin.php"); ?>
                
            