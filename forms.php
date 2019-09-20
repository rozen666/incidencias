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
          echo '<a  href="./forms.php" class="btn btn-block btn-outline-dark btn-sm mt-1 active">Formularios</a>';
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
      <div class="row">
        <div class="col-md-3 bg-light p-3 border border-light rounded scroll500">
        <h4 class="text-center text-primary">Formularios</h4>
        <div class="newFormConstructor">                
          <form>
            <select class="field form-control form-control-sm dataCollector"
            data-required="1" data-regex="0" data-key="field">
                <option selected disabled>Selecciona un tipo de campo</option>
                <option value="text">Texto general</option>
                <option value="catalog">Catálogo</option>
                <option value="password">Contraseña</option>
                <option value="textarea">Caja de texto</option>
                <option value="radio">Radio</option>
                <option value="checkbox">Checkbox</option>
            </select>

            <select class="catalog form-control form-control-sm dataCollector mt-1"
            data-required="0" data-regex="0" data-key="catalog">
                <option selected disabled>Selecciona un catálogo</option>
                <?php
                $query = "SELECT MCAT_NOMBRE AS 'NAME', MCAT_CAT_NOMBRE AS 'CAT' FROM 
                mgr_catalogs WHERE MCAT_ESTADO = 1";
                $result = $engine->executeQuery( $query );
                if( empty( $result['data'] )  ) {
                  echo '<option selected disabled>No hay catálogos, crea uno nuevo</option>';

                } else{
                  foreach( $result['data'] as $option ) {
                    echo '<option value="'.$option['CAT'].'">'.$option['NAME'].'</option>';
                  }
                }
                ?>
            </select>
            <input type="text" class="form-control form-control-sm dataCollector mt-1" 
            data-required="1" data-regex="0" data-key="label" placeholder="Etiqueta del campo">
            <input type="text" class="form-control form-control-sm dataCollector mt-1" 
            data-required="0" data-regex="0" data-key="value"placeholder="Valor por defecto">
            <input type="text" class="form-control form-control-sm dataCollector mt-1"
            data-required="0" data-regex="0" data-key="placeholder" placeholder="Texto de ayuda">
            
            <input type="checkbox" class="dataCollector"
            data-required="0" data-regex="0" data-key="required"> <small>¿Es requerido?</small>
            <input type="checkbox" class="dataCollector"
            data-required="0" data-regex="0" data-key="disabled"> <small>¿Está deshabilitado?</small>
            <button class="btn btn-success btn-sm btn-block addField">Agregar a vista previa</button>
          </form>
        </div><hr>
            <?php
            $query = "SELECT FORM_ID AS 'ID', FORM_NOMBRE AS 'NAME',
            FORM_ESTADO AS 'ESTADO'
            FROM forms ORDER BY ESTADO, NAME";
            $res = $engine->executeQuery( $query );
            if( empty( $res['data'] ) ) {
                echo '<h5 class="text-danger text-center">No hay formularios, crea uno</h5>';

            } else {
              foreach( $res['data'] as $form ) {
                $border = $form['ESTADO'] == 1 ? 'success' : 'danger';
                echo '<p class="formy pointer fs-8 text-center bg-dark text-light p-1 rounded border border-'.$border.'"
                data-id="'.$form['ID'].'">
                '.$form['NAME'].'</p>';
              }
            }
            ?>
        </div>
        <div class="col-md-9 preview scroll500">
          <ul class="list-unstyled results">
              <li class="shadow p-3 mt-2">
                  <div class="row">
                      <div class="col-md-6 offset-md-3 mb-2">
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            <input type="checkbox" class="default"> 
                          </div>
                        </div>
                        <input type="text" maxlength="50"
                          class="formName form-control form-control-sm" placeholder="Nombre del formulario">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-4">
                      <button class="newForm btn-info btn btn-sm btn-block">
                        <i class="fas fa-archive font-weight-bold"></i>
                          Nuevo formulario
                      </button>
                      </div>
                      <div class="col-md-4">
                          <button class="test btn btn-info btn-sm btn-block">
                              <i class="fas fa-eye font-weight-bold"></i> Probar
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
          </ul>
          <form></form>
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
        var selector, upId;
        $(document).on('click', '.newForm', function(){
          if( $('.newFormConstructor').is(':visible') ) {
            $('.newFormConstructor').hide('fast');

          } else{
            $('.newFormConstructor').show('fast');
            $('.preview form').empty();
            $('.formName').val('');
            $('.save').show();
            $('.update').hide();
            $('.default').prop('checked', false);
          }
            
        });

        $(document).on('click', '.addField', function( e ){
            e.preventDefault(); e.stopPropagation();
            let data = dataCollector( this );
            if( data.errors === 0 ) {
                let input;
                const required = data.required === 1 ? 'required' : '',
                  disabled = data.disabled === 1 ? 'disabled' : '',
                  formControl = data.field === 'checkbox' || data.field === 'radio' ? '' : 'form-control form-control-sm';
                if( data.field === 'textarea' ) {

                    input = `
                    <div class="form-group">
                        <button type="button" class="close removeField text-danger">&times;</button>
                        <label class="font-weight-bold mt-1">${data.label}</label>
                        <textarea class="${formControl} dataCollector" 
                        placeholder="${data.placeholder}" ${required} ${disabled}
                        data-key="${data.label}" data-regex="${data.regex || 0}" data-required="${data.required}">${data.value}</textarea>
                    </div>`;

                } else if( data.field === 'catalog' ) {
                  callAjax( { form: data.catalog }, 'get', 'api/catalogs', function(res){

                    if( !res.success ) {
                      swal( res.title, res.message, 'warning' );

                    } else{
                      let options = '';

                      res.data.forEach(function( opt ){
                        options += `<option value="${opt.ID}">${opt.NOMBRE}</option>`;
                      });
                      input = `
                      <div class="form-group">
                        <button type="button" class="close removeField text-danger">&times;</button>
                        <label class="font-weight-bold mt-1">${data.label}</label>
                        <select class="${formControl} dataCollector" 
                        ${required} ${disabled} data-catalog="${data.catalog}"
                        data-key="${data.label}" data-regex="${data.regex || 0}" data-required="${data.required}">
                        <option selected disabled>${data.placeholder}</option>
                        ${options}
                        </select>
                      </div>`;
                    }

                  }, true, false );
                  
                }

                 else{
                    input = `
                    <div class="form-group">
                        <button type="button" class="close removeField text-danger">&times;</button>
                        <label class="font-weight-bold mt-1">${data.label}</label>
                        <input type="${data.field}" class="${formControl} dataCollector" 
                        placeholder="${data.placeholder}" ${required} ${disabled} value="${data.value}"
                        data-key="${data.label}" data-regex="${data.regex || 0}" data-required="${data.required}">
                    </div>`;
                }
                $('.preview form').append( input );
                $(this).parents('form')[0].reset();
                $('.catalog').data('required', '0');
                $('.catalog').hide();
            }
        });

        $(document).on('click', '.test', function(e) {
            e.preventDefault(); e.stopPropagation();
            if( $('.preview form .dataCollector')[0] ) {
              let data = dataCollector( $('.preview form .dataCollector')[0], 1 );
              if( data.errors === 0 ){
                  const alert = `<div class="alert alert-success alert-dismissible mt-3">
                                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                                      <strong>¡Correcto!</strong> Se envió correctamente la prueba
                                  </div>`;
                  $('.preview form').append( alert );
                  $('.preview form .alert').fadeOut( 5000 );
                  
              }
            }
        });

        $(document).on('click', '.formy', function(){
          let id = $(this).data('id');
          selector = this;
          upId = id;
          callAjax( { id }, 'get', 'api/forms', function(res) {

            if( !res.success ) {
              swal( res.title, res.message, 'warning' );

            } else{
              
              data = res.data[0];
              $('.preview form').empty();
              $('.formName').val( data.NAME );
              $('.save').hide();
              $('.update').show();
              let checked = data.ESTADO == 1 ? true : false;
              $('.default').prop('checked', checked );
              for( let i = 0; i < data.INPUTS.split(',').length; i++ ){
                let input;
                const required = data.REQ.split(',')[i] === 1 ? 'required' : '',
                      disabled = data.DIS.split(',')[i] === 1 ? 'disabled' : '',
                  formControl  = data.INPUTS.split(',')[i] === 'checkbox' || data.INPUTS.split(',')[i] === 'radio' ? '' : 'form-control form-control-sm';
                if( data.INPUTS.split(',')[i] === 'textarea' ) {

                    input = `
                    <div class="form-group">
                        <button type="button" class="close removeField text-danger">&times;</button>
                        <label class="font-weight-bold mt-1">${data.LABELS.split(',')[i]}</label>
                        <textarea class="${formControl} dataCollector" 
                        placeholder="${data.PLACE.split(',')[i]}" ${required} ${disabled}
                        data-key="${data.LABELS.split(',')[i]}" data-regex="${data.regex || 0}" data-required="${data.REQ.split(',')[i]}"></textarea>
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
                        <button type="button" class="close removeField text-danger">&times;</button>
                        <label class="font-weight-bold mt-1">${data.LABELS.split(',')[i]}</label>
                        <select class="${formControl} dataCollector" 
                        ${required} ${disabled} data-catalog="${data.INPUTS.split(',')[i].split(':')[1]}"
                        data-key="${data.LABELS.split(',')[i]}" data-regex="${data.regex || 0}" data-required="${data.REQ.split(',')[i]}">
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
                        <button type="button" class="close removeField text-danger">&times;</button>
                        <label class="font-weight-bold mt-1">${data.LABELS.split(',')[i]}</label>
                        <input type="${data.INPUTS.split(',')[i]}" class="${formControl} dataCollector" 
                        placeholder="${data.PLACE.split(',')[i]}" ${required} ${disabled}
                        data-key="${data.LABELS.split(',')[i]}" data-regex="${data.regex || 0}" data-required="${data.REQ.split(',')[i]}">
                    </div>`;
                }
                $('.preview form').append( input );
              }
            }
          });
        });

        $(document).on('change', '.field', function() {
          if( $(this).val() == 'catalog' ) { 
            $('.catalog').data('required', '1');
            $('.catalog').show('fast');
          }
          else { 
            $('.catalog').data('required', '0');
            $('.catalog').hide('fast');
          } 
        });

        $(document).on('click', '.removeField', function( e ){
          e.preventDefault(); e.stopPropagation();
          $(this).parents('.form-group').remove();
          if( $('.preview form .form-group').length <= 0 ){
            $('.preview form .mx-auto').remove();
          }
        });

        $(document).on('click', '.update', function( e ){
          e.preventDefault(); e.stopPropagation();

          let inputs       = [],
              labels       = [],
              placeholders = [],
              requireds    = [],
              disableds    = [];
          
          $('.preview form .form-group').each( function(){

            labels.push( $(this).find('label').text() );

            if( $(this).find('input').length > 0 ) {
              inputs.push( $(this).find('input').attr('type') );
              placeholders.push( $(this).find('input').attr('placeholder') );
              requireds.push( $(this).find('input').data('required') );
              disableds.push( $(this).find('input').attr('disabled') ? 1 : 0 );
              

            }else if( $(this).find('select').length > 0 ) {
              inputs.push( 'select:' + $(this).find('select').data('catalog') );
              placeholders.push( $(this).find('select').find('option:disabled').val());
              requireds.push( $(this).find('select').data('required') );
              disableds.push( $(this).find('select').attr('disabled') ? 1 : 0 );

            }else {
              inputs.push( 'textarea' );
              placeholders.push( $(this).find('textarea').attr('placeholder') );
              requireds.push( $(this).find('textarea').data('required') );
              disableds.push( $(this).find('textarea').attr('disabled') ? 1 : 0 );

            }

          });
          const name = $('.formName').val().trim();
          if( inputs.length > 0  && name != '') {
            const data = {
            inputs: inputs.join(), 
            labels: labels.join(), 
            placeholders: placeholders.join(), 
            requireds: requireds.join(), 
            disableds: disableds.join(),
            name, id: upId, active: $('.default').is(':checked') ? 1 : 2
          };
            callAjax( data, 'put', 'api/forms', function(res) {

              if( !res.success ) {
                swal(res.title, res.message, 'warning');

              }else{
                swal( '¡Correcto!', res.message, 'success' );
                $(selector).click();
              }

            });
          }
        });

        $(document).on('click', '.save', function( e ){
          e.preventDefault(); e.stopPropagation();

          let inputs       = [],
              labels       = [],
              placeholders = [],
              requireds    = [],
              disableds    = [];
          
          $('.preview form .form-group').each( function(){

            labels.push( $(this).find('label').text() );

            if( $(this).find('input').length > 0 ) {
              inputs.push( $(this).find('input').attr('type') );
              placeholders.push( $(this).find('input').attr('placeholder') );
              requireds.push( $(this).find('input').data('required') );
              disableds.push( $(this).find('input').attr('disabled') ? 1 : 0 );
              

            }else if( $(this).find('select').length > 0 ) {
              inputs.push( 'select:' + $(this).find('select').data('catalog') );
              placeholders.push( $(this).find('select').find('option:disabled').val());
              requireds.push( $(this).find('select').data('required') );
              disableds.push( $(this).find('select').attr('disabled') ? 1 : 0 );

            }else {
              inputs.push( 'textarea' );
              placeholders.push( $(this).find('textarea').attr('placeholder') );
              requireds.push( $(this).find('textarea').data('required') );
              disableds.push( $(this).find('textarea').attr('disabled') ? 1 : 0 );

            }

          });
          const name      = $('.formName').val().trim();
          const cleanName = name.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
          const table     = `form_${ cleanName.toLowerCase().replace(/ /g,"_") }`;

          labels_fields = labels.map( ( name )=> {
            const cleanName = name.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
            return cleanName.replace(/ /g,"_");
          });

          if( inputs.length > 0  && name != '') {
            const data = {
            inputs: inputs.join(), 
            labels: labels.join(), 
            placeholders: placeholders.join(), 
            requireds: requireds.join(), 
            disableds: disableds.join(),
            labels_fields, table, name, active: $('.default').is(':checked') ? 1 : 2
          };
            callAjax( data, 'post', 'api/forms', function(res) {

              if( !res.success ) {
                swal(res.title, res.message, 'warning');

              }else{
                location.reload();
              }

            });
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
    header('Location: ./landing.php?error=Formularios');  
  }
}else{
  header('Location: /web');
}
?>