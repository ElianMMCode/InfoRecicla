-- =============================
-- SEEDS DE USUARIOS DE PRUEBA
-- =============================

-- 1️⃣ Usuario ADMIN
INSERT INTO usuario (usuario_id,
                     nombres,
                     apellidos,
                     password,
                     tipo_usuario,
                     tipo_documento,
                     numero_documento,
                     fecha_nacimiento,
                     celular,
                     email,
                     ciudad,
                     localidad,
                     foto_perfil,
                     biografia,
                     estado,
                     fecha_creacion,
                     fecha_actualizacion)
VALUES ('11111111-1111-1111-1111-111111111111',
        'Ana',
        'Ramírez',
        'Admin123@',
        'Admin', -- << ENUM EXACTO
        'CC',
        '900123456',
        '1990-04-12',
        '3000000001',
        'ana.admin@example.com',
        'Bogotá',
        'Engativá',
        NULL,
        'Administradora del sistema InfoRecicla.',
        'Activo', -- 👈 Debe existir en tu enum Estado
        NOW(), -- fecha_creacion
        NOW() -- fecha_actualizacion
       );

-- 2️⃣ Usuario CIUDADANO
INSERT INTO usuario (usuario_id,
                     nombres,
                     apellidos,
                     password,
                     tipo_usuario,
                     tipo_documento,
                     numero_documento,
                     fecha_nacimiento,
                     celular,
                     email,
                     ciudad,
                     localidad,
                     foto_perfil,
                     biografia,
                     estado,
                     fecha_creacion,
                     fecha_actualizacion)
VALUES ('22222222-2222-2222-2222-222222222222',
        'Carlos',
        'Gutiérrez',
        'Ciudadano123@',
        'Ciudadano', -- << ENUM EXACTO
        'CC',
        '1002345678',
        '2001-10-05',
        '3000000002',
        'carlos.ciudadano@example.com',
        'Bogotá',
        'Suba',
        NULL,
        'Usuario ciudadano interesado en reciclaje.',
        'Activo', -- 👈 Debe existir en tu enum Estado
        NOW(), -- fecha_creacion
        NOW() -- fecha_actualizacion
       );

-- 3️⃣ Usuario GESTOR ECA
INSERT INTO usuario (usuario_id,
                     nombres,
                     apellidos,
                     password,
                     tipo_usuario,
                     tipo_documento,
                     numero_documento,
                     fecha_nacimiento,
                     celular,
                     email,
                     ciudad,
                     localidad,
                     foto_perfil,
                     biografia,
                     estado,
                     fecha_creacion,
                     fecha_actualizacion)
VALUES ('33333333-3333-3333-3333-333333333333',
        'Luisa',
        'Fernández',
        'Gestor123@',
        'GestorECA', -- << ENUM EXACTO
        'CC',
        '1122334455',
        '1987-09-18',
        '3000000003',
        'luisa.gestor@example.com',
        'Bogotá',
        'Chapinero',
        NULL,
        'Gestora de Estación de Clasificación y Aprovechamiento (ECA).',
        'Activo', -- 👈 Debe existir en tu enum Estado
        NOW(), -- fecha_creacion
        NOW() -- fecha_actualizacion
       );

-- =============================
-- PUNTO ECA ASOCIADO AL GESTOR
-- =============================

-- 4️⃣ Punto ECA para el Gestor Luisa Fernández
INSERT INTO punto_eca (punto_id,
                       nombre_punto,
                       descripcion,
                       telefono_punto,
                       direccion,
                       coordenadas,
                       logo_url_punto,
                       foto_url_punto,
                       estado,
                       gestor_id,
                       -- Campos heredados de EntidadContacto (con @AttributeOverride)
                       celular_punto,
                       email_punto,
                       -- Campos heredados de EntidadLocalizacion
                       ciudad,
                       localidad,
                       latitud,
                       longitud,
                       -- Campos heredados de EntidadLocalizacionWebHorario (con @AttributeOverride)
                       sitio_web_punto,
                       horario_atencion_punto,
                       -- Campos de auditoría
                       fecha_creacion,
                       fecha_actualizacion)
VALUES ('44444444-4444-4444-4444-444444444444',
        'EcoPunto Chapinero',
        'Estación de Clasificación y Aprovechamiento ubicada en el centro de Chapinero, especializada en el manejo integral de residuos sólidos reciclables.',
        '6017891234',
        'Carrera 13 # 63-45, Zona Rosa',
        '4.6486,-74.0648',
        'https://example.com/logos/ecopunto-chapinero-logo.png',
        'https://example.com/fotos/ecopunto-chapinero.jpg',
        'Activo',
        '33333333-3333-3333-3333-333333333333', -- FK al gestor Luisa Fernández
        -- Campos heredados de EntidadContacto (con @AttributeOverride)
        '3001112233',
        'info@ecopunto-chapinero.com',
        -- Campos heredados de EntidadLocalizacion
        'Bogotá',
        'Chapinero',
        4.6486,
        -74.0648,
        -- Campos heredados de EntidadLocalizacionWebHorario (con @AttributeOverride)
        'https://www.ecopunto-chapinero.com',
        'Lunes a Viernes: 7:00 AM - 6:00 PM, Sábados: 8:00 AM - 4:00 PM, Domingos: Cerrado',
        -- Campos de auditoría
        NOW(),
        NOW()
       );
