-- PASO 1: Script de Preparación del Entorno PostgreSQL para WooCommerce
-- Ejecutar como superusuario (postgres)

-- 1. Crear el usuario (rol) que utilizará WordPress
-- Cambia 'tu_contraseña_segura' por una contraseña real y segura.
CREATE USER usuario_woo WITH PASSWORD '1234';

-- 2. Crear la base de datos
-- Se asigna usuario_woo como dueño y se establece codificación UTF8.
CREATE DATABASE mi_tienda_woo OWNER usuario_woo ENCODING 'UTF8';

-- 3. Asignar privilegios básicos sobre la base de datos
GRANT ALL PRIVILEGES ON DATABASE mi_tienda_woo TO usuario_woo;

-- NOTA: En PostgreSQL 15+, los permisos sobre el esquema public han cambiado.
-- Es recomendable ejecutar también lo siguiente conectándose a la base de datos recién creada:
-- \c mi_tienda_woo
-- GRANT ALL ON SCHEMA public TO usuario_woo;
