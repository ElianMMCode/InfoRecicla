-- =====================================================
-- MIGRACIÓN DE BASE DE DATOS - EVENTOS CON REPETICIÓN
-- =====================================================
-- Archivo: db/migration/V002__create_evento_venta_table.sql
--
-- Esta migración crea las tablas para eventos de calendario
-- basados en ventas de material con soporte para repetición automática
-- =====================================================

-- ===== CREAR TABLA DE EVENTOS BASE =====

CREATE TABLE IF NOT EXISTS evento (
    evento_id CHAR(36) PRIMARY KEY NOT NULL COMMENT 'ID único del evento (UUID)',

    -- Relaciones
    venta_inventario_id CHAR(36) NOT NULL COMMENT 'ID de la venta asociada',
    material_id CHAR(36) NOT NULL COMMENT 'ID del material vendido',
    centro_acopio_id CHAR(36) COMMENT 'ID del centro de acopio (opcional)',
    punto_eca_id CHAR(36) NOT NULL COMMENT 'ID del Punto ECA propietario',
    usuario_id CHAR(36) NOT NULL COMMENT 'ID del usuario propietario',

    -- Información básica
    titulo VARCHAR(255) NOT NULL COMMENT 'Título del evento',
    descripcion LONGTEXT COMMENT 'Descripción detallada',

    -- Fechas del evento
    fecha_inicio DATETIME NOT NULL COMMENT 'Fecha/hora de inicio',
    fecha_fin DATETIME NOT NULL COMMENT 'Fecha/hora de fin',

    -- Estilo
    color VARCHAR(7) DEFAULT '#28a745' COMMENT 'Color en formato hex',

    -- Repetición
    tipo_repeticion VARCHAR(15) COMMENT 'SEMANAL, QUINCENAL, MENSUAL, SIN_REPETICION',
    fecha_fin_repeticion DATETIME COMMENT 'Fecha en que termina la repetición',
    es_evento_generado BOOLEAN DEFAULT FALSE COMMENT 'Si es evento base o instancia',

    -- Auditoría
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Claves foráneas
    CONSTRAINT fk_evento_venta FOREIGN KEY (venta_inventario_id)
        REFERENCES venta_inventario(venta_id) ON DELETE CASCADE,
    CONSTRAINT fk_evento_material FOREIGN KEY (material_id)
        REFERENCES material(material_id) ON DELETE RESTRICT,
    CONSTRAINT fk_evento_centro_acopio FOREIGN KEY (centro_acopio_id)
        REFERENCES centro_acopio(centro_acopio_id) ON DELETE SET NULL,
    CONSTRAINT fk_evento_punto_eca FOREIGN KEY (punto_eca_id)
        REFERENCES punto_eca(id) ON DELETE CASCADE,
    CONSTRAINT fk_evento_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuario(usuario_id) ON DELETE CASCADE,

    -- Restricción única: una venta solo puede generar un evento
    UNIQUE KEY uk_evento_venta (venta_inventario_id, punto_eca_id),

    -- Índices para búsquedas
    KEY idx_evento_usuario_punto (usuario_id, punto_eca_id),
    KEY idx_evento_fecha (fecha_inicio, fecha_fin),
    KEY idx_evento_material (material_id),
    KEY idx_evento_centro_acopio (centro_acopio_id),
    KEY idx_evento_repeticion (tipo_repeticion),
    KEY idx_evento_fecha_fin_repeticion (fecha_fin_repeticion)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Configuración base de eventos del calendario (ventas repetidas)';

-- ===== CREAR TABLA DE INSTANCIAS DE EVENTOS =====

CREATE TABLE IF NOT EXISTS evento_instancia (
    instancia_id CHAR(36) PRIMARY KEY NOT NULL COMMENT 'ID único de la instancia (UUID)',

    -- Relación con evento base
    evento_base_id CHAR(36) NOT NULL COMMENT 'ID del evento base que genera esta instancia',

    -- Relaciones para seguridad y filtrado
    punto_eca_id CHAR(36) NOT NULL COMMENT 'Copiado del evento para filtrado rápido',
    usuario_id CHAR(36) NOT NULL COMMENT 'Copiado del evento para seguridad',

    -- Fechas específicas de esta instancia
    fecha_inicio DATETIME NOT NULL COMMENT 'Fecha/hora de inicio de esta ocurrencia',
    fecha_fin DATETIME NOT NULL COMMENT 'Fecha/hora de fin de esta ocurrencia',

    -- Seguimiento
    numero_repeticion INT COMMENT 'Número secuencial de esta repetición (1, 2, 3...)',
    es_completado BOOLEAN DEFAULT FALSE COMMENT 'Si la instancia fue completada',
    completado_en DATETIME COMMENT 'Cuándo se marcó como completada',
    observaciones TEXT COMMENT 'Observaciones del usuario sobre esta instancia',

    -- Auditoría
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,

    -- Claves foráneas
    CONSTRAINT fk_instancia_evento FOREIGN KEY (evento_base_id)
        REFERENCES evento(evento_id) ON DELETE CASCADE,
    CONSTRAINT fk_instancia_punto_eca FOREIGN KEY (punto_eca_id)
        REFERENCES punto_eca(id) ON DELETE CASCADE,
    CONSTRAINT fk_instancia_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuario(usuario_id) ON DELETE CASCADE,

    -- Índices para búsquedas (CRÍTICOS para rendimiento)
    KEY idx_instancia_evento (evento_base_id),
    KEY idx_instancia_fecha (fecha_inicio, fecha_fin),
    KEY idx_instancia_usuario_punto (usuario_id, punto_eca_id),
    KEY idx_instancia_completado (es_completado),
    KEY idx_instancia_fecha_completado (fecha_inicio, es_completado),
    KEY idx_instancia_usuario_pendientes (usuario_id, es_completado, fecha_inicio)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Instancias específicas de eventos que se repiten';

-- ===== CREAR TABLA DE TIPOS DE REPETICIÓN (Referencia) =====

CREATE TABLE IF NOT EXISTS tipo_repeticion_ref (
    tipo_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(15) UNIQUE NOT NULL,
    descripcion VARCHAR(255),
    dias_intervalo INT NOT NULL,

    KEY idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de referencia para tipos de repetición';

-- Insertar tipos de repetición
INSERT INTO tipo_repeticion_ref (nombre, descripcion, dias_intervalo) VALUES
('SIN_REPETICION', 'Evento único sin repetición', 0),
('SEMANAL', 'Se repite cada 7 días', 7),
('QUINCENAL', 'Se repite cada 14 días', 14),
('MENSUAL', 'Se repite cada 30 días', 30)
ON DUPLICATE KEY UPDATE descripcion=VALUES(descripcion);

-- ===== CONSULTAS ÚTILES =====

-- Ver todos los eventos de un usuario en un punto
-- SELECT * FROM evento
-- WHERE usuario_id = 'uuid-usuario' AND punto_eca_id = 'uuid-punto'
-- ORDER BY fecha_inicio DESC;

-- Ver instancias pendientes de un usuario
-- SELECT * FROM evento_instancia
-- WHERE usuario_id = 'uuid-usuario' AND es_completado = FALSE
-- ORDER BY fecha_inicio ASC;

-- Ver instancias completadas
-- SELECT * FROM evento_instancia
-- WHERE usuario_id = 'uuid-usuario' AND es_completado = TRUE
-- ORDER BY completado_en DESC;

-- Ver eventos con repetición próxima a vencer
-- SELECT * FROM evento
-- WHERE fecha_fin_repeticion IS NOT NULL
-- AND fecha_fin_repeticion BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()
-- ORDER BY fecha_fin_repeticion ASC;

-- Contar instancias por evento
-- SELECT evento_base_id, COUNT(*) as total_instancias
-- FROM evento_instancia
-- GROUP BY evento_base_id
-- ORDER BY total_instancias DESC;

-- ===== VISTA PARA FACILITAR CONSULTAS =====

CREATE OR REPLACE VIEW v_evento_instancia_detalle AS
SELECT
    ei.instancia_id,
    ei.evento_base_id,
    ei.fecha_inicio,
    ei.fecha_fin,
    ei.numero_repeticion,
    ei.es_completado,
    ei.completado_en,
    e.titulo,
    e.descripcion,
    e.color,
    e.tipo_repeticion,
    m.descripcion AS material_nombre,
    ca.nombre_centro_acopio AS centro_acopio_nombre,
    e.usuario_id,
    e.punto_eca_id,
    e.created_at,
    ei.created_at AS creada_en
FROM evento_instancia ei
JOIN evento e ON ei.evento_base_id = e.evento_id
JOIN material m ON e.material_id = m.material_id
LEFT JOIN centro_acopio ca ON e.centro_acopio_id = ca.centro_acopio_id
ORDER BY ei.fecha_inicio DESC;

-- ===== PROCEDIMIENTO PARA LIMPIAR INSTANCIAS ANTIGUAS =====

DELIMITER $$

CREATE PROCEDURE sp_limpiar_instancias_antiguas(
    IN p_usuario_id CHAR(36),
    IN p_dias_antiguedad INT,
    OUT p_registros_eliminados INT
)
BEGIN
    DECLARE v_fecha_limite DATETIME;

    -- Calcular fecha límite
    SET v_fecha_limite = DATE_SUB(NOW(), INTERVAL p_dias_antiguedad DAY);

    -- Eliminar instancias completadas hace más de N días
    DELETE FROM evento_instancia
    WHERE usuario_id = p_usuario_id
    AND es_completado = TRUE
    AND completado_en < v_fecha_limite;

    -- Retornar cantidad eliminada
    SET p_registros_eliminados = ROW_COUNT();
END$$

DELIMITER ;

-- ===== PROCEDIMIENTO PARA GENERAR INSTANCIAS =====

DELIMITER $$

CREATE PROCEDURE sp_generar_instancias(
    IN p_evento_id CHAR(36)
)
BEGIN
    DECLARE v_fecha_actual DATETIME;
    DECLARE v_fecha_fin DATETIME;
    DECLARE v_dias_intervalo INT;
    DECLARE v_numero_repeticion INT;
    DECLARE v_usuario_id CHAR(36);
    DECLARE v_punto_eca_id CHAR(36);
    DECLARE v_duracion_evento INT;

    -- Obtener información del evento
    SELECT fecha_inicio, COALESCE(fecha_fin_repeticion, DATE_ADD(fecha_inicio, INTERVAL 365 DAY)),
           CASE tipo_repeticion
               WHEN 'SEMANAL' THEN 7
               WHEN 'QUINCENAL' THEN 14
               WHEN 'MENSUAL' THEN 30
               ELSE 0
           END,
           usuario_id, punto_eca_id,
           TIMESTAMPDIFF(SECOND, fecha_inicio, fecha_fin)
    INTO v_fecha_actual, v_fecha_fin, v_dias_intervalo, v_usuario_id, v_punto_eca_id, v_duracion_evento
    FROM evento WHERE evento_id = p_evento_id;

    -- Si no hay repetición, crear una única instancia
    IF v_dias_intervalo = 0 THEN
        INSERT INTO evento_instancia (
            instancia_id, evento_base_id, punto_eca_id, usuario_id,
            fecha_inicio, fecha_fin, numero_repeticion
        ) VALUES (
            UUID(), p_evento_id, v_punto_eca_id, v_usuario_id,
            v_fecha_actual, DATE_ADD(v_fecha_actual, INTERVAL v_duracion_evento SECOND), 1
        );
    ELSE
        -- Generar instancias hasta la fecha fin
        SET v_numero_repeticion = 1;
        WHILE v_fecha_actual < v_fecha_fin DO
            INSERT INTO evento_instancia (
                instancia_id, evento_base_id, punto_eca_id, usuario_id,
                fecha_inicio, fecha_fin, numero_repeticion
            ) VALUES (
                UUID(), p_evento_id, v_punto_eca_id, v_usuario_id,
                v_fecha_actual, DATE_ADD(v_fecha_actual, INTERVAL v_duracion_evento SECOND),
                v_numero_repeticion
            );

            SET v_fecha_actual = DATE_ADD(v_fecha_actual, INTERVAL v_dias_intervalo DAY);
            SET v_numero_repeticion = v_numero_repeticion + 1;
        END WHILE;
    END IF;
END$$

DELIMITER ;

-- ===== ÍNDICES ADICIONALES PARA PERFORMANCE =====

-- Índice para búsqueda rápida en calendario
CREATE INDEX idx_instancia_busqueda_calendario
ON evento_instancia(usuario_id, punto_eca_id, fecha_inicio, fecha_fin, es_completado);

-- Índice para obtener próximos eventos
CREATE INDEX idx_instancia_proximos
ON evento_instancia(usuario_id, es_completado, fecha_inicio);

-- Índice para estadísticas
CREATE INDEX idx_evento_usuario_repeticion
ON evento(usuario_id, tipo_repeticion, fecha_fin_repeticion);

-- ===== COMENTARIOS Y DOCUMENTACIÓN =====

ALTER TABLE evento COMMENT =
'Configuración base de eventos del calendario
Los eventos se crean desde ventas de material y pueden repetirse
automáticamente de forma semanal, quincenal o mensual.

Relaciones:
- venta_inventario: Venta que origina el evento (1:1)
- material: Material vendido (N:1)
- centro_acopio: Centro de acopio (N:1, opcional)
- punto_eca: Punto ECA propietario (N:1)
- usuario: Usuario propietario (N:1)

Seguridad:
Todos los accesos deben validar usuario_id + punto_eca_id';

ALTER TABLE evento_instancia COMMENT =
'Instancias específicas de eventos repetidos
Cada instancia representa UNA ocurrencia del evento en el calendario.
El calendario muestra instancias, no eventos base.

Relaciones:
- evento_base: Evento configuración (N:1)
- punto_eca: Copiado para seguridad (N:1)
- usuario: Copiado para seguridad (N:1)

Rendimiento:
Los índices están optimizados para búsquedas por usuario+punto+fecha
que es la consulta principal del calendario.';

-- ===== DATOS DE PRUEBA (COMENTADO) =====

/*
-- Ejemplo de inserción de evento de prueba
INSERT INTO evento (evento_id, venta_inventario_id, material_id, punto_eca_id, usuario_id,
                   titulo, descripcion, fecha_inicio, fecha_fin, color, tipo_repeticion, fecha_fin_repeticion)
VALUES (UUID(), 'venta-uuid', 'material-uuid', 'punto-uuid', 'usuario-uuid',
        'Venta Semanal de Plástico', 'Venta recurrente de plástico PET',
        '2025-12-15 10:00:00', '2025-12-15 11:00:00', '#28a745',
        'SEMANAL', '2026-12-15 11:00:00');

-- Generar instancias
CALL sp_generar_instancias((SELECT evento_id FROM evento LIMIT 1));

-- Ver instancias creadas
SELECT * FROM v_evento_instancia_detalle LIMIT 10;
*/

