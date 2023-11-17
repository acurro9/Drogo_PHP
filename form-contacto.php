<?php 
//Para el formulario de contacto

//Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require './includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');

    // Se inicializan las variables a vacio
    $errores=[];
    $nombre="";
    $correo="";
    $tlf="";
    $mensaje="";
    $terms="";
    // En caso de hacer el POST
    if($_SERVER['REQUEST_METHOD']==='POST'){
        // Se guardan los datos del formulario
        $nombre=mysqli_real_escape_string($db, $_POST['nombre']);
        $correo=mysqli_real_escape_string($db, $_POST['correo']);
        $tlf=mysqli_real_escape_string($db, $_POST['telefono']);
        $mensaje=mysqli_real_escape_string($db, $_POST['mensaje']);
        $terms=isset($_POST['terminos']) ? 1 : 0;

        // Si falta algún dato se crea un error
        if(!$nombre){
            $errores[]="Debes añadir un nombre.";
        }
        if(!$correo){
            $errores[]="Debes añadir un correo electrónico.";
        }
        if(!$tlf){
            $errores[]="Debes añadir un teléfono.";
        }
        if(!$mensaje){
            $errores[]="Debes añadir un mensaje.";
        }
        if($terms==0){
            $errores[]="Debes aceptar los términos y condiciones.";
        }
        // Si no hay errores se crea la consulta para añadir el mensaje a contacto
        if(empty($errores)){
            $query="INSERT into contacto (nombre, email, telefono, mensaje) values ('$nombre', '$correo', '$tlf', '$mensaje');";
            $consulta=mysqli_query($db, $query);

            if($consulta){
                header("Location: /");
            }
        }

    }
?>

    <!--Se importan los css necesarios-->
    <link rel="stylesheet" href="./css/contacto.css">
    <header>Formulario de contacto</header>

    <main class="formularioEntero">
        <!-- FORMULARIO -->
        <?php foreach($errores as $error){ ?>
            <p class="error"><?php echo $error;?></p>
        <?php } ?>
        <form action="form-contacto.php" class="formulario" id="formulario" enctype="multipart/form-data" method="POST">

            <!-- GRUPO DE NOMBRE-->
            <div class="formulario__grupo" id="grupo__nombre">
                <label class="formulario__label">Nombre y Apellido(s)</label>
                <div class="formulario__grupo-input">
                    <input type="text" class="formulario__input" name="nombre" id="nombre" placeholder="Introduce tu nombre completo" value="<?php echo $nombre;?>">
                    <i class="formulario__validacion-estado fas fa-times-circle"></i>
                </div>
                <p class="formulario__input-error">El nombre debe tener minimo 4 caracteres y solo se admiten números, letras y guión bajo.</p>
            </div>
        
            <!-- GRUPO DE EMAIL -->
            <div class="formulario__grupo" id="grupo__correo">
                <label class="formulario__label">Correo Electonico</label>
                <div class="formulario__grupo-input">
                    <input type="email" class="formulario__input" name="correo" id="correo" placeholder="Email" value="<?php echo $correo;?>">
                    <i class="formulario__validacion-estado fas fa-times-circle"></i>
                </div>
                <p class="formulario__input-error">El correo debe tener mÍnimo 4 caracteres y sólo se admiten números, letras y guión bajo.</p>
            </div>
    
    
            <!-- GRUPO DE TELÉFONO -->
            <div class="formulario__grupo" id="grupo__telefono">
                <label class="formulario__label">Teléfono</label>
                <div class="formulario__grupo-input">
                    <input type="telefono" class="formulario__input" name="telefono" id="telefono" placeholder="Teléfono de contacto" value="<?php echo $tlf;?>">
                    <i class="formulario__validacion-estado fas fa-times-circle"></i>
                </div>
                <p class="formulario__input-error">El número debe tener 10 digitos</p>
            </div>
    
            <!--GRUPO DE MENSAJE-->
            <div class="formulario__grupo" id="grupo__Mensaje">
            <label class="formulario__label">Mensaje</label>
                <div class="formulario__grupo-input">
                    <textarea name="mensaje" id="mensaje" cols="300" rows="100" class="formulario__input"><?php echo $mensaje;?></textarea>
                    <i class="formulario__validacion-estado fas fa-times-circle"></i>
                </div>
            </div>
    
            <!-- GRUPO TERMINOS Y CONDICIONES -->
            <div class="formulario__grupo formulario__grupo-terminos" id="grupo__terminos">
                <label class="formulario__label">
                    <input class="formulario__checkbox" type="checkbox" name="terminos" id="terminos">
                    <b class="formulario__span">Acepto los terminos y condiciones</b>
                </label>
            </div>
            
            <!-- MENSAJE DE CAMPOS REQUERIDOS -->
            <div class="formulario__mensaje" id="formulario__mensaje">
                <p><i class="fas fa-exclamation-circle"> <b>Error:</b> Debes completar todos los campos</i></p>
            </div>
    
    
            <!-- GRUPO ENVIAR -->
            <div class="formulario__grupo formulario__grupo-btn-enviar">
                <p class="formulario__mensaje-exito" id="formulario__mensaje-exito">El formulario se envió correctamente</p>
                <input class="formulario__btn" type="submit" value="Enviar">
            </div>
        </form>
    </main>
            
    <section class="banner_bottom">
        <div class="icon_drogo_div">
            <img src="./assets/logoimg/icon_green.png" alt="icono drogo" class="icon_drogo">
        </div>
        <!-- Botón para ir al registro -->
        <a href="registro.php" class="registro">Registro</a>
    </section>
    <?php
    //Se incluye el footer
        incluirTemplate('footer');
    ?>