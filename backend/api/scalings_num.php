<?php
session_start();
require_once '../core/QueryEngine.php';
$engine   = new QueryEngine();

$response = array();
$method   = strtolower( $_SERVER['REQUEST_METHOD'] );

switch ( $method ) {
  case 'get':
    $params = $_GET;
    $query  = "SELECT COUNT( ID ) AS 'COUNT' FROM ".$params['table']." WHERE `OWNER` = ? AND ESTADO = 1";
    $types  = "i";
    $data   = array( $params['id'] );

    $result = $engine->executeQuery( $query, $data, $types, 1 );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
  break;

  case 'post':
    $params = $_POST;
    $query  = "";
    $types  = "";
    $result = $engine->executeQuery( $query, $params, $types );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];

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
