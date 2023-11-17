<?php 
//Para el resgistro

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos
    require './includes/funciones.php';
    incluirTemplate('header');
    require './includes/config/database.php';
    $db=conectarDB();

    //Se crea un vector para los errores
    $errores=[];
    //Se inicializan las variables vacias
    $usuario='';
    $email='';
    $contraseña= '';
    $contraseña2='';
    $tipo='';
    $mensaje='';
    // En caso de hacer el POST
    if ($_SERVER['REQUEST_METHOD']==="POST"){
        // Se guardan los datos del formulario
        $usuario=mysqli_real_escape_string($db, $_POST['usuario']);
        $email=mysqli_real_escape_string($db, $_POST['email']);
        $contraseña=mysqli_real_escape_string($db, $_POST['password']);
        $contraseña2=mysqli_real_escape_string($db, $_POST['password2']);
        $tipo=mysqli_real_escape_string($db, $_POST['tipo']);
        
        //Se consultan el username y el correo
        $consulta="SELECT username, email FROM usuario;";
        $resConsulta=mysqli_query($db, $consulta);
        
        // Se comprueba si el nombre de usuario o correo ya existe en la base de datos
        while($user=mysqli_fetch_assoc($resConsulta)){
            if($user['username']==$usuario){
                $errores[] = "El nombre de usuario ya existe.";
            }
            if($user['email']==$email){
                $errores[] = "El correo electrónico ya existe.";
            }
        }
        // En caso de que falte algún dato se crea un error
        if(!$usuario){
            $errores[] = "Debes introducir un nombre de usuario.";
        }
        if(!$email){
            $errores[] = "Debes introducir un email.";
        }
        if(!$contraseña){
            $errores[] = "Debes introducir la contraseña.";
        }
        if(!$contraseña2){
            $errores[] = "Debes repetir la contraseña.";
        }
        if(!$tipo){
            $errores[] = "Debes introducir el tipo de usuario que eres.";
        } else if($tipo!=1 && $tipo!=2 && $tipo!=3 && $tipo!=4){
            $errores[] = "Debes elegir un tipo de usuario existente.";
        }

        // Se compara las contraseñas que sean iguales
        if(strcmp($contraseña, $contraseña2)!==0){
            $errores[]= "Las contraseñas no coinciden";
        }
        // En caso de que no haya errores
        if(empty($errores)){
            // Se crea un hash para la id
            $codigo=md5(uniqid(rand(),true));
            // Se hashea la contraseña
            $passwordHash=password_hash($contraseña, PASSWORD_DEFAULT);
            // Se realiza la inserción con los datos introducidos
            $query="INSERT INTO usuario (id, username, email, password_hash, tipo) values 
            ('$codigo', '$usuario', '$email', '$passwordHash', $tipo);";
            $resultado=mysqli_query($db, $query);
            if($resultado){
                // En caso de que el tipo de usuario sea admin te redirige al login del admin
                if($tipo==4){
                header('Location: /loginAdmin.php');
                // En caso de que se registre pero no sea admin redirige a la pagina para incluir la cartera
                }else{
                header('Location:/registro2.php?tipo='.$tipo.'&usuario='.$usuario);
                }
            }
        }
    }

?>
    <!--Se importan los css necesarios-->
    <link rel="stylesheet" href="./css/registro.css">
<main>
    <!-- Formulario para el registro -->
<form method="POST" action="/registro.php" enctype="multipart/form-data">
    <h3>Registro</h3>

    <?php foreach($errores as $error){ ?>
        <!-- Se imprimen los errores si existen -->
        <div style="color: red;">
            <?php echo $error; ?>
        </div>

    <?php } ?>

    <label for="usuario">Usuario:</label>
    <input type="text" placeholder="Nombre de usuario" id="usuario" name="usuario" value="<?php echo $usuario; ?>">

    <label for="email">Email:</label>
    <input type="email" placeholder="Correo Electrónico" id="email" name="email" value="<?php echo $email; ?>">

    <label for="password">Contraseña:</label>
    <div class="mostrar">
        <input type="password" placeholder="Password" id="cont1" name="password" value="<?php echo $contraseña; ?>">
        <!-- Botón para ver u ocultar la contraseña -->
        <button type="button" name="password" onclick="clickBoton('cont1', 'vis1')">
            <p id="vis1">
                <img src="./assets/icons/bloq.svg" alt="">
            </p>
        </button>
    </div>

    <label for="password2">  Repetir constraseña:</label>
    <div class="mostrar">
        <input type="password" placeholder="Password" id="cont2" name="password2" value="<?php echo $contraseña2; ?>">
        <!-- Botón para ver u ocultar la contraseña -->
        <button type="button" name="password" onclick="clickBoton('cont2', 'vis2')">
            <p id="vis2">
                <img src="./assets/icons/bloq.svg" alt="">
            </p>
        </button>
    </div>

    <label for="tipo">Seleccione su perfil: </label>
    <!-- Desplegable para indicar el tipo de usuario (el admin no se puede crear con el formulario, se hace desde la base de datos) -->
    <select name="tipo">
        <option value=""></option>
        <option <?php echo $tipo==1?'selected':'';?> value="1">Comprador</option>
        <option <?php echo $tipo==2?'selected':'';?> value="2">Vendedor</option>
        <option <?php echo $tipo==3?'selected':'';?> value="3">Distribuidor</option>
    </select>

    <div class="user_actions">
        <input type="submit" value="Registrarme" class="registro">
        <!-- Botón para ir al registro -->
        <p>¿Ya tienes cuenta? Entra <a href="login.php">aquí</a></p>
    </div>
</form>
</main>
    <!-- Se incluye los archivos de javascript -->
    <script src="./js/contraseña.js"></script>
</body>
</html>
