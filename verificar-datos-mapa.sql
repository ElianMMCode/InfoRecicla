-- ===============================================
-- VERIFICAR Y CREAR DATOS DE PRUEBA - PUNTOS ECA
-- ===============================================

-- Verificar si hay puntos ECA activos con coordenadas
SELECT
    p.punto_eca_id,
    p.nombre_punto,
    p.latitud,
    p.longitud,
    p.estado,
    l.nombre as localidad
FROM punto_eca p
LEFT JOIN localidad l ON p.localidad_id = l.localidad_id
WHERE p.estado = 'Activo'
AND p.latitud IS NOT NULL
AND p.longitud IS NOT NULL
LIMIT 10;

-- Si no hay datos, crear algunos de prueba:
-- (Descomentar si es necesario)

/*
-- 1. Verificar que exista al menos una localidad
SELECT * FROM localidad LIMIT 1;

-- Si no hay, crear una localidad de prueba
INSERT INTO localidad (localidad_id, nombre, descripcion, estado, fecha_creacion, usuario_creacion)
VALUES (
    UUID(),
    'Chapinero',
    'Localidad de prueba para ECA',
    'Activo',
    NOW(),
    UUID()
)
ON DUPLICATE KEY UPDATE nombre = 'Chapinero';

-- 2. Crear usuario gestor de prueba
INSERT INTO usuario (usuario_id, tipo_documento, numero_documento, nombre, apellido, email, celular,
                     tipo_usuario, estado, fecha_creacion)
VALUES (
    UUID(),
    'CC',
    '1234567890',
    'Gestor',
    'ECA',
    'gestor@prueba.com',
    '3001234567',
    'GestorECA',
    'Activo',
    NOW()
)
ON DUPLICATE KEY UPDATE nombre = 'Gestor';

-- 3. Obtener IDs para crear punto ECA
SET @usuario_id = (SELECT usuario_id FROM usuario WHERE numero_documento = '1234567890' LIMIT 1);
SET @localidad_id = (SELECT localidad_id FROM localidad WHERE nombre = 'Chapinero' LIMIT 1);

-- 4. Crear punto ECA con coordenadas válidas de Bogotá
INSERT INTO punto_eca (
    punto_eca_id,
    nombre_punto,
    latitud,
    longitud,
    ciudad,
    localidad_id,
    gestor_id,
    direccion,
    celular_punto,
    email_punto,
    descripcion,
    estado,
    fecha_creacion,
    usuario_creacion
) VALUES (
    UUID(),
    'Punto ECA Centro',
    4.7110,
    -74.0721,
    'Bogotá',
    @localidad_id,
    @usuario_id,
    'Carrera 10 #23-45',
    '6012345678',
    'centro@eca.com',
    'Centro de acopio de materiales reciclables',
    'Activo',
    NOW(),
    @usuario_id
);

-- 5. Crear más puntos de prueba en diferentes localidades
-- Punto en Chapinero (norte)
INSERT INTO punto_eca (
    punto_eca_id,
    nombre_punto,
    latitud,
    longitud,
    ciudad,
    localidad_id,
    gestor_id,
    direccion,
    celular_punto,
    email_punto,
    descripcion,
    estado,
    fecha_creacion,
    usuario_creacion
) VALUES (
    UUID(),
    'Punto ECA Chapinero',
    4.7300,
    -74.0500,
    'Bogotá',
    @localidad_id,
    @usuario_id,
    'Calle 82 #10-25',
    '6012345679',
    'chapinero@eca.com',
    'Punto de acopio en Chapinero',
    'Activo',
    NOW(),
    @usuario_id
);

-- Punto en Usaquén
INSERT INTO punto_eca (
    punto_eca_id,
    nombre_punto,
    latitud,
    longitud,
    ciudad,
    localidad_id,
    gestor_id,
    direccion,
    celular_punto,
    email_punto,
    descripcion,
    estado,
    fecha_creacion,
    usuario_creacion
) VALUES (
    UUID(),
    'Punto ECA Usaquén',
    4.7520,
    -74.0350,
    'Bogotá',
    @localidad_id,
    @usuario_id,
    'Carrera 6 #119-30',
    '6012345680',
    'usaquen@eca.com',
    'Centro de acopio en Usaquén',
    'Activo',
    NOW(),
    @usuario_id
);

-- Punto en Suba
INSERT INTO punto_eca (
    punto_eca_id,
    nombre_punto,
    latitud,
    longitud,
    ciudad,
    localidad_id,
    gestor_id,
    direccion,
    celular_punto,
    email_punto,
    descripcion,
    estado,
    fecha_creacion,
    usuario_creacion
) VALUES (
    UUID(),
    'Punto ECA Suba',
    4.8000,
    -74.1000,
    'Bogotá',
    @localidad_id,
    @usuario_id,
    'Calle 147 #78-10',
    '6012345681',
    'suba@eca.com',
    'Punto de reciclaje en Suba',
    'Activo',
    NOW(),
    @usuario_id
);

-- 6. Verificar que se crearon correctamente
SELECT
    p.punto_eca_id,
    p.nombre_punto,
    p.latitud,
    p.longitud,
    p.estado,
    l.nombre as localidad
FROM punto_eca p
LEFT JOIN localidad l ON p.localidad_id = l.localidad_id
WHERE p.estado = 'Activo'
AND p.latitud IS NOT NULL
AND p.longitud IS NOT NULL;
*/

