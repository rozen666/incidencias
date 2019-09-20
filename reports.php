<?php
session_start();
require './backend/core/CoreTools.php';
require './backend/core/QueryEngine.php';
$engine= new QueryEngine();
$tools = new CoreTools();
if( !$tools->sessionLost( $_SESSION ) ){
  if( $tools->hasAccess( $_SESSION, '1,2,3' ) ) {
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <title>Guard System</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
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
          echo '<a  href="./reports.php" class="btn btn-block btn-outline-dark btn-sm mt-1 active">Levantar incidencia</a>';
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
  	<div class="col-md-10 p-5">
    <div class="err text-center"></div>
      <div class="container">
          <div class="row">
              <div class="col-md-6 offset-md-3 preview">
                  <h4 class="text-center text-info formName"></h4>
                  <form class="form" action="./backend/api/reports.php" method="post" enctype='multipart/form-data'>
                  
                  </form>
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

    var url_string = window.location.href;
    var url = new URL(url_string);
    var c = url.searchParams.get("result");
    
    if( c ) {
      if( c == 'error' ) {
        $('.preview form').append( ` <div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>¡Ocurrió un error!</strong> No fue posible ingresar el registro, inténtalo nuevamente
  </div>` );
      } else {
        $('.preview form').append( ` <div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>¡Correcto!</strong> Se ingresó el registro correctamente
  </div>` );
      }
      
    }
    

    callAjax( { active: 1 }, 'get', 'api/forms', function(res) {

    if( !res.success ) {
      const input = `<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>¡Sin formulario!</strong><br> No se ha creado un formulario o está activo alguno
  </div>`;
      $('.err').append( input );

    } else{
        data = res.data[0];
        $('.formName').text( data.NAME );
        let checked = data.ESTADO == 1 ? true : false;
        for( let i = 0; i < data.INPUTS.split(',').length; i++ ){

        let labelName = data.LABELS.split(',')[i];
        let underscore = labelName.replace(/ /g,"_").toUpperCase();
        let sanitized  = underscore.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
        
        let input;
        const required = data.REQ.split(',')[i] === 1 ? 'required' : '',
                disabled = data.DIS.split(',')[i] === 1 ? 'disabled' : '',
            formControl  = data.INPUTS.split(',')[i] === 'checkbox' || data.INPUTS.split(',')[i] === 'radio' ? '' : 'form-control form-control-sm';
        if( data.INPUTS.split(',')[i] === 'textarea' ) {

            input = `
            <div class="form-group">
                <label class="font-weight-bold mt-1">${data.LABELS.split(',')[i]}</label>
                <textarea class="${formControl} dataCollector" 
                name="${sanitized}"
                placeholder="${data.PLACE.split(',')[i]}" ${required} ${disabled}
                data-key="${sanitized}" data-regex="${data.regex || 0}" data-required="${data.REQ.split(',')[i]}"></textarea>
            </div>`;

        } else if( data.INPUTS.split(',')[i].split(':')[0] === 'select' ) {
            callAjax( { form: data.INPUTS.split(',')[i].split(':')[1] }, 'get', 'api/catalogs', function(sel){

            if( !sel.success ) {
                swal( sel.title, sel.message, 'warning' );

            } else{
                
                let options = '';

                sel.data.forEach(function( opt ){
                options += `<option value="${opt.ID}">${opt.NOMBRE}</option>`;
                });
                input = `
                <div class="form-group">
                <label class="font-weight-bold mt-1">${data.LABELS.split(',')[i]}</label>
                <select class="${formControl} dataCollector" 
                name="${sanitized}"
                ${required} ${disabled} data-catalog="${data.INPUTS.split(',')[i].split(':')[1]}"
                data-key="${sanitized}" data-regex="${data.regex || 0}" data-required="${data.REQ.split(',')[i]}">
                <option selected disabled>${data.PLACE.split(',')[i]}</option>
                ${options}
                </select>
                </div>`;
            }

            }, true, false );
            
        }

            else{
            input = `
            <div class="form-group">
                <label class="font-weight-bold mt-1">${data.LABELS.split(',')[i]}</label>
                <input type="${data.INPUTS.split(',')[i]}" class="${formControl} dataCollector" 
                placeholder="${data.PLACE.split(',')[i]}" ${required} ${disabled} 
                name="${sanitized}"
                data-key="${sanitized}" data-regex="${data.regex || 0}" data-required="${data.REQ.split(',')[i]}">
            </div>`;
        }
        $('.preview form').append( input );
        }

        const table = data.NAME;
        const cleanName = table.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
        const form     = `form_${ cleanName.toLowerCase().replace(/ /g,"_") }`;
        $('.preview form').append( `<input type="hidden" value="${form}" name="form">` );
        $('.preview form').append( `
        <label class="font-weight-bold">Evidencia</label>
        <input type="file" class="form-control form-control-sm" name="file[]" multiple="multiple" required><br>
        <button type="submit" class="save btn btn-primary btn-sm btn-block">
                        <i class="fas fa-save font-weight-bold"></i> Guardar
                    </button>` );
    }
    });


    $(document).on('submit', '.form', function( e ){
      e.preventDefault(); e.stopPropagation();
      let data = dataCollector( $(this).find('button') );

      if( data.errors === 0 ) {
        $(this)[0].submit();
      }
    });

    var count = getScaling( <?php echo $_SESSION['ID'] . ',"' . $table . '"'; ?> );
    $('.scalings').text( count );
  });
  </script>        
</body>
</html>

<?php
  }else {
    header('Location: ./landing.php?error=Reportes');  
  }
}else{
  header('Location: /web');
}
?>
