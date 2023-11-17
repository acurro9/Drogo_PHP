<?php 
//Para realizar el login del admin

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos
    require './includes/funciones.php';
    incluirTemplate('header');
    require './includes/config/database.php';
    $db=conectarDB();

    //Se crea un vector para los errores
    $errores=[];
    //Se inicializan las variables vacias
    $usuario='';
    $contraseñaUsuario='';
    // En caso de realiza el POST
    if ($_SERVER['REQUEST_METHOD']==="POST"){
        // Se guardan los datos del formulario
        $usuario=mysqli_real_escape_string($db, $_POST['usuario']);
        $contraseña=mysqli_real_escape_string($db, $_POST['password']);
        
        // Si falta algún dato se crea un error
        if(!$usuario){
            $errores[] = "Debes introducir un nombre de usuario.";
        }
        if(!$contraseña){
            $errores[] = "Debes introducir la contraseña.";
        }
        // Si no hay errores se realiza la query para seleccionar los datos del usuario introducido
        if(empty($errores)){
            $query = "SELECT * from usuario where username like '$usuario' or email like '$usuario';";
            $resultado = mysqli_query($db, $query);

            if ($resultado->num_rows){
                //Se revisa si el password es correcto comparandolo con la contraseña hasheada de la base de datos
                while($user=mysqli_fetch_assoc($resultado)){
                    $auth=password_verify($contraseña, $user["password_hash"]);
                    // Si el usuario y contraseña coindicen se guardan varios datos de la sesión para usarlos más adelante
                    if($auth && $user['tipo']==='Administrador'){
                        session_start();
                        $_SESSION["contraseña"]=$user["password_hash"];
                        $_SESSION["usuario"]=$user["email"];
                        $_SESSION["tipo"]=$user["tipo"];
                        $_SESSION["login"]=true;
                        $_SESSION["id"]=$user["id"];
                        //Se redirige al area personal del administrador
                        header('Location: /areaPersonalAdmin.php');
                    } else{
                    $errores[] = "No eres administrador, inicia sesión como usuario.";
                    }
                }
            } else{
                $errores[] = "El usuario no existe.";
            }
        }
    }

    foreach($errores as $error){ 
?>
    <!-- Se imprimen los errores si los hay -->
    <div class="alerta error">
        <?php echo $error;?>
    </div>
        
<?php
    }
?>

    <!--Se importan los css necesarios-->
    <link rel="stylesheet" href="./css/login.css">
    <link rel="stylesheet" href="./css/loginAdmin.css">

    <!-- Formulario del login del administrador -->
    <form method="POST" action="/loginAdmin.php" enctype="multipart/form-data">
        <h4 class="adminTitle">Área de administrador</h4>    
        <h3>Inicio de sesión</h3>

        <label for="usuario">Usuario:</label>
        <input type="text" placeholder="Nombre de usuario o Email" id="usuario" name="usuario">

        <label for="password">Contraseña:</label>
        <div class="mostrar">
            <input type="password" placeholder="Password" id="cont" name="password">
            <button type="button" name="password" onclick="clickBoton('cont', 'vis')">
                <p id="vis">
                    <img src="./assets/icons/bloq.svg" alt="">
                </p>
            </button>
        </div>

        <div class="user_actions">
        <!-- Botón para iniciar sesión -->
        <input type="submit" value="Iniciar Sesión" class="boton">
        <!-- Botón para ir al registro -->
            <p>¿Aún no tienes cuenta? Regístrate <span><a href="registro.php">aquí</a></span></p>
        </div>

    </form>
    <!-- Se incluye los archivos de javascript -->
    <script src="./js/contraseña.js"></script>
    <script src="./js/index.js"></script>
</body>
</html>