var dataCollector = function( that, selectors = false ) {

    let boxCollector = $( that ).parents( 'form' ),
        obj          = {},
        errors       = 0;
        selectorsArr = [];
  
    boxCollector.find('.dataCollector').removeClass('border border-danger');
    boxCollector.find('.errorMessage').remove();
    boxCollector.find( '.dataCollector' ).each( function(){
      let tag;
  
      if( $( this ).is('input[type="checkbox"]') ){ tag = 'checkbox'; }
      else if(  $( this ).is('textarea') ){ tag = 'textarea'; }
      else if(  $( this ).is('select')   ){ tag = 'select'; }
      else if(  $( this ).is('input')  ){ tag = 'input'; }
      else if(  $( this ).is('ul')       ){ tag = 'ul'; }
      else { tag = null; }
  
      if( !tag ){
        let error = '<small class="form-text text-muted errorMessage">' +
                        'El tipo de dato no fue identificado' +
                    '</small>';
        $(this).after( error );
        $(this).addClass('border border-danger');
        errors++;
        if( selectors ) { selectorsArr.push( this ); }
  
      }else{
        let required = parseInt( $(this).data('required') ),
            regex    = $(this).data('regex'),
            key      = $(this).data('key'),
            value    = tag === 'input'    ? $(this).val().trim() :
                       tag === 'textarea' ? $(this).val().trim() :
                       tag === 'select'   ? $(this).val() :
                       tag === 'checkbox' ? ( $(this).is(':checked') ? 1 : 0 ) : null;
  
        if( tag === 'ul' ){
  
          if( $(this).find('li').length === 0 || !$(this).find('li') ){
            value = 0;
  
          }else{
            let temp = [];
            $(this).find('li').each(function(){
              temp.push( $(this).data('id') );
            });
            value = temp.join(',');
          }
        }
        
        
        if( required === 1 ){
          if( value === 0 && tag === 'ul' ){
            let error = '<small class="form-text text-muted errorMessage">' +
                            'Debe haber por lo menos 1 selección' +
                        '</small>';
            $(this).after( error );
            $(this).addClass('border border-danger');
            errors++;
            if( selectors ) { selectorsArr.push( this ); }
  
          }else if( !value ){
            let error = '<small class="form-text text-muted errorMessage">' +
                            'Este valor no puede estar vacío' +
                        '</small>';
            $(this).after( error );
            $(this).addClass('border border-danger');
            errors++;
            if( selectors ) { selectorsArr.push( this ); }
  
          }else{
            if( regex == 0 ){
                obj[ key ] = value;
            }else{
  
              let reg = regexValidation( regex, value );
  
              if( reg.valid ){
                obj[ key ] = value;
  
              }else{
                let error = '<small class="form-text text-muted errorMessage">' +
                                reg.message +
                            '</small>';
                $(this).after( error );
                $(this).addClass('border border-danger');
                errors++;
                if( selectors ) { selectorsArr.push( this ); }
              }
            }
  
          }
  
        }else{
          obj[ key ] = value;
        }
  
  
  
  
      }
  
    });
  
    obj[ 'errors' ] = errors;
    obj[ 'selectors' ] = selectorsArr;
    return obj;
  }
  var regexValidation = function( reg, val ){
    var ret_obj = {},
        test    = false,
        pattern = '',
        error   = '';
    switch ( reg ) {
      case 'mail':
        pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        error   = 'El formato del correo es inválido';
      break;
  
      case 'password':
        pattern = /^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.["@$!%?&])[A-Za-z\d"@$!%?&]{8,}$/;
        error   = 'El formato de la contraseña debe contener una mayúscula';
        error  += ' una minúscula, un dígito, un caracter especial y 8 caracteres como mínimo';
      break;
  
      case 'words':
        pattern = /^[\w]+$/;
        error   = 'Ãšnicamente se permiten caracteres alfabéticos';
      break;
  
      case 'numeric':
        pattern = /^[\d]+$/
        error   = 'Únicamente se permiten números';
      break;
  
      default:
        error = 'Patrón de expresión regular no identificado, repórtalo con el administrador';
      break;
  
    }
    ret_obj['message'] = error;
    ret_obj['valid']  = pattern == '' ? true : pattern.test( val );
    return ret_obj;
  };
  var callAjax = function( data, method, path, func, cover = true, async = true, formData = false ){
  
    var ajax = {};
    ajax[ 'data' ]   = data;
    ajax[ 'method' ] = method;
    ajax[ 'url' ]    = 'backend/' + path + '.php';
    ajax[ 'async' ]  = async;
    ajax[ 'cache' ]  = false;
  
    ajax[ 'error' ]      = function( e ){
      Swal( '¡Ocurrió un error!', `${ e.statusText}(${e.status})`, 'error' );
    };
    ajax[ 'beforeSend' ] = function(){
      if( cover ) { $('.ajax-layer').addClass('d-flex'); }
    };
  
    ajax[ 'complete' ]  = function(){
      if( cover ){ $('.ajax-layer').removeClass('d-flex'); }
    };

    if( formData ) {
      ajax[ 'contentType' ] = false;
      ajax[ 'processData' ] = false;
    }
  
    ajax['success']     = func;
    $.ajax( ajax );
  }


  var getScaling = function( id, rawTable ) {
    const cleanName = rawTable.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
    const table     = `form_${ cleanName.toLowerCase().replace(/ /g,"_") }`;
    count = 0;
    callAjax( { id, table }, 'get', 'api/scalings_num', function( res ){

      if( res.data ) {
        count = res.data[0].COUNT;
      }
      
    }, true, false);

    return count;
  };
  
  $( ()=>{
    $( window ).scroll(function(){
      $('.ajax-layer').css('min-height', $('html')[0].offsetHeight);
    });
  
    $( window ).resize(function(){
      $('.ajax-layer').css('min-height', $('html')[0].offsetHeight);
    });

    $(document).on("click", "#firmarse", function(){
      callAjax(null, "POST", "api/login", function(respuesta){
          if(!respuesta.success){
            swal(respuesta.title, respuesta.messagge, "warning");
          }
          else{
            location.reload();
          }
      });
    });

    $(document).on("click", "#desfirmarse", function(){
      const id= {id:$(this).data("id")};
        callAjax(id, "PUT", "api/login", function(respuesta){
          if(!respuesta.success){
            swal(respuesta.title, respuesta.messagge, "warning");
          }
          else{
            location.href="backend/core/logout.php";
          }
      });
    });




    
  });