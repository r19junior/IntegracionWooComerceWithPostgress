# Guía Paso 1: Preparación del Entorno PostgreSQL

Esta carpeta contiene los archivos necesarios para cumplir con el **Paso 1** del tutorial de integración.

El objetivo es crear la base de datos `mi_tienda_woo` y el usuario `usuario_woo` que WordPress utilizará para conectarse.

## Archivos Incluidos

- `init_db.sql`: Script con los comandos SQL necesarios.

## Instrucciones de Ejecución

Tienes dos opciones principales para ejecutar estos comandos:

### Opción A: Usando la Terminal (psql)

Si tienes PostgreSQL instalado y accesible desde la línea de comandos:

1.  Abre tu terminal (PowerShell o CMD).
2.  Inicia sesión como el superusuario `postgres`:
    ```bash
    psql -U postgres
    ```
3.  Una vez dentro de la consola de `postgres=#`, copia y pega el contenido del archivo `init_db.sql`.
4.  Alternativamente, puedes ejecutar el archivo directamente desde la terminal sin entrar a la consola interactiva:
    ```bash
    psql -U postgres -f init_db.sql
    ```

### Opción B: Usando pgAdmin (Interfaz Gráfica)

1.  Abre **pgAdmin 4**.
2.  Conéctate a tu servidor de base de datos.
3.  Haz clic derecho sobre el servidor o la base de datos `postgres` y selecciona **Query Tool** (Herramienta de Consulta).
4.  Abre el archivo `init_db.sql` o copia su contenido en el editor.
5.  Haz clic en el botón de **Ejecutar** (icono de "Play" ▶️ o tecla F5).
6.  Verifica en el panel izquierdo (haz click derecho -> Refresh) que la nueva base de datos `mi_tienda_woo` aparezca listada.

## Verificación

Para confirmar que todo está listo, intenta conectarte con el nuevo usuario:

```bash
psql -U usuario_woo -d mi_tienda_woo -h localhost
```
(Te pedirá la contraseña que definiste en el script).

---
> **Nota de Seguridad**: Recuerda cambiar `'tu_contraseña_segura'` en el archivo `init_db.sql` por una contraseña real antes de ejecutarlo en un servidor de producción.
