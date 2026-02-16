# Guía Paso 2: Conexión de WordPress con PostgreSQL

En este paso descargaremos WordPress y configuraremos el conector necesario para que hable con nuestra base de datos PostgreSQL.

## 1. Descarga de Archivos Necesarios

Necesitas descargar dos cosas:

1.  **WordPress**: Descarga la última versión desde [wordpress.org/download](https://wordpress.org/download/).
    - Descomprime el archivo ZIP.
    - Mueve el contenido de la carpeta `wordpress` a tu carpeta de servidor web (ej. `C:\xampp\htdocs\mi_tienda` o `/var/www/html`).

2.  **Plugin PG4WP (PostgreSQL for WordPress)**:
    - Este es el "puente" mágico. Descárgalo desde GitHub: [https://github.com/kevinoid/postgresql-for-wordpress](https://github.com/kevinoid/postgresql-for-wordpress)
    - O busca un fork mantenido si ese es muy antiguo, pero es el estándar.

## 2. Instalación del Driver (CRÍTICO)

⚠️ **IMPORTANTE**: Debes hacer esto **ANTES** de ejecutar la instalación de WordPress.

1.  Ve a la carpeta donde pusiste WordPress (ej. `mi_tienda`).
2.  Entra a la carpeta `wp-content`.
3.  Del archivo ZIP de `pg4wp` que descargaste, copia la carpeta `pg4wp` dentro de `wp-content`.
4.  Dentro de `wp-content/pg4wp`, encontrarás un archivo llamado `db.php`.
5.  **Copia** (no muevas, copia) ese archivo `db.php` y pégalo directamente en `wp-content`.

La estructura debe quedar así:
```
mi_tienda/
├── wp-admin/
├── wp-includes/
├── wp-content/
│   ├── pg4wp/       <-- Carpeta del plugin
│   ├── db.php       <-- Archivo copiado (driver)
│   ├── plugins/
│   └── themes/
├── index.php
└── ...
```

## 3. Configuración de `wp-config.php`

WordPress trae un archivo de ejemplo. Vamos a usar uno configurado para PostgreSQL.

1.  En la carpeta raíz de tu WordPress, busca `wp-config-sample.php`.
2.  Renómbralo a `wp-config.php` (o usa el archivo `wp-config-pg.php` que te he adjuntado en esta carpeta, renombrándolo).
3.  Ábrelo con un editor de texto (Notepad, VS Code) y edita los datos de conexión:

```php
// Nombre de la base de datos creada en el Paso 1
define( 'DB_NAME', 'mi_tienda_woo' );

// Usuario Creado en el Paso 1
define( 'DB_USER', 'usuario_woo' );

// Contraseña del usuario
define( 'DB_PASSWORD', '1234' );

// Servidor (localhost si tienes PostgreSQL en la misma máquina)
define( 'DB_HOST', 'localhost' );

// Forzar el uso de PG4WP (si el driver lo requiere)
define( 'PG4WP_AUTO_ERROR_SCANS', false );
define( 'PG4WP_CHARSET', 'utf8' );
```

## 4. Ejecutar la Instalación

1.  Abre tu navegador y ve a `http://localhost/mi_tienda` (o la ruta que hayas configurado).
2.  Si hiciste bien el paso del `db.php`, WordPress no te pedirá datos de MySQL, sino que intentará conectar a PostgreSQL usando los datos de `wp-config.php`.
3.  Deberías ver la pantalla de "Bienvenido a WordPress".
4.  Completa el título del sitio, tu usuario administrador y correo.
5.  ¡Instalar WordPress!

## 5. Instalación de WooCommerce

Una vez dentro del panel de administración (wp-admin):

1.  Ve a **Plugins > Añadir nuevo**.
2.  Busca "WooCommerce".
3.  Instalar y Activar.
4.  Sigue el asistente de configuración de la tienda.
    - Gracias a PostgreSQL, las tablas de WooCommerce se crearán automáticamente en tu base de datos `mi_tienda_woo`.

---
**Solución de Problemas Comunes**:
- **"Error establishing a database connection"**: Revisa que el servicio de PostgreSQL esté corriendo y que la contraseña en `wp-config.php` sea correcta.
- **Pantalla en blanco**: Revisa que tengas instalada la extensión `php_pgsql` o `php_pdo_pgsql` en tu archivo `php.ini`.

---

# ¡Excelente noticia!

Si estás viendo esa pantalla de selección de idioma, significa que **has superado el obstáculo técnico más difícil**.

Esa imagen confirma lo siguiente:

1.  **PHP ya reconoce PostgreSQL**: El error de la extensión desapareció.
2.  **El driver PG4WP está funcionando**: WordPress ha detectado el archivo `db.php` en `wp-content` y, en lugar de darte un error de base de datos, ha procedido a la instalación.
3.  **La conexión es exitosa**: WordPress ya se comunicó con tu base de datos PostgreSQL para verificar que puede empezar a escribir las tablas.

## ¿Qué sigue ahora?

Solo debes continuar con el flujo normal de instalación de WordPress:

1.  **Selecciona tu idioma** (Español) y haz clic en **Continuar**.
2.  **Información necesaria**: Te pedirá el título del sitio, tu nombre de usuario administrador, una contraseña y tu correo electrónico.
3.  **Finalizar**: Haz clic en "Instalar WordPress".

### Un último consejo de "pro"

Una vez que entres al escritorio (Dashboard), ve a **Plugins > Añadir nuevo** y busca **WooCommerce** para completar tu objetivo original. Al activarlo, WooCommerce creará automáticamente sus tablas en PostgreSQL sin que tengas que hacer nada manual.
