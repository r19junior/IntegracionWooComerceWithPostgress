<?php
/**
 * Configuración base de WordPress para PostgreSQL
 * Renombra este archivo a wp-config.php una vez editado.
 */

// ** Configuración de MySQL - Solicita estos datos a tu proveedor de alojamiento web ** //
// ** En este caso, usaremos PostgreSQL gracias al plugin PG4WP ** //

/** El nombre de tu base de datos de WordPress */
define( 'DB_NAME', 'mi_tienda_woo' );

/** Tu nombre de usuario de PostgreSQL */
define( 'DB_USER', 'usuario_woo' );

/** Tu contraseña de PostgreSQL */
define( 'DB_PASSWORD', '1234' ); // Cambiar por tu contraseña real

/** Host de PostgreSQL (localhost es lo habitual) */
define( 'DB_HOST', 'localhost' );

/** Codificación de caracteres para la base de datos. */
define( 'DB_CHARSET', 'utf8' );

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define( 'DB_COLLATE', '' );

/**
 * Claves únicas de autentificación y sal.
 *
 * Cambia esto por frases únicas diferentes.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress.org}
 */
define( 'AUTH_KEY',         'pon aqui tu frase aleatoria' );
define( 'SECURE_AUTH_KEY',  'pon aqui tu frase aleatoria' );
define( 'LOGGED_IN_KEY',    'pon aqui tu frase aleatoria' );
define( 'NONCE_KEY',        'pon aqui tu frase aleatoria' );
define( 'AUTH_SALT',        'pon aqui tu frase aleatoria' );
define( 'SECURE_AUTH_SALT', 'pon aqui tu frase aleatoria' );
define( 'LOGGED_IN_SALT',   'pon aqui tu frase aleatoria' );
define( 'NONCE_SALT',       'pon aqui tu frase aleatoria' );

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar múltiples blogs en una sola base de datos.
 * Emplea solo números, letras y guion bajo.
 */
$table_prefix = 'wp_';

/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define( 'WP_DEBUG', false );

/** Configuración específica para PG4WP (PostgreSQL Driver) */
define( 'PG4WP_AUTO_ERROR_SCANS', false );
define( 'PG4WP_CHARSET', 'utf8' );

/* ¡Eso es todo, deja de editar! Feliz publicación. */

/** Ruta absoluta al directorio de WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Configura las variables de WordPress y ficheros incluidos. */
require_once ABSPATH . 'wp-settings.php';
