<?php
//Para la actualización de los usuarios

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require '../includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');

    //En caso de no estar logueado y no ser el administrador nos redirige al index.php
    $auth=$_SESSION['login']??false;
    if(!$auth || $_SESSION['tipo']!='Administrador'){
        header('Location: /');
    }

    //Se guarda la id del usuario pasado por URL, si no existe devuelve al area personal del admin
    $id=$_GET['usuario']??null;
    if(!$id){
        header('Location: /areaPersonalAdmin.php');
    } else{
        // Se inicializa errores y se crea la query necesaria para rellenar los datos
        $errores=[];
        $query = "SELECT id, username, email, tipo from usuario where id='$id';";
        $resultado = mysqli_query($db, $query);

        if($fila=mysqli_fetch_row($resultado)){
            // Se guardan los datos devueltos por la consulta
            $ref=$fila[0];
            $nombre=$fila[1];
            $correo=$fila[2];
            $tip=$fila[3];
        }
        $cont="";

        // En el caso de hacer POST
        if($_SERVER['REQUEST_METHOD']==="POST"){
            // Se guardan los datos de los inputs del formulario
            $ref=mysqli_real_escape_string($db, $_POST['id']);
            $nombre=mysqli_real_escape_string($db, $_POST['nombre']);
            $correo=mysqli_real_escape_string($db, $_POST['correo']);
            $tip=mysqli_real_escape_string($db, $_POST['tip']);
            $cont=mysqli_real_escape_string($db, $_POST['password']);

            // En caso de que falte algún dato se crea el error
            if(!$ref){
                $errores[]="Debes añadir una referencia del usuario.";
            }
            if(!$nombre){
                $errores[]="Debes añadir un nombre de usuario.";
            }
            if(!$correo){
                $errores[]="Debes añadir un correo electrónico.";
            }
            if(!$tip){
                $errores[]="Debes añadir el tipo de usuario que es.";
            }

            // En caso de no haber errores
            if(empty($errores)){
                if(!$cont){
                    // En caso de no indicar la contraseña se actualiza todo menos eso
                    $consulta="UPDATE usuario set id='$ref', username='$nombre', email='$correo', tipo=$tip where id='$id';";
                } else{
                    // En caso de indicar contraseña la hasheamos y se actualiza la base de datos
                    $contHash=password_hash($cont, PASSWORD_DEFAULT);
                    $consulta="UPDATE usuario set id='$ref', username='$nombre', email='$correo', password_hash='$contHash', tipo=$tip where id='$id';";
                }

                //Se realiza la consulta, si es correcta nos devuelve a la tabla de usuarios
                $result=mysqli_query( $db, $consulta );
                if($result){
                    header("Location: /Usuarios/usuario.php?res=3");
                }
            }
        }
    }
?>
<!--Se importan los css necesarios-->
<link rel="stylesheet" href="/css/usuarios.css">
<link rel="stylesheet" href="/css/styles.css">
<!-- Formulario para la actualización de usuarios -->
<form action="./actualizarUsuario.php?usuario=<?php echo $id;?>" method="POST" enctype="multipart/form-data" class="formu">
    <!-- Se imprimen los errores si los hay -->
    <?php foreach($errores as $error){?>
        <div>
            <p class="error">
                <?php echo $error;?>    
            </p>
        </div>
    <?php } ?>
    <fieldset>
        <legend>Actualizar Usuario: </legend>

        <label for="id">ID: </label>
        <input type="text" name="id" value="<?php echo $ref; ?>">

        <label for="">Nombre de usuario: </label>
        <input type="text" name="nombre" value="<?php echo $nombre; ?>">

        <label for="">Email: </label>
        <input type="text" name="correo" value="<?php echo $correo; ?>">

        <label for="">Contraseña: </label>
        <input type="text" name="password" value="<?php echo $cont; ?>">

        <label for="tipo">Tipo de usuario: </label>
        <!-- Desplegable con las opciones de tipo de usuario -->
        <select name="tip">
            <option value=""></option>
            <option <?php echo $tip==1||$tip=='Comprador'?'selected':'';?> value="1">Comprador</option>
            <option <?php echo $tip==2||$tip=='Vendedor'?'selected':'';?> value="2">Vendedor</option>
            <option <?php echo $tip=='Distribuidor'||$tip==3?'selected':'';?> value="3">Distribuidor</option>
    </select>
        <!-- Botones de volver y de actualizar -->
        <div class="botones">
            <input type="submit" value="Actualizar Usuario" class="registro">
            <a href="/Usuarios/usuario.php" class="buton">Volver</a>
        </div>
    </fieldset>
</form>

<?php
    // Se incluye el footer
    incluirTemplate('footer');
?>