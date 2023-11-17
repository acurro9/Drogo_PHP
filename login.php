<?php 
//Para realizar el login

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
    // En el caso de hacer POST
    if ($_SERVER['REQUEST_METHOD']==="POST"){
        // Se guardan los datos del formulario
        /*si solo fuese por email
        $usuario=mysqli_real_escape_string($db, filter_var($_POST['usuario'], FILTER_VALIDATE_EMAIL));*/
        $usuario=mysqli_real_escape_string($db, $_POST['usuario']);
        $contraseña=mysqli_real_escape_string($db, $_POST['password']);
        // Se realiza la query para ver los usuarios bloqueados
        $consBloq="SELECT username, email from bloqueado where username like '$usuario' or email like '$usuario';";
        $resBloq=mysqli_query($db, $consBloq);
        // Se guardan los datos de la consulta
        if($fila=mysqli_fetch_row($resBloq)){
            $userBloq=$fila[0];
            $emailBloq=$fila[1];
            // En caso de que el usuario esté bloqueado se crea un error
            if($userBloq==$usuario || $emailBloq==$usuario){
                $errores[]="Cuenta de usuario bloqueada, ponte en contacto con atención al cliente";
            }
        }

        // Si falta algún dato se crea un error
        if(!$usuario){
            $errores[] = "Debes introducir un nombre de usuario.";
        }
        if(!$contraseña){
            $errores[] = "Debes introducir la contraseña.";
        }
        // Si no hay errores se realiza la query para seleccionar los datos del usuario introducido
        if(empty($errores)){
            $query = "SELECT * from usuario where email like '${usuario}' or username like '${usuario}';";
            $resultado = mysqli_query($db, $query);

            if ($resultado->num_rows){
                //Se revisa si el password es correcto comparandolo con la contraseña hasheada de la base de datos
                $user=mysqli_fetch_assoc($resultado);
                $auth=password_verify($contraseña, $user["password_hash"]);

                // En caso de introducir de username 'admin' y de contraseña '1234' nos redirige al login del admin
                if($usuario=='admin' && $contraseña=='1234'){
                    header('Location: /loginAdmin.php');
                } else{   
                    // Si el usuario y contraseña coindicen se guardan varios datos de la sesión para usarlos más adelante
                    if($auth){
                        session_start();
                        $_SESSION["usuario"]=$user["email"];
                        $_SESSION["tipo"]=$user["tipo"];
                        $_SESSION["login"]=true;
                        $_SESSION["id"]=$user["id"];
                        $_SESSION["contraseña"]=$user["password_hash"];
                        //Se redirige al area personal
                        header('Location: /areaPersonal.php');
                    } else{
                        $errores[] = "La contraseña es incorrecta.";
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
    <!-- Formulario para el inicio de sesión -->
    <form method="POST" action="/login.php" enctype="multipart/form-data">
        <h3>Inicio de sesión</h3>

        <label for="usuario">Usuario:</label>
        <input type="text" placeholder="Nombre de usuario o Email" id="usuario" name="usuario" value="<?php echo $usuario;?>">

        <label for="password">Contraseña:</label>
        <div class="mostrar">
            <input type="password" placeholder="Password" id="cont" name="password" value="<?php echo $contraseñaUsuario;?>">
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
</body>
</html>