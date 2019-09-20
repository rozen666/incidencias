<?php
session_start();
require './backend/core/CoreTools.php';
require './backend/core/QueryEngine.php';
$engine= new QueryEngine();
$tools = new CoreTools();
if( !$tools->sessionLost( $_SESSION ) ){
    if( $tools->hasAccess( $_SESSION, '1' ) ) {
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
          echo '<a  href="./reports.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Levantar incidencia</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1' ) ) ) {
          echo '<a  href="./forms.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Formularios</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1,2' ) ) ) {
          echo '<a  href="./users.php" class="btn btn-block btn-outline-dark btn-sm mt-1">Usuarios</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1' ) ) ) {
          echo '<a  href="./catalogs.php" class="btn btn-block btn-outline-dark btn-sm mt-1 active">Catálogos</a>';
        }

        if( in_array( $_SESSION['ROL_ID'], explode( ',', '1,2' ) ) ) {
          echo '<a  href="./scaling.php" class="btn btn-block btn-outline-dark btn-sm mt-1">
          Escalamientos <span class="badge badge-primary scalings">0</span></a>';
        }
      }
      ?>
  	</div>
  	<div class="col-md-10 p-5">
      <div class="row">
        <div class="col-md-3 bg-light p-3 border border-light rounded scroll500">
        <h4 class="text-center text-primary">Catálogos</h4>
        <?php
        $query = "SELECT MCAT_NOMBRE AS 'NAME', MCAT_CAT_NOMBRE AS 'CAT' FROM 
        mgr_catalogs WHERE MCAT_ESTADO = 1";

        $result = $engine->executeQuery( $query );


        if( !empty( $result['data'] ) ){ 

            foreach( $result['data'] as $cat ) {
                echo '<p class="isCatalog pointer fs-8 text-center bg-dark text-light p-1 rounded border border-light"
                data-catalog="'.$cat['CAT'].'">
                '.$cat['NAME'].'</p>';
            }

        }
        
        ?>
        </div>
        <div class="col-md-9 scroll500">
        <ul class="list-unstyled results">
            <li class="shadow p-3 mt-2">
                <div class="row">
                    <div class="col-md-6 offset-md-3 mb-2">
                        <input type="text" maxlength="15"
                        class="catName form-control form-control-sm" placeholder="Nombre del catálogo">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                    <button class="newCat btn-info btn btn-sm btn-block">
                    <i class="fas fa-archive font-weight-bold"></i> Nuevo catálogo
                    </button>
                    </div>
                    <div class="col-md-4">
                        <button class="add btn btn-info btn-sm btn-block">
                            <i class="fas fa-plus font-weight-bold"></i> Agregar campo
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button class="save btn btn-primary btn-sm btn-block">
                        <i class="fas fa-save font-weight-bold"></i> Guardar
                        </button>
                        <button class="update btn btn-primary btn-sm btn-block">
                        <i class="fas fa-edit font-weight-bold"></i> Actualizar
                        </button>
                    </div>
                </div>
            </li>
            <li class="shadow p-3 mt-2">
                <label>Nombre del campo</label>
                <div class="input-group">
                    <input type="text" class="catOption form-control form-control-sm" placeholder="Ingresa el valor que tendrá este campo">
                    <div class="input-group-append">
                        <button class="vis btn btn-success btn-sm" type="button">
                            <i class="fas fa-check font-weight-bold"></i>
                        </button> 
                    </div>
                </div>
            </li>
        </ul>
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
  $( ()=> {
      var activeCatalog, selectorCatalog;
      $(document).on('click', '.dismiss', function( e ){
        e.preventDefault(); e.stopPropagation();
        $(this).removeClass('dismiss btn-danger').addClass('vis btn-success');
        $(this).find('i').removeClass('fa-ban').addClass('fa-check');
      });

      $(document).on('click', '.vis', function( e ){
        e.preventDefault(); e.stopPropagation();
        $(this).removeClass('vis btn-success').addClass('dismiss btn-danger');
        $(this).find('i').removeClass('fa-check').addClass('fa-ban');
      });

      $(document).on('click', '.newCat', function( e ){
        e.preventDefault(); e.stopPropagation();
        selectorCatalog = null;
        $('.update').hide(); $('.save').show(); $('.catName').prop('readonly', false).val('');
        let i = 0;
            $('.results li').each(function(){
                if( i > 0 ){
                    $(this).remove();
                }
                i++;
            });
        $('.add').click();
      });

      $(document).on('click', '.add', function( e ){
        e.preventDefault(); e.stopPropagation();
        let li = `<li class="shadow p-3 mt-2">
                <label>Nombre del campo</label>
                <div class="input-group">
                    <input type="text" class="catOption form-control form-control-sm" placeholder="Ingresa el valor que tendrá este campo">
                    <div class="input-group-append">
                        <button class="vis btn btn-success btn-sm" type="button">
                            <i class="fas fa-check font-weight-bold"></i>
                        </button> 
                    </div>
                </div>
            </li>`;
        $(this).parents('ul').append( li );
        $(this).parents('ul').find('li:last').find('.catOption').focus();
      });

      $(document).on( 'click', '.save', function( e ) {
        e.preventDefault(); e.stopPropagation();
        let data = {};
        data.name    = $('.catName').val().trim();
        let cleanName = data.name.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
        data.catname = `cat_${ cleanName.toLowerCase().replace(/ /g,"_") }`;
        data.options = [];
        let i = 0;
        while( $('.catOption')[ i ] ){
            const val = $( $('.catOption')[ i ] ).val().trim();
            if( val != '' ) { data.options.push( val ); }
            i++;
        }
        if( data.name == '' || data.options.length <= 0 ){
            swal( 'Formulario incompleto', 'El catálago debe tener nombre y por lo menos una opción activa', 'info' );
        }
        else{
            callAjax( data, 'post', 'api/catalogs', function( res ) {

                if( !res.success ) {
                    swal( res.title, res.message, 'warning' );
                }else{
                    location.reload();
                }

            });
        }
      });

      $(document).on('click', '.update', function( e ){
        e.preventDefault(); e.stopPropagation();
        let data = {};
        data.options = [];
        data.name    = $('.catName').val().trim();
        let cleanName = data.name.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
        data.catname = `cat_${ cleanName.toLowerCase().replace(/ /g,"_") }`;
        let i = 0;
        while( $('.catOption')[ i ] ){
            const val    = $( $('.catOption')[ i ] ).val().trim();
            const id     = $( $('.catOption')[ i ] ).data('id');
            const active = $( $('.catOption')[ i ] ).next('.input-group-append').find('button').hasClass('vis') ? 1 : 2;
            if( val != '' ) { data.options.push( { val, id, active } ); }
            i++;
        }
    
        if( data.name == '' || data.options.length <= 0 ){
            swal( 'Formulario incompleto', 'El catálago debe tener nombre y por lo menos una opción activa', 'info' );
        }
        else{
            callAjax( data, 'put', 'api/catalogs', function( res ) {

                if( !res.success ) {
                    swal( res.title, res.message, 'warning' );
                }else{
                    swal( '¡Correcto!', res.message, 'success' );
                    $(selectorCatalog).click();
                }

            });
        }
      });

      $(document).on('click', '.isCatalog', function(){
        selectorCatalog = this;
        const catName = $(this).text();
        const cat     = $(this).data('catalog');
        $('.update').show(); $('.save').hide(); $('.catName').prop('readonly', true);
        $('.save').attr('data-method', 'put');
        if( cat && cat != '') {
            activeCatalog = cat;
            callAjax( {cat}, 'get', 'api/catalogs', function(res) {

                if( !res.success ){
                    swal( res.title, res.message, 'warning' );

                } else{
                    let i = 0;
                    $('.results li').each(function(){
                        if( i > 0 ){
                            $(this).remove();
                        }
                        i++;
                    });

                    res.data.forEach( function(catalog){
                        const fa  = catalog.ESTADO == 2 ? 'ban' : 'check';
                        const btn = catalog.ESTADO == 2 ? 'danger' : 'success';
                        const vis = catalog.ESTADO == 2 ? 'dismiss' : 'vis';
                        let li = `<li class="shadow p-3 mt-2">
                            <label>Nombre del campo</label>
                            <div class="input-group">
                                <input type="text" class="catOption form-control form-control-sm" placeholder="Ingresa el valor que tendrá este campo" 
                                value="${catalog.NOMBRE}" data-id="${catalog.ID}">
                                <div class="input-group-append">
                                    <button class="${vis} btn btn-${btn} btn-sm" type="button">
                                        <i class="fas fa-${fa} font-weight-bold"></i>
                                    </button> 
                                </div>
                            </div>
                        </li>`;
                        $('.results').append( li );
                    });
                    $('.catName').val( catName.trim() );
                }
            });
        }
      });

      var count = getScaling( <?php echo $_SESSION['ID'] . ',"' . $table . '"'; ?> );
      $('.scalings').text( count );
  })
  </script>        
</body>
</html>

<?php
  }else {
    header('Location: ./landing.php?error=Catalogos');  
  }
}else{
  header('Location: /web');
}
?>
