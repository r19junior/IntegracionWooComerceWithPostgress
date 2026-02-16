# Guía Maestra para Rodrigo: Tu Tienda en PostgreSQL

Hola Rodrigo, entiendo que te sientas perdido con tantas carpetas y pasos. Esta es tu **GUÍA DEFINITIVA**.

Olvida todo lo anterior si te confundió. Aquí empezamos de nuevo, paso a paso, usando tus datos:
- **Base de Datos**: `t`
- **Usuario**: `r`
- **Contraseña**: `1234`

---

## 🛑 Paso 1: Crear la Base de Datos (PostgreSQL)

Necesitamos crear el "terreno" donde vivirá tu tienda.

1.  Abre tu herramienta de base de datos (pgAdmin o terminal `psql`).
2.  Ejecuta este código SQL exacto:

```sql
-- 1. Crear al usuario r
CREATE USER r WITH PASSWORD '1234';

-- 2. Crear la base de datos t
CREATE DATABASE t OWNER r ENCODING 'UTF8';

-- 3. Darle llaves a r
GRANT ALL PRIVILEGES ON DATABASE t TO r;
```

✅ **Resultado esperado**: Tienes una base de datos vacía llamada `t`.

---

## 📥 Paso 2: Descargar y Preparar WordPress

No instales nada todavía. Primero preparamos los archivos.

1.  **Descarga WordPress**: Bájalo de [wordpress.org](https://wordpress.org/download/) y descomprímelo en tu carpeta de servidor (ej: `C:\xampp\htdocs\tienda` o `/var/www/html/tienda`).
2.  **Descarga el conector PG4WP**: [Haz clic aquí para ir al repositorio](https://github.com/kevinoid/postgresql-for-wordpress) y descárgalo como ZIP.
3.  **Instala el conector**:
    - Ve a la carpeta `wp-content` de tu nuevo WordPress.
    - Copia la carpeta `pg4wp` del ZIP ahí dentro.
    - **IMPORTANTE**: Saca el archivo `db.php` que está dentro de `pg4wp` y pégalo **directamente** en `wp-content`.
    - Debe quedar así:
      ```
      tienda/
      └── wp-content/
          ├── db.php       <-- ¡Este archivo hace la magia!
          ├── pg4wp/       <-- Carpeta con el resto del plugin
          ├── plugins/
          └── themes/
      ```

---

## ⚙️ Paso 3: Configurar la Conexión (`wp-config.php`)

WordPress no sabe que existe PostgreSQL, hay que decírselo.

1.  En la carpeta principal de tu `tienda`, busca el archivo `wp-config-sample.php`.
2.  Cámbiale el nombre a `wp-config.php`.
3.  Borra todo lo que tenga sobre MySQL y pon esto:

```php
define( 'DB_NAME', 't' ); // Base de datos t
define( 'DB_USER', 'r' ); // Usuario r
define( 'DB_PASSWORD', '1234' ); // Tu contraseña
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// Configuración especial para PostgreSQL
define( 'PG4WP_AUTO_ERROR_SCANS', false );
define( 'PG4WP_CHARSET', 'utf8' );
```

---

## 🚀 Paso 4: ¡Instalar!

1.  Abre tu navegador: `http://localhost/tienda` (o tu dominio).
2.  Si todo salió bien, verás el logo de WordPress pidiéndote idioma.
3.  Sigue los pasos (Título del sitio, tu usuario admin, etc.).
4.  ¡Listo! Ya tienes WordPress corriendo sobre PostgreSQL.

---

## 📦 Paso 5: Instalar WooCommerce

1.  Entra al escritorio de WordPress (`/wp-admin`).
2.  Ve a **Plugins > Añadir nuevo**.
3.  Busca "WooCommerce", instálalo y actívalo.
4.  WooCommerce detectará automáticamente que estás en PostgreSQL y creará sus tablas (pedidos, productos, clientes) en tu base de datos `t`.

---

## 🛠️ Paso Extra: Tus Tablas Personalizadas

Si necesitas guardar datos extra (como garantías o historiales), usa este SQL en pgAdmin:

```sql
-- Conéctate a la base de datos 't' antes de ejecutar esto
CREATE TABLE wp_historial_envios (
    envio_id SERIAL PRIMARY KEY,
    order_id BIGINT NOT NULL,
    estado_nuevo VARCHAR(50),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

¡Y eso es todo! Has convertido WordPress para usar PostgreSQL con tu usuario `r`.
