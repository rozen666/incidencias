<?php
session_start();
require_once '../core/QueryEngine.php';
$engine   = new QueryEngine();

$response = array();
$method   = strtolower( $_SERVER['REQUEST_METHOD'] );

switch ( $method ) {
  case 'get':
    $params = $_GET;

    if( !empty( $params['cat'] ) ) {
        $query  = "SELECT ID, NOMBRE, ESTADO FROM ".$params['cat']." ORDER BY ID";
        $result = $engine->executeQuery( $query );
    
    } elseif( !empty( $params['form'] ) ){
        $query  = "SELECT ID, NOMBRE, ESTADO FROM ".$params['form']." WHERE ESTADO = 1 ORDER BY ID";
        $result = $engine->executeQuery( $query );

    } else{
      $query  = "SELECT MCAT_NOMBRE AS 'NAME', MCAT_CAT_NOMBRE AS 'CAT' FROM 
      mgr_catalogs WHERE MCAT_ESTADO = 1";
      $result = $engine->executeQuery( $query );
    }
    
    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];
    $response['meta']    = $result['meta'];
  break;

  case 'post':
    $params = $_POST;
    $data   = array( $params['name'], $params['catname'], $_SESSION['ID'] );
    $query  = "INSERT INTO mgr_catalogs (MCAT_NOMBRE, MCAT_CAT_NOMBRE, MCAT_CREADO_POR, MCAT_CREADO_FECHA)
                VALUES( ?, ?, ?, NOW() )";
    $types  = "ssi";
    $result = $engine->executeQuery( $query, $data, $types, 1 );

    if( empty( $result['data'] ) ) {
        $response['title']   = '¡Ocurrió algo extraño!';
        $response['success'] = !empty( $result['data'] );
        $response['message'] = $result['message'];
        $response['data']    = $result['data'];    
        $response['meta']    = $result['meta'];    

    } else{
        $sub_query = "CREATE TABLE ".$params['catname']." (
        ID INT(1) NOT NULL AUTO_INCREMENT,
        NOMBRE VARCHAR(30) NOT NULL,
        CREADO_POR INT(8) NOT NULL,
        CREADO_FECHA DATETIME NOT NULL,
        MODIF_POR INT(8) NULL DEFAULT NULL,
        MODIF_FECHA DATETIME NULL DEFAULT NULL,
        ESTADO INT(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (ID),
        UNIQUE INDEX NOMBRE (NOMBRE),
        INDEX `".strtoupper($params['catname'])."_CREADO POR` (CREADO_POR),
        INDEX `".strtoupper($params['catname'])."_MODIFICADO POR` (MODIF_POR),
        INDEX `".strtoupper($params['catname'])."_ESTADO` (ESTADO),
        CONSTRAINT `".strtoupper($params['catname'])."_ESTADO` FOREIGN KEY (ESTADO) REFERENCES estados (EDO_ID) ON UPDATE CASCADE,
        CONSTRAINT `".strtoupper($params['catname'])."_CREADO POR` FOREIGN KEY (CREADO_POR) REFERENCES users (USR_ID) ON UPDATE CASCADE,
        CONSTRAINT `".strtoupper($params['catname'])."_MODIFICADO POR` FOREIGN KEY (MODIF_POR) REFERENCES users (USR_ID) ON UPDATE CASCADE)
        COLLATE='latin1_swedish_ci'
        ENGINE=InnoDB";
        
        $sub_result = $engine->executeQuery( $sub_query );

        $check_result = $engine->executeQuery( "SHOW TABLES LIKE '".$params['catname']."'", array(), '', 1 );

        if( empty( $check_result['data'] ) ) {
            $response['title']   = '¡Ocurrió algo extraño!';
            $response['success'] = false;
            $response['message'] = 'No fue posible crear la tabla';
            $response['data']    = null;   
            $response['meta']    = $check_result['meta'];    

        }else{
            $counter = 1;
            foreach( $params['options'] as $option ) {
                $in_params = array( $option, $_SESSION['ID'] );
                $in_types  = 'si';
                $in_query  = "INSERT INTO ".$params['catname']." (NOMBRE, CREADO_POR, CREADO_FECHA)
                VALUES( ?, ?, NOW() )";

                $in_result = $engine->executeQuery( $in_query, $in_params, $in_types );


                if( !empty( $in_result['data'] ) ) {
                    $counter++;
                }
                $response['success'] = true;
                $response['message'] = 'Se creó la tabla y se insetaron ' . $counter . ' de ' . count( $params['options'] );
                $response['data']    = null;  
            }

        }

    }

    $response['title']   = '¡Ocurrió algo extraño!';
    $response['success'] = !empty( $result['data'] );
    $response['message'] = $result['message'];
    $response['data']    = $result['data'];

  break;

  case 'put':
    parse_str(file_get_contents("php://input"), $params);


    foreach( $params['options'] as $option ) {
        if( empty( $option['id'] ) && !isset( $option['id'] ) ){
            $query   = "INSERT INTO ".$params['catname']."(NOMBRE, CREADO_POR, CREADO_FECHA) 
            VALUES( ?, ?, NOW() )";
            $data    = array( $option['val'], $_SESSION['ID'] );
            $types  = 'si';    

        } else{
            $query  = "UPDATE ".$params['catname']." SET NOMBRE = ?, ESTADO = ? WHERE ID = ?";
            $data   = array( $option['val'], $option['active'], $option['id'] );
            $types  = "sii";
        }

        $engine->executeQuery( $query, $data, $types);
    }

    $response['success'] = true;
    $response['message'] = 'Se actualizaron los campos correctamente';
    $response['data']    = null;
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
