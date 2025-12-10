-- Script SQL para insertar usuario admin y punto ECA asociado directamente en la base de datos
-- Ejecutar este script en MySQL cuando Lombok no funcione correctamente

-- Primero insertar la localidad si no existe
INSERT IGNORE INTO localidad (localidad_id, nombre, estado, fecha_creacion, fecha_actualizacion)
VALUES ('a1b2c3d4-e5f6-47a9-b12d-cdef01234502', 'Chapinero', 'Activo', NOW(), NOW());

-- Insertar el usuario admin si no existe
INSERT IGNORE INTO usuario (
    usuario_id,
    nombres,
    apellidos,
    email,
    password,
    tipo_usuario,
    tipo_documento,
    numero_documento,
    fecha_nacimiento,
    biografia,
    activo,
    celular,
    ciudad,
    latitud,
    longitud,
    estado,
    fecha_creacion,
    fecha_actualizacion,
    localidad_id
) VALUES (
    'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',  -- usuario_id fijo para admin
    'Admin',                                  -- nombres
    'Sistema',                               -- apellidos
    'admin@inforecicla.com',                 -- email
    '$2a$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password encriptada para "Admin123@"
    'Admin',                                 -- tipo_usuario
    'CC',                                   -- tipo_documento
    '1000000000',                           -- numero_documento
    '1990-01-01',                          -- fecha_nacimiento
    'Usuario administrador del sistema',    -- biografia
    true,                                  -- activo
    '3001234567',                         -- celular
    'Bogotá',                             -- ciudad
    4.7110,                              -- latitud
    -74.0721,                           -- longitud
    'Activo',                          -- estado
    NOW(),                             -- fecha_creacion
    NOW(),                             -- fecha_actualizacion
    'a1b2c3d4-e5f6-47a9-b12d-cdef01234502'  -- localidad_id (Chapinero)
);

-- Insertar el Punto ECA asociado al admin
INSERT IGNORE INTO punto_eca (
    punto_eca_id,
    nombre_punto,
    descripcion,
    gestor_id,
    email_punto,
    celular_punto,
    telefono_punto,
    direccion,
    horario_atencion_punto,
    sitio_web_punto,
    ciudad,
    latitud,
    longitud,
    estado,
    fecha_creacion,
    fecha_actualizacion,
    localidad_id
) VALUES (
    'bbbbbbbb-cccc-dddd-eeee-ffffffffffff',  -- punto_eca_id fijo
    'Punto ECA Demo Admin',                   -- nombre_punto
    'Punto ECA de prueba para el administrador del sistema', -- descripcion
    'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',  -- gestor_id (admin usuario_id)
    'punto.eca.admin@inforecicla.com',       -- email_punto (campo obligatorio)
    '3009876543',                            -- celular_punto (campo obligatorio)
    '6012345678',                            -- telefono_punto
    'Calle 63 #11-50, Chapinero, Bogotá',  -- direccion
    'Lunes a Viernes: 8:00 AM - 6:00 PM',  -- horario_atencion_punto
    'https://inforecicla.com',              -- sitio_web_punto
    'Bogotá',                               -- ciudad
    4.7110,                                 -- latitud
    -74.0721,                              -- longitud
    'Activo',                              -- estado
    NOW(),                                 -- fecha_creacion
    NOW(),                                 -- fecha_actualizacion
    'a1b2c3d4-e5f6-47a9-b12d-cdef01234502'  -- localidad_id (Chapinero)
);

-- Verificar que se creó correctamente
SELECT
    u.usuario_id,
    u.nombres,
    u.apellidos,
    u.email,
    u.tipo_usuario,
    p.punto_eca_id,
    p.nombre_punto,
    p.email_punto,
    p.celular_punto
FROM usuario u
LEFT JOIN punto_eca p ON u.usuario_id = p.gestor_id
WHERE u.email = 'admin@inforecicla.com';
