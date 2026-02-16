-- PASO 3: Script SQL para Tablas Personalizadas en PostgreSQL

-- 1. Tabla de Historial de Envíos
-- Esta tabla registra cada cambio de estado de un pedido (ej. 'procesando' -> 'enviado')
CREATE TABLE wp_historial_envios (
    envio_id SERIAL PRIMARY KEY,
    order_id BIGINT NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Índice para consultas rápidas por Pedido
CREATE INDEX idx_historial_order ON wp_historial_envios(order_id);

-- 3. Tabla de Garantías Extendidas (Ejemplo del Tutorial)
CREATE TABLE wp_garantias_extendidas (
    id SERIAL PRIMARY KEY,
    order_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    fecha_expiracion TIMESTAMP NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    notas TEXT
);
