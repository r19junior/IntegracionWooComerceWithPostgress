# Guía Paso 3: Estructura de Datos y Tablas Personalizadas (PostgreSQL)

Una vez que WordPress está corriendo sobre PostgreSQL, es importante entender las diferencias en cómo se almacenan y crean los datos.

## 1. Estructura Estándar de WooCommerce en PostgreSQL

WooCommerce no cambia su lógica interna, pero la base de datos subyacente sí cambia sus tipos de datos.

| Concepto | MySQL (Estándar) | PostgreSQL (Tu instalación) |
| :--- | :--- | :--- |
| **Tablas Core** | `wp_posts`, `wp_postmeta` | `wp_posts`, `wp_postmeta` (Igual) |
| **Tablas Pedidos** | `wp_wc_orders` (WC 8.0+) | `wp_wc_orders` |
| **Incrementales** | `AUTO_INCREMENT` | `SERIAL` o `GENERATED ALWAYS AS IDENTITY` |
| **Texto Largo** | `LONGTEXT` | `TEXT` |
| **Fechas** | `DATETIME` | `TIMESTAMP` |

## 2. Creación de Tablas Personalizadas

Si necesitas crear tablas adicionales para tu plugin o integración, **NO puedes usar sintaxis MySQL**.

### Ejemplo Incorrecto (MySQL) ❌
```sql
CREATE TABLE wp_mi_tabla (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- AUTO_INCREMENT no existe en PG
    nombre VARCHAR(255)
);
```

### Ejemplo Correcto (PostgreSQL) ✅
Guarda este código en un archivo `.sql` o ejecútalo en tu terminal `psql`:

```sql
CREATE TABLE wp_mi_tabla_personalizada (
    id SERIAL PRIMARY KEY,              -- 'SERIAL' crea un entero autoincremental
    pedido_id BIGINT NOT NULL,
    codigo_rastreo TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear índice para búsquedas rápidas
CREATE INDEX idx_mi_tabla_pedido ON wp_mi_tabla_personalizada(pedido_id);
```

## 3. Integración desde PHP (WordPress)

El plugin `pg4wp` traduce muchas cosas, pero para tablas personalizadas es mejor escribir código "neutro" o compatible.

### Insertar Datos
Usa `$wpdb->insert`, que es seguro y compatible con ambos motores.

```php
global $wpdb;
$tabla = 'wp_mi_tabla_personalizada';

$wpdb->insert(
    $tabla,
    array(
        'pedido_id' => 12345,
        'codigo_rastreo' => 'PG-99887766'
    ),
    array( '%d', '%s' ) // Formatos: %d (entero), %s (string)
);
```

### Consultas SQL Directas (`$wpdb->get_results`)
Si escribes SQL a mano, evita usar comillas invertidas (\`) que son exclusivas de MySQL. Usa comillas dobles `"` para nombres de tablas/columnas si es necesario, o nada.

**Incorrecto (MySQL)**:
`SELECT * FROM `wp_posts` WHERE `ID` = 1`

**Correcto (PostgreSQL/Estándar)**:
`SELECT * FROM wp_posts WHERE ID = 1`

---
### Tarea: Ejecuta el script SQL
En esta carpeta encontrarás `tablas_extra_pg.sql`. Ejecútalo en tu base de datos `mi_tienda_woo` para añadir una tabla de ejemplo de "Historial de Envíos".
