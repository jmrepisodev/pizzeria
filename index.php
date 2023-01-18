<?php

session_start();

if(!empty($_SESSION['login'])) { //si la sesión está iniciada...

        //Si la sesión no pertenece a esta app, destruimos la sesión y redirigimos a la página de login
        if(isset($_SESSION['app']) && $_SESSION['app']!='pizzeria'){
            session_destroy();
            header('Location: login.php');
            exit();
        }

        $id_usuario=$_SESSION['id_usuario'];
        $username=$_SESSION['username'];
        $rol=$_SESSION['rol'];

        if(isset($_SESSION['rol']) && $_SESSION['rol'] == "admin"){ //si es admin, redirige al panel de administración
            header('Location: admin.php');
            exit(); // termina la ejecución del script
        }


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
 

?>

<?php require_once ("header.php") ?>

<div class="container">
  
        <div class="row mb-3 d-flex justify-content-center align-items-center" style="min-height:80vh;">
            <div class="card m-3" style="max-width: 28rem; background:#FFD684">
                <div class="mx-auto" style="max-width:320px;">
                    <img src="./img/logo_pizza.jpg" class="card-img-top img-fluid p-2" alt="logo">
                </div>
               
                <div class="card-body">
                    <div class="card-title">
                        <h1 class="text-center">Pizzería Repiso</h1>
                    </div>
                    <?php if(isset($_SESSION['login'])){

                            echo "<a class='btn btn-danger p-3 text-center fs-3 mt-4' style='width: 100%;' href='./catalogo.php'><i class='fas fa-play me-2'></i></i>Realizar pedido</a>";
                    
                        }else{ 
                            echo "<a class='btn btn-success p-3 text-center fs-3 mt-4' style='width: 100%;' href='./login.php'><i class='fas fa-sign-in-alt me-2'></i></i>Login</a>";
                            
                        } 

                    ?>
                </div>
            </div>
        </div>
  

</div>



<?php require_once ("footer.php") ?>

