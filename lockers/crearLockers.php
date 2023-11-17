<?php
//Para la creacón de lockers

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require '../includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');

    //Se inicializan las variables a vacio
    $errores=[];
    $refLocker="";
    $direccion="";
    $passwordLocker="";

    //Se comprueba que el usuario esté logueado y sea admin, si no te redirige al index
    $auth=$_SESSION['login']??false;
    if(!$auth || $_SESSION['tipo']!='Administrador'){
        header('Location: /');
    }

    // En caso de hacer el POST
    if($_SERVER['REQUEST_METHOD']==="POST"){
        //Se guardan los datos de los inputs del formulario
        $refLocker=mysqli_real_escape_string($db, $_POST["refLocker"]);
        $direccion=mysqli_real_escape_string($db, $_POST["direccion"]);
        $passwordLocker=mysqli_real_escape_string($db, $_POST["passwordLocker"]);

        //En caso de que falte algún dato se guarda en errores
        if(!$refLocker){
            $errores[]="Debes de añadir una refencia al Locker";
        }
        if(!$direccion){
            $errores[]="Debes añadir una dirección al Locker";
        }
        if(!$passwordLocker){
            $errores[]="Debes ponerle una PassWord al Locker";
        }
        
        // En caso de que no haya errores
        if(empty($errores)){
            //Se realiza la consulta para la creación y si se realiza con exito nos redirige a la tabla de lockers
            $consulta="INSERT INTO locker(refLocker, direccion, passwordLocker) VALUES ('$refLocker', '$direccion', '$passwordLocker');";
            $resultado=mysqli_query($db, $consulta);
            if($resultado){
                header("Location: /Lockers/lockers.php?res=1");
            }
        }
    }
?>
<!--Se importan los css necesarios-->
<link rel="stylesheet" href="/css/lockers.css">

<!-- Formulario para la creación de lockers -->
<form action="/Lockers/crearLockers.php" method="POST" class="formu">
    <fieldset>
        <legend>Crear Locker: </legend>

        <label for="">refLocker: </label>
        <input type="text" name="refLocker" value="<?php echo $refLocker; ?>">

        <label for="">Dirección: </label>
        <input type="text" name="direccion" value="<?php echo $direccion; ?>">

        <label for="">PassWord: </label>
        <input type="text" name="passwordLocker" value="<?php echo $passwordLocker; ?>">

        <input type="submit" value="Crear Locker">
    </fieldset>
</form>
<div class="centro">
            <a href="/Lockers/lockers.php" class="buton grande">Volver</a>
        </div>

<?php
//Se incluye el footer
    incluirTemplate("footer");
?>