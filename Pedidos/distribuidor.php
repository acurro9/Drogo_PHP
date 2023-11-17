<?php 
//Para la actualización de distribuciones

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require '../includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');
    
    //En caso de no estar logueado y no ser el administrador nos redirige al index.php
    $auth=$_SESSION['login']??false;
    if(!$auth || $_SESSION['tipo']!='Administrador'){
        header('Location: /');
    }

    //Se guarda el id pasado por URL, si no hay redirige a la pagina del area personal del admin
    $id=$_GET['pedido']??null;
    if(!$id){
        header('Location: /areaPersonalAdmin.php');
    } else{
        //Se inicializa errores y se realizan las consultas necesarias
        $errores=[];
        $consulta1="SELECT * from entrega where refCompra like '$id';";
        $result=mysqli_query($db,$consulta1);

        $query="SELECT * from usuario where tipo='Distribuidor';";
        $resultado=mysqli_query($db,$query);

        $query2="SELECT * from locker;";
        $result2=mysqli_query($db, $query2); 
        $result4=mysqli_query($db, $query2); 

        $query3="SELECT * from Compra;";
        $result3=mysqli_query($db, $query3);


        //Se guardan los datos de la entrega
        if($fila=mysqli_fetch_row($result)){
            $ref=$fila[0];
            $distribuidor=$fila[1];
            $refCompra=$fila[2];
            $fechaRec=$fila[3];
            $fechaDep=$fila[4];
            $lockerOg=$fila[5];
            $lockerDep=$fila[6];
        }

        // En caso de hacer el POST
        if($_SERVER['REQUEST_METHOD']==="POST"){
            //Se guardan los datos de los inputs del formulario
            $distribuidor=mysqli_real_escape_string($db, $_POST['comp']);
            $refCompra=mysqli_real_escape_string($db, $_POST['compra']);
            $fechaRec=date($_POST['registro']);
            $fechaDep=date($_POST['deposito']);
            $lockerOg=mysqli_real_escape_string($db, $_POST['lockerOg']);
            $lockerDep=mysqli_real_escape_string($db, $_POST['lockerRec']);
            $fecha=date('Y-m-d');

            //En caso de que falte algún dato se guarda en errores
            if(!$distribuidor){
                $errores[]="Debes añadir una distribuidor.";
            }
            if(!$refCompra){
                $errores[]="Debes añadir un pedido.";
            }
            if(!$fechaRec){
                $errores[]="Debes añadir una fecha de recogida.";
            }
            if(!$fechaDep){
                $errores[]="Debes añadir una fecha de depósito.";
            }
            if(!$lockerOg){
                $errores[]="Debes añadir un locker de origen.";
            }
            if(!$lockerDep){
                $errores[]="Debes añadir un locker de depósito.";
            } 

            if($fecha>$fechaDep){
                $errores[]="La fecha de deposito no puede ser anterior a la fecha actual.";
            }
            if($fecha>=$fechaRec){
                $errores[]="La fecha de recogida no puede ser anterior a la fecha actual.";
            }
            if($fechaDep<$fechaRec){
                $errores[]="La fecha de recogida no puede ser anterior a la fecha de deposito.";
            }

            // En caso de que no haya errores se realiza el update y si es correcto nos redirige a la tabla pedidos
            if(empty($errores)){
                $consulta="UPDATE entrega set hash_distribuidor='$distribuidor', refCompra='$refCompra', fechaRecogida='$fechaRec', fechaDeposito='$fechaDep', lockerOrigen='$lockerOg', lockerDeposito='$lockerDep' where id='$ref';";
                $result=mysqli_query( $db, $consulta );
                if($result){
                    header("Location: /Pedidos/pedidos.php?res=4");
                }
            }
        }
    }
?>
<!--Se importan los css necesarios-->
<link rel="stylesheet" href="/css/pedidos.css">

<!-- Formulario para la actualización de las distribuciones -->
<form action="./distribuidor.php?pedido=<?php echo $id;?>" method="POST" enctype="multipart/form-data" class="formu">
    <?php foreach($errores as $error){?>
        <!-- Se imprimen los errores si los hay -->
        <div>
            <p class="error">
                <?php echo $error;?>    
            </p>
        </div>
    <?php } ?>
    <fieldset>
        <legend>Actualizar Distribuidor:</legend>
        <div class="formulario">
            <div class="part1">        
                <label for="comp">Distribuidor: </label>
                <select name="comp">
                    <!-- Desplegable con los distribuidores -->
                    <option value="" class="black">--Seleccione--</option>
                    <?php while($distribuidor2=mysqli_fetch_assoc($resultado)){?>
                        <option <?php echo $distribuidor==$distribuidor2['id']?'selected':''; ?> value="<?php echo $distribuidor2['id'];?>" class="black">
                            <?php echo $distribuidor2['username'];?>
                        </option>
                    <?php }?>    
                </select>
                
                <label for="lockerOg">Locker de Origen: </label>
                <select name="lockerOg">
                    <!-- Desplegable con los lockers -->
                    <option value="" class="black">--Seleccione--</option>
                    <?php while($lockerOg2=mysqli_fetch_assoc($result2)){ ?>
                        <option <?php echo $lockerOg==$lockerOg2['refLocker']?'selected':''; ?> value="<?php echo $lockerOg2['refLocker'];?>" class="black">
                            <?php echo $lockerOg2['direccion'];?>
                        </option>
                    <?php }?>    
                </select>

                <label for="lockerRec">Locker de Recogida: </label>
                <select name="lockerRec">
                    <!-- Desplegable con los lockers -->
                    <option value="" class="black">--Seleccione--</option>
                    <?php while($lockerRec2=mysqli_fetch_assoc($result4)){ ?>
                        <option <?php echo $lockerDep==$lockerRec2['refLocker']?'selected':''; ?> value="<?php echo $lockerRec2['refLocker'];?>" class="black">
                            <?php echo $lockerRec2['direccion'];?>
                        </option>
                    <?php }?>    
                </select>

            </div>
            <div class="part2">
                <label for="compra">refCompra: </label>
                <select name="compra">
                    <!-- Desplegable con los envios -->
                    <option value="" class="black">--Seleccione--</option>
                    <?php while($compra2=mysqli_fetch_assoc($result3)){ ?>
                        <option <?php echo $refCompra==$compra2['refCompra']?'selected':''; ?> value="<?php echo $compra2['refCompra'];?>" class="black">
                            <?php echo $compra2['refCompra'];?>
                        </option>
                    <?php }?>    
                </select>

                <label for="fecRegistro">Fecha de Recogida: </label>
                <input type="date" name="registro" value="<?php echo $fechaRec; ?>">
        
                <label for="fecDeposito">Fecha de Depósito: </label>
                <input type="date" name="deposito" value="<?php echo $fechaDep; ?>">
        

            </div>
        </div>
        <center>
            <label for="ref">Referencia: </label>
            <p class="lockerFijo"><?php echo $ref; ?> </p>
        </center>

        <!-- Botones para la actualización o volver -->
        <div class="botones">
            <input type="submit" value="Actualizar Distribuidor" class="registro">
            <a href="/Pedidos/pedidos.php" class="buton">Volver</a>
        </div>
    </fieldset>
</form>




<?php
//Se incluye el footer
    incluirTemplate("footer");
?>