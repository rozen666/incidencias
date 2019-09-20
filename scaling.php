<?php
session_start();
require './backend/core/CoreTools.php';
require './backend/core/QueryEngine.php';
$engine= new QueryEngine();
$tools = new CoreTools();
if( !$tools->sessionLost( $_SESSION ) ){
    if( $tools->hasAccess( $_SESSION, '1,2' ) ) {
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Guard System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
  <link rel="stylesheet" href="./frontend/css/styles.css">
</head>
<body>

<nav class="navbar navbar-expand-md bg-light navbar-light border-bottom-dark">
<a class="navbar-brand" href="landing.php">
    <img src="frontend/images/pepsico.png" alt="logo" style="width:130px;">
  </a>

  
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse justify-content-end navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">

    <?php
    $query = "SELECT COUNT(LOGA_ID) AS 'COUNT', LOGA_ID AS 'ID' FROM log_asistencia WHERE LOGA_USR = ".$_SESSION["ID"]." 
    AND LOGA_INICIO BETWEEN NOW() - INTERVAL 1 DAY AND NOW()";
    $resultado = $engine->executeQuery($query);
    $signed    = $resultado["data"][0]['COUNT'] > 0 ? true : false;
    if( $signed ){
      echo '<li class="nav-item pointer">
        <a class="text-info nav-link" data-id="'.$resultado["data"][0]["ID"].'" id="desfirmarse">Marcar salida laboral</a>
      </li>';
    }

    else {
      echo '<li class="nav-item pointer">
        <a class="text-success font-weight-bold nav-link" id="firmarse">Marcar inicio laboral</a>
      </li>';
    }
     ?>

      <li class="nav-item">
        <a class="text-danger font-weight-bold nav-link" href="./backend/core/logout.php">Salir</a>
      </li>
    </ul>
  </div>  
</nav>

<div class="container-fluid sidebar-height">
  <div class="row panel">
  	<div class="col-md-2 bg-light pt-5 h-100 border-right-dark">

      <center>
      <img  src="frontend/images/perfil.png" 
      class="rounded-circle" alt="Imagen" width="90" height="80">
      <h3> <?php  echo $_SESSION['NOMBRE']; ?> <br>
      <small class="text-muted"><i> <?php echo $_SESSION['ROL']; ?> </i></small></h3>
      </center>
      <hr>
      <?php 
      if( $signed ) {

        $res   = $engine->executeQuery( "SELECT FORM_NOMBRE FROM forms WHERE FORM_ESTADO = 1 LIMIT 1" );
        $table = $res['data'][0]['FORM_NOMBRE'];

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1,2,3' ) ) ) {
          echo '<a  href="./reports.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Levantar incidencia</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1' ) ) ) {
          echo '<a  href="./forms.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Formularios</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1,2' ) ) ) {
          echo '<a  href="./users.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Usuarios</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1' ) ) ) {
          echo '<a  href="./catalogs.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Catálogos</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1,2' ) ) ) {
          echo '<a  href="./scaling.php" class="btn btn-block btn-outline-dark btn-sm mt-1 active">
          Escalamientos <span class="badge badge-primary scalings">0</span></a>';
        }
      }
      ?>
  	</div>
  	<div class="col-md-10">
        <div class="row p-3">
            <div class="col-md-3 bg-light p-3 border border-light rounded scroll500 incidences">
                <h4 class="text-center text-primary">Incidencias</h4>
                <div class="lista"></div>
            </div>
            <div class="col-md-8 incidence">
                <div class="basics"></div><hr>
                <div class="form"></div><hr>
                <div class="evidences">
                    <h4 class="text-center text-info">Evidencias</h4>
                    <div id="evi" class="carousel slide" data-ride="carousel">
                        <ul class="carousel-indicators"></ul>
                        <div class="carousel-inner"></div>

                        <a class="carousel-control-prev" href="#evi" data-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </a>
                        <a class="carousel-control-next" href="#evi" data-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
  	</div>

  </div>
</div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.js"></script>
  <script src="./frontend/js/core.js"></script>

  <script>

    $( () => {
      var count = getScaling( <?php echo $_SESSION['ID'] . ',"' . $table . '"'; ?> );
      $('.scalings').text( count );
      
      const rawTable  = <?php echo '"'.$table.'"' ; ?>; 
      const cleanName = rawTable.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
      const table     = `form_${ cleanName.toLowerCase().replace(/ /g,"_") }`;

      callAjax( { table }, 'get', 'api/scalings', function( res ){

        if( !res.success ) {
            swal( res.title, res.message, 'warning' );
        } else {
            
            $('.incidences .lista').empty();
            
            res.data.forEach( function( current ) {
                const div = `<p class="isIncidence pointer fs-8 text-center bg-dark text-light p-1 rounded border border-light"
                data-incidence="${current.ID}">
                de ${current.POR} <br> <span class="text-info fs-07 font-italic" >el ${current.FECHA}</span></p>`;

                $('.incidences .lista').append( div );
            });
        }
      });

      $(document).on('click', '.isIncidence', function() {
        const id = $(this).data('incidence');
        callAjax( { table, id }, 'get', 'api/scalings', function( res ){

            if( !res.success ) {
                swal( res.title, res.message, 'warning' );
            } else {    
                $('.form, .basics, .evidences #evi ul, .evidences #evi .carousel-inner').empty();
                var current = res.data[0];
                    let files = current.FILES.split(',');
                    let i = 0;
                    files.forEach( function( file ) {
                        const img = `<img src="./frontend/images/uploads/${file}" 
                        class="rounded evidence">`;
                        const active = i === 0 ? 'active' : '';
                        $('.evidences #evi ul').append( `<li data-target="#evi" data-slide-to="${i}" class="${active}"></li>` );
                        $('.evidences #evi .carousel-inner').append( `<div class="carousel-item ${active}">${img}</div>` );
                        
                        i++;
                    });
                
                let modif = current.MOD_POR ? current.MOD_POR : 'Aún no se ha modificado';
                let selected = current.ESTADO == 1 ? 'selected' : '';
                let basics = `
                <h4 class="text-center text-info">Información básica</h4>
                <label class="fs-9 font-weight-bold text-primary">Creado por <span class="fs-7 text-dark font-weight-light">${current.POR}</span></label><br>
                <label class="fs-9 font-weight-bold text-primary">Modificado por <span class="fs-7 text-dark font-weight-light">${modif}</span></label><br>
                <select class="form-control form-control-sm w-50">
                    <option value="1" selected>Abierto</option>
                    <option value="2">Cerrado</option>
                </select>`;
                $('.basics').append( basics );
                
                const noKeys = [ 'CREADO_FECHA', 'CREADO_POR', 'ESTADO', 'ID', 'MODIF_FECHA', 'MODIF_POR', 'OWNER', 'POR', 'FILES' ];
                let keys = Object.keys( current ).map( (key) => {
                    if( !noKeys.includes( key ) ) return key;      
                });

                let newKeys = [];
                keys.forEach( (k) => {
                    if( k ) newKeys.push( k );
                });
                
                $('.form').append( '<h4 class="text-center text-info">Información del formulario</h4>' );
                newKeys.forEach( ( key ) => {
                    const k = key.split(' ').map(s => s.charAt(0).toUpperCase() + s.slice(1).toLowerCase()).join(' ');
                    const form = `<label class="fs-9 font-weight-bold text-primary">${k.replace( /_/g,' ' )} <span class="fs-7 text-dark font-weight-light">${current[key]}</span></label><br>`;
                    $('.form').append( form );

                });
            }
            });
        });
    });

  </script>
</body>

</html>

<?php
}else {
  header('Location: ./landing.php?error=Escalamientos');  
}
}else{
header('Location: /web');
}
?>
