<?php
session_start();
require_once '../core/QueryEngine.php';
$engine   = new QueryEngine();

$response = array();
$method   = strtolower( $_SERVER['REQUEST_METHOD'] );

switch ( $method ) {
  case 'get':
    $params = $_GET;
    $query  = "";
    $types  = "";
    $result = $engine->executeQuery( $query, $params, $types );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
  break;

  case 'post':
    $path = "../../frontend/images/uploads";
    $date = date('YmdHis');
    $names = array();
    foreach ($_FILES["file"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES["file"]["tmp_name"][$key];
            $name = basename( $date . '_' . $_FILES["file"]["name"][$key]);
            $moved = move_uploaded_file($tmp_name, "$path/$name");
            var_dump( $moved );
            var_dump( $tmp_name );
            var_dump( $path );
            var_dump( $name );
            var_dump( $_FILES );
            echo '<hr><hr>';
            array_push( $names, $name );
        }
    }
    exit();
    $keys   = array();
    $values = array( $_SESSION['BOSS'], $_SESSION['ID'], implode( ',', $names )  );
    $types  = 'iis';
    $cnt    = array();
    foreach ($_POST as $key => $value ) {
        if( $key != 'form' ) {
            array_push( $keys, $key );
            array_push( $values, $value );
            array_push( $cnt, '?' );
            $types .= 's';
        }
    }

    $query = "INSERT INTO ".$_POST['form']." 
    ( OWNER, CREADO_POR, CREADO_FECHA, FILES, ". implode(',', $keys) .")
    VALUES ( ?,?,NOW(),?,". implode( ',', $cnt ) ." )";
    
    $result = $engine->executeQuery( $query, $values, $types );

    if( empty( $result['data'] ) ) {
        header( 'Location: ../../reports.php?result=error' );
    } else {
        header( 'Location: ../../reports.php?result=success' );
    }

  break;

  case 'put':
    parse_str(file_get_contents("php://input"), $params);
    $query  = "";
    $types  = "";
    $result = $engine->executeQuery( $query, $params, $types );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
  break;

  case 'delete':
    parse_str(file_get_contents("php://input"), $params);
    $query  = "";
    $types  = "";
    $result = $engine->executeQuery( $query, $params, $types );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
  break;

  default:
    $response['success'] = false;
    $response['message'] = 'Método de petición no soportado';
  break;
}

header('Content-type: application/json');
echo json_encode( $response );
?>
