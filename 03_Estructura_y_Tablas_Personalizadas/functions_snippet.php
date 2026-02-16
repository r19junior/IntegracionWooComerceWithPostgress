<?php
/**
 * Snippets de Código PHP para interactuar con tablas PostgreSQL en WordPress.
 *
 * Copia estas funciones en tu archivo functions.php de tu tema hijo
 * o en un plugin personalizado.
 */

// 1. Hook para registrar datos cuando un pedido se completa
add_action( 'woocommerce_order_status_completed', 'guardar_historial_pg', 10, 1 );

function guardar_historial_pg( $order_id ) {
    global $wpdb; // Objeto de base de datos global

    // Definir nombre de tabla (sin tildes ni caracteres raros)
    $tabla = 'wp_historial_envios';

    // Obtener datos del pedido
    $order = wc_get_order( $order_id );

    // Insertar usando el método seguro de $wpdb
    // PostgreSQL Driver (pg4wp) traducirá esto correctamente.
    $resultado = $wpdb->insert(
        $tabla,
        array(
            'order_id'       => $order_id,
            'estado_anterior'=> 'procesando', // Ejemplo estático
            'estado_nuevo'   => 'completado',
            'fecha_cambio'   => current_time( 'mysql' ) // Devuelve formato Y-m-d H:i:s
        ),
        array(
            '%d', // order_id es entero
            '%s', // estado_anterior es string
            '%s', // estado_nuevo es string
            '%s'  // fecha_cambio es string/timestamp
        )
    );

    if ( false === $resultado ) {
        error_log( "Error insertando en PG: " . $wpdb->last_error );
    }
}

// 2. Función para leer datos (SELECT)
function obtener_historial( $order_id ) {
    global $wpdb;
    
    // Consulta SQL directa. 
    // NOTA: No uses comillas invertidas (`). Usa comillas simples para strings.
    $query = $wpdb->prepare(
        "SELECT * FROM wp_historial_envios WHERE order_id = %d ORDER BY fecha_cambio DESC",
        $order_id
    );

    $resultados = $wpdb->get_results( $query );
    return $resultados;
}
