<?php
session_start();
require_once '../core/QueryEngine.php';
$engine   = new QueryEngine();

$response = array();
$method   = strtolower( $_SERVER['REQUEST_METHOD'] );

switch ( $method ) {

  case 'post':
    $query  = "INSERT INTO log_asistencia (LOGA_INICIO, LOGA_USR) VALUES (NOW(),".$_SESSION["ID"].")";
    $result = $engine->executeQuery( $query);
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];

  break;

  case 'put':
    parse_str(file_get_contents("php://input"), $params);
    $query  = "UPDATE log_asistencia SET LOGA_FIN = NOW() WHERE LOGA_ID = ?";
    $types  = "i";
    $result = $engine->executeQuery( $query, $params, $types, 1 );
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
    $response['meta'] = $result['meta'];
  break;

  default:
    $response['success'] = false;
    $response['message'] = 'Método de petición no soportado';
  break;
}

header('Content-type: application/json');
echo json_encode( $response );
?>