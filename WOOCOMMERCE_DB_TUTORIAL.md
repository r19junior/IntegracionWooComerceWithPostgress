# Tutorial: Crear e Integrar una Base de Datos con WooCommerce

Este tutorial te guiará paso a paso en el proceso de creación de una base de datos para WooCommerce y cómo interactuar con ella.

WooCommerce funciona sobre WordPress, por lo que "la base de datos de WooCommerce" es técnicamente la base de datos de WordPress con tablas adicionales específicas para comercio electrónico.

---

## Tabla de Contenidos

1. [Requisitos Previos](#requisitos-previos)
2. [Paso 1: Diseño y Planificación](#paso-1-diseño-y-planificación)
3. [Paso 2: Crear la Base de Datos (MySQL/MariaDB)](#paso-2-crear-la-base-de-datos)
4. [Paso 3: Conexión con WordPress y WooCommerce](#paso-3-conexión-con-wordpress-y-woocommerce)
5. [Paso 4: Entendiendo la Estructura de Datos de WooCommerce](#paso-4-entendiendo-la-estructura-de-datos-de-woocommerce)
6. [Paso 5: Integración Avanzada (Tablas Personalizadas)](#paso-5-integración-avanzada-tablas-personalizadas)

---

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:
- **Servidor Web**: Apache, Nginx, o un entorno local como XAMPP/Laragon.
- **Base de Datos**: MySQL (v5.6+) o MariaDB (v10.1+).
- **PHP**: v7.4 o superior.
- **Acceso**: Terminal o phpMyAdmin.

---

## Paso 1: Diseño y Planificación

WooCommerce utiliza la estructura de tablas de WordPress (`wp_posts`, `wp_postmeta`) para almacenar productos y pedidos, pero añade sus propias tablas optimizadas para búsquedas y atributos.

Si tu objetivo es **sólo instalar WooCommerce**, ve al Paso 2.
Si tu objetivo es **integrar una base de datos externa** (ej. un ERP o CRM), necesitarás planificar cómo sincronizar los datos (usualmente vía REST API o acceso directo SQL).

---

## Paso 2: Crear la Base de Datos

Tienes dos formas principales de crear la base de datos necesaria para WooCommerce.

### Opción A: Usando phpMyAdmin (Gráfico)

1. Abre **phpMyAdmin** en tu navegador (`http://localhost/phpmyadmin` o la URL de tu hosting).
2. Haz clic en la pestaña **"Bases de datos"**.
3. En "Crear base de datos", escribe un nombre (ej: `mi_tienda_woo`).
4. Selecciona el cotejamiento (collation): `utf8mb4_unicode_ci` (recomendado para soporte completo de caracteres).
5. Haz clic en **Crear**.

### Opción B: Usando la Terminal (SQL)

Si tienes acceso por línea de comandos:

```sql
-- Conéctate a MySQL
mysql -u root -p

-- Crea la base de datos
CREATE DATABASE mi_tienda_woo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crea un usuario seguro (opcional pero recomendado)
CREATE USER 'usuario_woo'@'localhost' IDENTIFIED BY 'tu_contraseña_segura';

-- Otorga permisos
GRANT ALL PRIVILEGES ON mi_tienda_woo.* TO 'usuario_woo'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## Paso 3: Conexión con WordPress y WooCommerce

Una vez creada la base de datos vacía, debemos conectarla.

1. **Descarga WordPress**: Bájalo desde [wordpress.org](https://wordpress.org/download/).
2. **Configura `wp-config.php`**:
   Renombra el archivo `wp-config-sample.php` a `wp-config.php` y edita los detalles de conexión:

   ```php
   // Variables de la base de datos
   define( 'DB_NAME', 'mi_tienda_woo' );
   define( 'DB_USER', 'usuario_woo' );
   define( 'DB_PASSWORD', 'tu_contraseña_segura' );
   define( 'DB_HOST', 'localhost' );
   define( 'DB_CHARSET', 'utf8mb4' );
   ```

3. **Ejecuta la Instalación**:
   Accede a tu sitio web. WordPress instalará sus tablas base.

4. **Instala WooCommerce**:
   - Ve a `Plugins > Añadir nuevo`.
   - Busca "WooCommerce" e instálalo.
   - Actívalo y sigue el asistente de configuración.

   > **Nota Importante**: Al finalizar el asistente, WooCommerce creará automáticamente sus tablas específicas en la base de datos (ej: `wp_wc_orders`, `wp_wc_order_stats`, etc.).

---

## Paso 4: Entendiendo la Estructura de Datos de WooCommerce

Para integrar o consultar datos, debes conocer dónde se guarda cada cosa. A partir de WooCommerce 8.0+ (HPOS - High Performance Order Storage), los datos se están moviendo a tablas propias.

### Tablas Clave:

| Tabla | Descripción |
| :--- | :--- |
| `wp_posts` | Almacena Productos (post_type='product') y Cupones. |
| `wp_postmeta` | Metadatos de productos (precio, SKU, stock). |
| `wp_wc_orders` | (Nuevo) Almacena la información principal de los pedidos. |
| `wp_wc_order_addresses` | Direcciones de facturación y envío. |
| `wp_wc_product_meta_lookup` | Tabla optimizada para búsquedas rápidas de productos. |

### Ejemplo de Consulta SQL de Integración

Si quieres obtener un listado de productos y sus precios directamente desde la base de datos (para un reporte externo):

```sql
SELECT 
    p.ID as product_id,
    p.post_title as product_name,
    MAX(CASE WHEN pm.meta_key = '_price' THEN pm.meta_value END) as price,
    MAX(CASE WHEN pm.meta_key = '_stock' THEN pm.meta_value END) as stock
FROM 
    wp_posts p
JOIN 
    wp_postmeta pm ON p.ID = pm.post_id
WHERE 
    p.post_type = 'product' 
    AND p.post_status = 'publish'
GROUP BY 
    p.ID;
```

---

## Paso 5: Integración Avanzada (Tablas Personalizadas)

A veces necesitas guardar datos extra que no encajan bien en la estructura estándar (ej: puntos de fidelidad complejos, historial de garantías).

### 1. Crear una Tabla Personalizada

Puedes ejecutar esto en tu gestor SQL o mediante un plugin personalizado al activarse:

```sql
CREATE TABLE wp_wc_garantias_extendidas (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    order_id bigint(20) NOT NULL,
    product_id bigint(20) NOT NULL,
    fecha_expiracion datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY  (id)
);
```

### 2. Insertar Datos desde WooCommerce (Hook)

Para "integrar" esta tabla, usa los ganchos (hooks) de WooCommerce en tu archivo `functions.php`:

```php
add_action( 'woocommerce_order_status_completed', 'registrar_garantia_al_completar' );

function registrar_garantia_al_completar( $order_id ) {
    global $wpdb;
    $order = wc_get_order( $order_id );

    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        
        // Insertar en nuestra tabla personalizada
        $wpdb->insert( 
            'wp_wc_garantias_extendidas', 
            array( 
                'order_id' => $order_id, 
                'product_id' => $product_id,
                'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+1 year')) // 1 año de garantía
            ) 
        );
    }
}
```

---

## Conclusión

Has creado una base de datos para WooCommerce y aprendido cómo integrarla con datos personalizados. Recuerda:

1. **Respalda siempre** tu base de datos antes de consultas manuales `INSERT/UPDATE`.
2. Utiliza la clase `$wpdb` de WordPress para consultas seguras.
3. Intenta usar la **REST API de WooCommerce** para integraciones externas en lugar de acceso directo a la BD, siempre que sea posible, para mantener la integridad de los datos.
