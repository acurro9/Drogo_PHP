<?php 
//Para la creación de pedidos

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require '../includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    $auth=$_SESSION['login']??false;
    if(!$auth || $_SESSION['tipo']!='Administrador'){
        header('Location: /');
    }

    // Se realizan las consultas necesarias para rellenar los datos
    $consulta1="SELECT * from usuario where tipo like 'Comprador';";
    $result=mysqli_query($db,$consulta1);
    $consulta2="SELECT * from usuario where tipo like 'Vendedor';";
    $result2=mysqli_query($db,$consulta2);
    $consulta3="SELECT refLocker, direccion from locker;";
    $result3=mysqli_query($db,$consulta3);

    //Se inicializan las variables a vacio
    $errores=[];
    $referencia='';
    $hash_comprador='';
    $hash_vendedor='';
    $importe='';
    $cargoTrans='';
    $cargoAds='';
    $locker='';
    $fecDeposito='';
    $fecRecogida='';
    $fechaDep='';
    $fechaRec='';
    $distribuidor='';

    // En caso de hacer el POST
    if($_SERVER['REQUEST_METHOD']==="POST"){
        //Se guardan los datos de los inputs del formulario
        $referencia=md5(uniqid(rand(),true));
        $hash_comprador=mysqli_real_escape_string($db, $_POST["comp"]);
        $hash_vendedor=mysqli_real_escape_string($db, $_POST['vend']);
        $importe=mysqli_real_escape_string($db, $_POST['imp']);
        $cargoTrans=mysqli_real_escape_string($db, $_POST['carT']);
        $cargoAds=mysqli_real_escape_string($db, $_POST['carA']);
        $locker=mysqli_real_escape_string($db, $_POST['locker']);
        $fecha=date('Y-m-d');
        $fechaDep=date($_POST['deposito']);
        $fechaRec=date($_POST['registro']);
        $distribuidor=isset($_POST['dist']) ? 1 : 0;

        //En caso de que falte algún dato se guarda en errores
        if(!$hash_comprador){
            $errores[]="Debes añadir un comprador.";
        }
        if(!$hash_vendedor){
            $errores[]="Debes añadir un vendedor.";
        }
        if(!$importe){
            $errores[]="Debes añadir el importe.";
        }
        if(!$cargoTrans){
            $errores[]="Debes añadir un cargo de transporte.";
        }
        if(!$cargoAds){
            $errores[]="Debes añadir los cargos adicionales.";
        }
        if(!$locker){
            $errores[]="Debes añadir un locker para el envio.";
        } 
        if(!$fechaDep){
            $errores[]="Debes añadir una fecha de deposito.";
        }
        if(!$fechaRec){
            $errores[]="Debes añadir una fecha de recogida.";
        }
        if($fecha>$fechaDep){
            $errores[]="La fecha de deposito no puede ser anterior a la fecha actual.";
        }
        if($fecha>=$fechaRec){
            $errores[]="La fecha de recogida no puede ser anterior a la fecha actual.";
        }
        if($fechaDep>$fechaRec){
            $errores[]="La fecha de recogida no puede ser anterior a la fecha de deposito.";
        }

        // En caso de que no haya errores
        if(empty($errores)){
            //Si se eligió la opción de distribución se añade a la tabla entrega la referencia de la compra y nos redirige a la tabla pedidos
                $query="INSERT into compra (refCompra, hash_comprador, hash_vendedor, fechaCompra, importe, cargoTransporte, cargosAdicionales, fechaDeposito, fechaRecogida, refLocker, distribuidor)
                    values ('$referencia', '$hash_comprador', '$hash_vendedor', '$fecha', $importe, $cargoTrans, $cargoAds, '$fechaDep', '$fechaRec', '$locker', $distribuidor );";
                $resultado=mysqli_query($db, $query);
            if($resultado){
                if($distribuidor==1){
                    $query2="INSERT into entrega (refCompra) values ('$referencia');";
                    $resultado2=mysqli_query($db, $query2);
                    if($resultado2){
                        header("Location: /Pedidos/pedidos.php?res=1");
                    }
                } else{
                    header("Location: /Pedidos/pedidos.php?res=1");
                }
            }
            
        }
    }


?>
<!--Se importan los css necesarios-->
<link rel="stylesheet" href="/css/pedidos.css">

<!-- Formulario para la creación de pedidos -->
<form action="/Pedidos/crearPedido.php" method="POST" enctype="multipart/form-data" class="formu">
    <?php foreach($errores as $error){?>
        <!-- Se imprimen los errores si existen -->
        <div class="error">
            <?php echo $error;?>    
        </div>
    <?php } ?>
    <fieldset>
        <legend>Crear Pedido:</legend>
        <div class="formulario">
            <div class="part1">
                <label for="comp">Comprador: </label>
                <select name="comp">
                    <option value="" class="black">--Seleccione--</option>
                    <!-- Desplegable con los compradores -->
                    <?php while($comprador=mysqli_fetch_assoc($result)){?>
                        <option <?php echo $hash_comprador==$comprador['id']?'selected':''; ?> value="<?php echo $comprador['id'];?>" class="black">
                            <?php echo $comprador['username'];?>
                        </option>
                    <?php }?>    
                </select>
        
                <label for="vend">Vendedor: </label>
                <select name="vend">
                    <option value="" class="black">--Seleccione--</option>
                    <!-- Desplegable con los vendedores -->
                    <?php while($vendedor=mysqli_fetch_assoc($result2)){ ?>
                        <option <?php echo $hash_vendedor==$vendedor['id']?'selected':''; ?> value="<?php echo $vendedor['id'];?>" class="black">
                            <?php echo $vendedor['username'];?>
                        </option>
                    <?php }?>    
                </select>
                
                <label for="Locker">Locker: </label>
                <select name="locker">
                    <option value="" class="black">--Seleccione--</option>
                    <!-- Desplegable con los lockers -->
                    <?php while($lockers=mysqli_fetch_assoc($result3)){ ?>
                        <option <?php echo $locker==$lockers['refLocker']?'selected':''; ?> value="<?php echo $lockers['refLocker'];?>" class="black">
                            <?php echo $lockers['direccion'];?>
                        </option>
                    <?php }?>    
                </select>
        
        
                <label for="imp">Importe: </label>
                <input type="number" name="imp" value="<?php echo $importe;?>">
            </div>
    
            <div class="part2">
                <label for="carT">Cargo de transporte: </label>
                <input type="number" name="carT" value="<?php echo $cargoTrans;?>">
        
                <label for="carA">Cargos adicionales: </label>
                <input type="number" name="carA" value="<?php echo $cargoAds;?>">
        
                <label for="fecDeposito">Fecha de Depósito: </label>
                <input type="date" name="deposito" value="<?php echo $fechaDep; ?>">
        
                <label for="fecRegistro">Fecha de Recogida: </label>
                <input type="date" name="registro" value="<?php echo $fechaRec; ?>">
        
            </div>
        </div>
        <div class="dist">
            <label for="dist">Distribuidor: </label>
            <input type="checkbox" name="dist" <?php echo $distribuidor==1?'checked':''; ?>>
        </div>
        
        <!-- Botones para crear o volver -->
        <div class="botones">
            <input type="submit" value="Crear Pedido" class="registro">
            <a href="/Pedidos/pedidos.php" class="buton">Volver</a>
        </div>
    </fieldset>
</form>




<?php
//Se incluye el footer
    incluirTemplate("footer");
?>