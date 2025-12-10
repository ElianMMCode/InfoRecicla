-- =====================================================
-- MIGRACIÓN DE BASE DE DATOS PARA FULLCALENDAR
-- =====================================================
-- Archivo: db/migration/V001__create_evento_table.sql
--
-- Esta migración crea la tabla de eventos necesaria para
-- almacenar los eventos del calendario FullCalendar
-- =====================================================

-- Crear tabla de eventos
CREATE TABLE IF NOT EXISTS evento (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,

    -- Datos principales del evento
    titulo VARCHAR(255) NOT NULL COMMENT 'Título del evento',
    descripcion LONGTEXT COMMENT 'Descripción detallada del evento',

    -- Fechas y horas
    fecha_inicio DATETIME NOT NULL COMMENT 'Fecha y hora de inicio del evento',
    fecha_fin DATETIME NOT NULL COMMENT 'Fecha y hora de fin del evento',

    -- Estilo y apariencia
    color VARCHAR(7) DEFAULT '#28a745' COMMENT 'Color del evento en formato hexadecimal',

    -- Relaciones
    punto_eca_id BIGINT NOT NULL COMMENT 'ID del Punto ECA propietario del evento',

    -- Auditoría
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',

    -- Índices
    KEY idx_punto_eca (punto_eca_id),
    KEY idx_fecha_inicio (fecha_inicio),
    KEY idx_fecha_fin (fecha_fin),
    KEY idx_fecha_rango (fecha_inicio, fecha_fin),

    -- Restricción de clave foránea
    CONSTRAINT fk_evento_punto_eca FOREIGN KEY (punto_eca_id)
        REFERENCES punto_eca(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de eventos para el calendario FullCalendar';

-- =====================================================
-- DATOS DE PRUEBA (OPCIONAL - COMENTAR SI NO SE NECESITA)
-- =====================================================

-- Insertar eventos de ejemplo
-- Nota: Ajusta el punto_eca_id según tus datos existentes
-- SELECT id FROM punto_eca LIMIT 1; para obtener un ID válido

INSERT INTO evento (titulo, descripcion, fecha_inicio, fecha_fin, color, punto_eca_id)
VALUES
    (
        'Recolección de materiales',
        'Recolección programada de reciclables en el punto ECA',
        DATE_ADD(CURDATE(), INTERVAL 5 DAY) + INTERVAL 10 HOUR,
        DATE_ADD(CURDATE(), INTERVAL 5 DAY) + INTERVAL 12 HOUR,
        '#28a745',
        1
    ),
    (
        'Capacitación ambiental',
        'Taller sobre sostenibilidad y reciclaje para la comunidad',
        DATE_ADD(CURDATE(), INTERVAL 10 DAY) + INTERVAL 14 HOUR,
        DATE_ADD(CURDATE(), INTERVAL 10 DAY) + INTERVAL 16 HOUR,
        '#007bff',
        1
    ),
    (
        'Revisión de inventario',
        'Conteo mensual y verificación del inventario de materiales',
        DATE_ADD(CURDATE(), INTERVAL 15 DAY) + INTERVAL 9 HOUR,
        DATE_ADD(CURDATE(), INTERVAL 15 DAY) + INTERVAL 11 HOUR,
        '#ffc107',
        1
    ),
    (
        'Mantenimiento de instalaciones',
        'Limpieza y mantenimiento general del punto ECA',
        DATE_ADD(CURDATE(), INTERVAL 20 DAY) + INTERVAL 8 HOUR,
        DATE_ADD(CURDATE(), INTERVAL 20 DAY) + INTERVAL 12 HOUR,
        '#dc3545',
        1
    );

-- =====================================================
-- CONSULTAS ÚTILES PARA VERIFICAR LA INSTALACIÓN
-- =====================================================

-- Ver estructura de la tabla
-- DESCRIBE evento;

-- Ver todos los eventos
-- SELECT * FROM evento ORDER BY fecha_inicio DESC;

-- Ver eventos de un punto ECA específico
-- SELECT * FROM evento WHERE punto_eca_id = 1 ORDER BY fecha_inicio DESC;

-- Ver eventos en un rango de fechas
-- SELECT * FROM evento
-- WHERE fecha_inicio >= '2025-12-01' AND fecha_fin <= '2025-12-31'
-- ORDER BY fecha_inicio ASC;

-- Contar eventos por punto ECA
-- SELECT punto_eca_id, COUNT(*) as total_eventos
-- FROM evento
-- GROUP BY punto_eca_id;

