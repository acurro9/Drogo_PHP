<?php 
//Para la tabla de los pedidos

    //Importamos las funciones, incluimos el header y creamos la conexión a la base de datos 
    require '../includes/funciones.php';
    incluirTemplate('header');
    $db=mysqli_connect('localhost', 'root', '', 'drogoDB');

    // Se guarda la respuesta, el id del usuario, el tipo y se inicializan variables vacias
    $res=$_GET["res"]??null;
    $id=$_SESSION["id"];
    $tipo=$_SESSION["tipo"];
    $tipoUsuario='';
    $errores=[];
    $consulta='';
    $titulo="Mis pedidos";

    //Se realiza la consulta para tener el nº total de pedidos
    $consultaCont="SELECT count(*) as contador FROM compra;";
    $datosCont=mysqli_query($db,$consultaCont);
    $data=mysqli_fetch_assoc($datosCont);
    $cantPropiedades=$data['contador'];
    $cantPropiedades2=$data['contador'];
        /*Se crean las variables para la paginacion
    Por defecto los productos por pagina son 5 y la página es 1, si en la url está presente cambia*/
    $ppp = 5;
    if (isset($_GET["producto"])) {
        $ppp = $_GET["producto"];
    }
    $pagina = 1;
    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }

    if($cantPropiedades==0){
        $totalPaginas=1;
        $offset=0;
        $limit=10;
        $cant="No hay pedidos.";
    } else{
        //Se coge el total de páginas redondeando hacia arriba
        $totalPaginas=ceil($cantPropiedades/$ppp);
        //Se calculan el limit y el offset
        $offset=($pagina-1)*$ppp;
        $limit= $ppp;
        $cant="";
    }

    //En caso de no estar logueado y ser distribuidor nos redirige al index.php
    $auth=$_SESSION['login']??false;
    if(!$auth || $_SESSION['tipo']=='Distribuidor'){
        header('Location: /');
    }

    // Se cambia el valor de unas variables en función del tipo de usuario
    switch ($_SESSION['tipo']) {
        case 'Comprador':
            $tipoUsuario= 'hash_comprador';
            $consulta="and $tipoUsuario='$id' ";
            break;
        case 'Vendedor':
            $tipoUsuario= 'hash_vendedor';
            $consulta="and $tipoUsuario='$id' ";
            break;
        case 'Administrador':
            $consulta=" ";
            $tipoUsuario="admin";
            $titulo="Pedidos";
    }

    //Si existe tipo de usuario se realiza la conexión para ver los pedidos, si es admin los mira todos, si es comprador o vendedor solo mira los pedidos propios
    if($tipoUsuario){
        $query = "SELECT Compra.refCompra, Compra.fechaCompra, Compra.importe, Compra.fechaRecogida, Compra.fechaDeposito, Compra.cargoTransporte, Compra.cargosAdicionales, Compra.hash_comprador, Compra.hash_vendedor, Compra.distribuidor, Locker.refLocker, Locker.direccion from Compra inner join Locker where Compra.refLocker=Locker.refLocker $consulta order by refCompra limit $limit offset $offset;";
        $datos = mysqli_query($db, $query);
        if(!$datos->num_rows){
            if($tipo!='Administrador'){
                $errores[]="El usuario no tiene pedidos relacionados.";
                $cant='';
            } else{
                $errores[]="";
            }
        }
    }

    // En caso de hacer el POST
    if ($_SERVER['REQUEST_METHOD']==='POST'){
        //En el caso de darle al boton de borrar
        if(isset($_POST['borrar'])){
            //Se guarda la id del pedido y se elimina
            $id=$_POST['refCompra'];
            $consulta="DELETE from compra where refCompra='${id}';";
            $result=mysqli_query($db, $consulta);
            if($result){
                header("Location: /Pedidos/pedidos.php?res=3");
            }
        }
    }
    
?>
<!--Se importan los css necesarios-->
<link rel="stylesheet" href="/css/pedidos.css">
<link rel="stylesheet" href="/css/styles.css">
<main>
    <h1 class="abajo"><?php echo $titulo;?></h1>
    <div class="centrado">
        <!--Se le pide al usuario el número de pedidos por página-->
        <form class="paginado">
            <fieldset>
                <legend style="color: white;">Pedidos por página: </legend>
                <div class="cen">
                    <select name="producto">
                        <option <?php echo $ppp==3?'selected':''; ?> value=3>3</option>
                        <option <?php echo $ppp==5?'selected':''; ?> value=5>5</option>
                        <option <?php echo $ppp==10?'selected':''; ?> value=10>10</option>
                        <option <?php echo $ppp==20?'selected':''; ?> value=20>20</option>
                    </select>
                    <input type="submit" value=" " class="">
                </div>
            </fieldset>
        </form>
    </div>
    
    <table class="tabla">
    <?php
    // Se imprimen los errores
        foreach($errores as $error){ 
    ?>
                    
        <div class="error">
            <?php echo $error;?>
        </div>
                    
    <?php
        }
        // Se imprime el mensaje necesario dependiendo de la respuesta
        if(intval($res)===1){?>
            <p class="success">Pedido creado con exito.</p>
       <?php } if(intval($res)===2){?>
            <p class="success">Pedido modificado con exito.</p>
       <?php } if(intval($res)===3){?>
            <p class="success">Pedido borrado con exito.</p>
       <?php } else if(intval($res)===4){?>
            <p class="success">Distribuidor modificado con exito.</p>
       <?php }
    ?>
<?php echo '<p class="error">'.$cant.'</p>'?>
        <thead>
            <tr>
                <th>Referencia Pedido</th>
                <th>Fecha Pedido</th>
                <th>Importe</th>
                <th>Locker del envio</th>
                <th>Dirección</th>
                <!-- En el caso del comprador -->
                <?php if($tipo=='Comprador'){?>
                    <th>Fecha Recogida</th>    
                <!-- En el caso del vendedor -->
                <?php } else if($tipo=='Vendedor'){?>
                    <th>Fecha Deposito</th>
                    <th>Cargo de Transporte</th>
                    <th>Cargos Adicionales</th>
                    <th>Distribuidor</th>
                <!-- En el caso del administrador -->
                <?php } else if($tipo=='Administrador'){ ?>
                    <th>Fecha Recogida</th>
                    <th>Fecha Deposito</th>
                    <th>Cargo de Transporte</th>
                    <th>Cargos Adicionales</th>
                    <th>Comprador</th>
                    <th>Vendedor</th>
                    <th>Distribuidor</th>
                    <th>Operaciones</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php while ($fila=mysqli_fetch_assoc($datos)){ ?>
            <tr>
                <td><?php echo $fila['refCompra']; ?></td>
                <td><?php echo $fila['fechaCompra']; ?></td>
                <td><?php echo $fila['importe']; ?></td>
                <td><?php echo $fila['refLocker']; ?></td>
                <td class="direccion"><?php echo $fila['direccion']; ?></td>
                <!-- En el caso del comprador -->
                <?php if($tipo=='Comprador'){?>
                    <td><?php echo $fila['fechaRecogida'];?></td>   
                <!-- En el caso del vendedor -->
                <?php } else if($tipo=='Vendedor'){?>
                    <td><?php echo $fila['fechaDeposito'];?></td>   
                    <td><?php echo $fila['cargoTransporte'];?></td>   
                    <td><?php echo $fila['cargosAdicionales'];?></td>   
                    <!-- Dependiendo de si hay distribución o no aparece una cosa u otra -->
                    <td>
                        <div class="row">

                            <p><?php echo $fila['distribuidor']==1?'Si':'No';?></p>
                            <?php 
                                if($fila['distribuidor']==1){?>
                                    <a href="/Pedidos/distribuidor.php?pedido=<?php echo $fila['refCompra'];?>" class="act"><img src="/assets/icons/edit.svg" alt=""></a>
                            <?php } ?>
                        </div>
                    </td> 
                    <!-- En el caso del administrador -->
                <?php } else if($tipo=='Administrador'){ ?>
                    <td><?php echo $fila['fechaRecogida'];?></td>   
                    <td><?php echo $fila['fechaDeposito'];?></td>  
                    <td><?php echo $fila['cargoTransporte'];?></td>   
                    <td><?php echo $fila['cargosAdicionales'];?></td>    
                    <td><?php echo $fila['hash_comprador'];?></td>  
                    <td><?php echo $fila['hash_vendedor'];?></td> 
                    <!-- Dependiendo de si hay distribución o no aparece una cosa u otra -->
                    <td>
                        <div class="row">

                            <p><?php echo $fila['distribuidor']==1?'Si':'No';?></p>
                            <?php 
                                if($fila['distribuidor']==1){?>
                                    <a href="/Pedidos/distribuidor.php?pedido=<?php echo $fila['refCompra'];?>" class="act"><img src="/assets/icons/edit.svg" alt=""></a>
                            <?php } ?>
                        </div>
                    </td> 
                    <td>
                        <!-- Formulario para eliminar el pedido seleccionado -->
                        <div class="bloque">
                            <form action="<?php $_SERVER[ 'PHP_SELF' ]; ?>" method="post" onsubmit="return confirmEliminado()" class="formEliminado">
                                <input class="bTabla" type="submit" name="borrar" value=" ">
                                <input class="oculto" type="hidden" name="refCompra" value=<?php echo $fila['refCompra'];?>>
                            </form>
                            <a href="/Pedidos/actualizarPedido.php?pedido=<?php echo $fila['refCompra'];?>" class="bTabla act"><img src="/assets/icons/writer.svg" alt=""></a>
                </div>
                <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <!--Para moverse entre las páginas-->
        <div class="pagComplete">
            <?php if($cantPropiedades!=0){ if($pagina>1){?>
                
            <a class="pag" href="/Pedidos/pedidos.php?producto=<?php echo $ppp;?>&pagina=<?php echo $pagina-1?>"><</a><?php } ?>
            <?php for($i=0; $i<$totalPaginas; $i++){ ?>
                <a class="pag" href="/Pedidos/pedidos.php?producto=<?php echo $ppp;?>&pagina=<?php echo $i+1;?>" <?php echo $pagina==$i+1?'style="color: #71B100; font-weight: 800;"':'';?>><?php echo $i+1;?></a>
                <?php } 
                if($pagina<$totalPaginas){
                    ?>
            <a class="pag" href="/Pedidos/pedidos.php?producto=<?php echo $ppp;?>&pagina=<?php echo $pagina+1?>">></a>
                    <?php }} ?>
        </div>
        <?php
            if($_SESSION['tipo']=='Administrador'){
        ?>
            <div class="centro">
                <a href="/Pedidos/crearPedido.php" class="buton grande">Crear Pedido</a>
            </div>
        <?php } ?>
</main>
<!-- Se incluye los archivos de javascript -->
<script src="/js/index.js"></script>
<?php
    //Se incluye el footer
    incluirTemplate('footer');
?>