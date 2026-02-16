# Tutorial: Integración de PostgreSQL con WooCommerce

Este documento detalla el procedimiento técnico para implementar una base de datos PostgreSQL en un entorno de WordPress y WooCommerce.

WordPress está diseñado nativamente para MySQL/MariaDB. Para utilizar PostgreSQL, es necesario implementar una capa de abstracción de base de datos (drop-in) que traduzca las consultas SQL.

---

## Tabla de Contenidos

1. [Requisitos del Sistema](#requisitos-del-sistema)
2. [Paso 1: Preparación del Entorno PostgreSQL](#paso-1-preparación-del-entorno-postgresql)
3. [Paso 2: Instalación de la Capa de Abstracción (PG4WP)](#paso-2-instalación-de-la-capa-de-abstracción)
4. [Paso 3: Configuración de WordPress](#paso-3-configuración-de-wordpress)
5. [Paso 4: Estructura de Datos en PostgreSQL](#paso-4-estructura-de-datos-en-postgresql)
6. [Paso 5: Desarrollo de Tablas Personalizadas (Sintaxis PostgreSQL)](#paso-5-desarrollo-de-tablas-personalizadas)

---

## Requisitos del Sistema

- **Servidor Web**: Apache, Nginx.
- **Base de Datos**: PostgreSQL 12 o superior.
- **PHP**: Versión 7.4+ con la extensión `pgsql` o `pdo_pgsql` habilitada.
- **Plugin de Abstracción**: [PostgreSQL for WordPress (PG4WP)](https://github.com/kevinoid/postgresql-for-wordpress) o similar.

---

## Paso 1: Preparación del Entorno PostgreSQL

Debemos crear la base de datos y el usuario con los permisos adecuados. Utiliza `psql` o una herramienta como pgAdmin.

### Comandos SQL (Terminal `psql`)

```sql
-- 1. Crear el usuario (rol)
CREATE USER usuario_woo WITH PASSWORD 'tu_contraseña_segura';

-- 2. Crear la base de datos con codificación UTF8
CREATE DATABASE mi_tienda_woo OWNER usuario_woo ENCODING 'UTF8';

-- 3. Asignar privilegios (si es necesario verificar permisos en esquema public)
GRANT ALL PRIVILEGES ON DATABASE mi_tienda_woo TO usuario_woo;
```

---

## Paso 2: Instalación de la Capa de Abstracción

WordPress no conectará con PostgreSQL sin un controlador específico (`db.php`).

1. **Descarga el Driver**: Obtén el código de un proyecto como `PG4WP` desde GitHub.
2. **Ubicación del Archivo**:
   - Copia el archivo `db.php` del driver en la carpeta `wp-content/` de tu instalación de WordPress.
   - La ruta final debe ser: `/var/www/html/wp-content/db.php`.
   - Si el driver requiere una carpeta `pg4wp`, colócala también en `wp-content/`.

---

## Paso 3: Configuración de WordPress

Edita el archivo `wp-config.php` para definir las credenciales de PostgreSQL.

```php
define( 'DB_NAME', 'mi_tienda_woo' );
define( 'DB_USER', 'usuario_woo' );
define( 'DB_PASSWORD', 'tu_contraseña_segura' );
define( 'DB_HOST', 'localhost' ); // O la IP de tu servidor PostgreSQL
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// Habilitar el driver de PG si requiere constante específica (depende del driver)
// define( 'PG4WP_DEBUG', true ); 
```

Una vez configurado, ejecuta el instalador de WordPress accediendo a tu dominio. El archivo `db.php` interceptará las conexiones MySQL y las redirigirá a PostgreSQL.

---

## Paso 4: Estructura de Datos en PostgreSQL

Al instalarse, WooCommerce creará sus tablas. En PostgreSQL, verifica lo siguiente:

- **Esquemas**: Por defecto, las tablas se crean en el esquema `public`.
- **Prefijos**: Se mantiene el prefijo definido en `wp-config.php` (ej: `wp_`).
- **Tipos de Datos**:
  - `BIGINT` se usa para IDs.
  - `TEXT` suele reemplazar a `LONGTEXT` de MySQL.
  - `TIMESTAMP` reemplaza a `DATETIME`.

### Consulta SQL Compatible con PostgreSQL

Para obtener productos y precios (usando casting explícito si es necesario):

```sql
SELECT 
    p.id AS product_id,
    p.post_title AS product_name,
    MAX(CASE WHEN pm.meta_key = '_price' THEN pm.meta_value END) AS price,
    MAX(CASE WHEN pm.meta_key = '_stock' THEN pm.meta_value END) AS stock
FROM 
    wp_posts p
JOIN 
    wp_postmeta pm ON p.id = pm.post_id
WHERE 
    p.post_type = 'product' 
    AND p.post_status = 'publish'
GROUP BY 
    p.id;
```

---

## Paso 5: Desarrollo de Tablas Personalizadas

Para integraciones avanzadas, la sintaxis de creación de tablas cambia respecto a MySQL (ej: `AUTO_INCREMENT` no existe, se usa `SERIAL` o `GENERATED ALWAYS AS IDENTITY`).

### 1. Crear Tabla (Sintaxis PostgreSQL)

```sql
CREATE TABLE wp_wc_garantias_extendidas (
    id SERIAL PRIMARY KEY,
    order_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    fecha_expiracion TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

-- Crear índices para mejorar rendimiento
CREATE INDEX idx_garantias_order ON wp_wc_garantias_extendidas(order_id);
```

### 2. Insertar Datos desde PHP

El uso de `$wpdb` en WordPress se mantiene igual, ya que `db.php` traduce la consulta. Sin embargo, evita usar sintaxis exclusiva de MySQL (como backticks \` \` para nombres de tablas/columnas, usa comillas dobles `"` si es necesario, o nada si son minúsculas).

```php
add_action( 'woocommerce_order_status_completed', 'registrar_garantia_pg' );

function registrar_garantia_pg( $order_id ) {
    global $wpdb;
    $order = wc_get_order( $order_id );

    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        
        // $wpdb->insert maneja la sanitización
        $wpdb->insert( 
            'wp_wc_garantias_extendidas', 
            array( 
                'order_id' => $order_id, 
                'product_id' => $product_id,
                'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+1 year'))
            ) 
        );
    }
}
```

---

## Consideraciones Finales

- **Rendimiento**: PostgreSQL maneja mejor concurrencias altas, pero WordPress está optimizado para los índices de MySQL. Monitorea las consultas lentas.
- **Plugins**: Algunos plugins de WordPress hardcodean consultas MySQL específicas. Estos podrían fallar en PostgreSQL. Testea exhaustivamente en un entorno de staging.
- **Backups**: Utiliza `pg_dump` para realizar copias de seguridad de tu base de datos.
