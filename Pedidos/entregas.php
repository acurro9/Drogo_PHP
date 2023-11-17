<?php 
//Para la tabla de distribucines

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require '../includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');

    //En caso de no estar logueado y no ser el administrador o distribuidor nos redirige al index.php
    $auth=$_SESSION['login']??false;
    if(!$auth || !($_SESSION['tipo']=='Distribuidor'||$_SESSION['tipo']=='Administrador')){
        header('Location: /');
    }

    //Se guarda la respuesta, el tipo de usuario y la id del usuario
    $res=$_GET['res']??NULL;
    $tipo=$_SESSION['tipo'];
    $idU=$_GET['id']??NULL;

    // Se realizan las querys necesarias
    if(!$idU){
        $query="SELECT * from entrega;";
    } else{
        $query="SELECT * from entrega where hash_distribuidor='$idU';";
    }
    $datos=mysqli_query($db, $query);
    
    // En el caso de hacer el POST
    if ($_SERVER['REQUEST_METHOD']==='POST'){
        //Se guarda la id y se realiza la consulta para la eliminación, en caso de ser correcto aparece un mensaje exito
        $id=$_POST['refCompra'];
        $consulta="DELETE from entrega where id='$id';";
        $result=mysqli_query($db, $consulta);
        if($result){
            header("Location: /Pedidos/entregas.php?res=1");
        }
    }
    
?>
<!--Se importan los css necesarios-->
<link rel="stylesheet" href="/css/pedidos.css">
<link rel="stylesheet" href="/css/styles.css">

    <h1>Distribuciones</h1>
    
<main>
    
    <table class="tabla">
    <?php
    // Se imprime el mensaje necesario dependiendo de la respuesta
        if(intval($res)===1){?>
            <p class="success">Entrega eliminada con exito.</p>
        <?php } if(intval($res)===2){?>
        <p class="success">Entrega modificada con exito.</p>
        <?php } ?>
        <thead>
            <tr>
                <th>ID</th>
                <th>Locker de origen</th>
                <th>Locker de depósito</th>
                <th>refCompra</th>
                <th>Fecha de recogida</th>
                <th>Fecha de depósito</th>
                <!-- Esto solo aparece si se es administrador -->
                <?php if($tipo=='Administrador'){?>
                    <th>Distribuidor</th>  
                    <th>Operaciones</th> 
                <?php }?>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila=mysqli_fetch_assoc($datos)){ ?>
            <tr>
                <td><?php echo $fila['id']; ?></td>
                <td><?php echo $fila['refCompra']; ?></td>
                <td><?php echo $fila['fechaRecogida']; ?></td>
                <td><?php echo $fila['fechaDeposito']; ?></td>
                <td><?php echo $fila['lockerOrigen']; ?></td>
                <td><?php echo $fila['lockerDeposito']; ?></td>

                <!-- Esto solo aparece si se es administrador -->
                <?php if($tipo=='Administrador'){ ?>
                    <td><?php echo $fila['hash_distribuidor']; ?></td>
                    <td>
                        <div class="bloque">
                            <!-- Formulario para eliminar el pedido seleccionado -->
                            <form action="<?php $_SERVER[ 'PHP_SELF' ]; ?>" method="post" onsubmit="return confirmEliminado()" class="formEliminado">
                                <input class="bTabla" type="submit" value=" ">
                                <input class="oculto" type="hidden" name="refCompra" value=<?php echo $fila['id'];?>>
                            </form>
                            <a href="/Pedidos/distribuidor.php?pedido=<?php echo $fila['refCompra'];?>" class="bTabla act"><img src="/assets/icons/writer.svg" alt=""></a>
                </div>
                <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <!-- Si el usuario no es distribuidor puede volver a la tabla pedidos -->
    <?php if($_SESSION['tipo']!='Distribuidor'){?>
        <div class="centro">
            <a href="/Pedidos/pedidos.php" class="buton grande">Ver Pedidos</a>
        </div>
    <?php } ?>
</main>
<!-- Se incluye los archivos de javascript -->
<script src="/js/index.js"></script>
<?php
    //Se incluye el footer
    incluirTemplate('footer');
?>