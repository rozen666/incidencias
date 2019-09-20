<?php
session_start();
require './backend/core/CoreTools.php';
require './backend/core/QueryEngine.php';
$engine= new QueryEngine();
$tools = new CoreTools();
if( !$tools->sessionLost( $_SESSION ) ){
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
    <img src="frontend/images/logo.png" alt="logo" style="width:130px;">
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
          echo '<a  href="./scaling.php" class="btn btn-block btn-outline-dark btn-sm mt-1">
          Escalamientos <span class="badge badge-primary scalings">0</span></a>';
        }
      }
      ?>
  	</div>
  	<div class="col-md-10">
        <?php
          if( !empty( $_GET['error'] ) ) {
            echo '<div class="text-center alert alert-danger alert-dismissible fade show m-5">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>No tienes suficientes privilegios</strong> <br>No puedes ver la sección '.$_GET['error'].'
          </div>';
          }
        ?>
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
    });

  </script>
</body>

</html>

<?php
}else{
  header('Location: /web');
}
?>
