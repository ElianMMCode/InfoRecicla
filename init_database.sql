-- Script de inicialización de la base de datos para Inforecicla

-- Insertar datos de ejemplo para probar la aplicación
INSERT IGNORE INTO usuario (usuario_id, nombres, apellidos, email, celular, ciudad, localidad, tipo_usuario, tipo_documento, numero_documento, fecha_nacimiento, biografia, foto_perfil, password)
VALUES (
    '33333333-3333-3333-3333-333333333333',
    'Juan Carlos',
    'Pérez González',
    'juan.perez@test.com',
    '3001234567',
    'Bogotá',
    'USAQUEN',
    'GestorECA',
    'CC',
    '12345678',
    '1990-01-01',
    'Gestor ECA de prueba',
    '/images/default-avatar.png',
    'password123'
);

INSERT IGNORE INTO punto_eca (punto_eca_id, nombre_punto, descripcion, telefono_punto, direccion, coordenadas, logo_url_punto, foto_url_punto, localidad, celular, email, sitio_web, horario_atencion, estado, ciudad, usuario_id)
VALUES (
    '11111111-1111-1111-1111-111111111111',
    'Punto ECA La Alquería',
    'Punto de recolección de materiales reciclables en La Alquería',
    '6015551234',
    'Calle 123 # 45-67',
    '4.7110,-74.0721',
    '/logos/punto-alqueria.png',
    '/fotos/punto-alqueria.jpg',
    'USAQUEN',
    '3001234567',
    'alqueria@puntoeca.com',
    'www.puntoecaalqueria.com',
    'Lunes a Viernes: 8:00 AM - 6:00 PM',
    'Activo',
    'Bogotá',
    '33333333-3333-3333-3333-333333333333'
);

INSERT IGNORE INTO material (material_id, nombre, descripcion, categoria)
VALUES
    ('22222222-2222-2222-2222-222222222222', 'Papel Blanco', 'Papel de oficina, hojas blancas', 'Papel'),
    ('22222222-2222-2222-2222-222222222223', 'Cartón', 'Cartón corrugado y cajas', 'Papel'),
    ('22222222-2222-2222-2222-222222222224', 'Plástico PET', 'Botellas plásticas transparentes', 'Plástico'),
    ('22222222-2222-2222-2222-222222222225', 'Aluminio', 'Latas de aluminio', 'Metal');

INSERT IGNORE INTO inventario (inventario_id, capacidad_maxima, unidad_medida, stock_actual, umbral_alerta, umbral_critico, precio_compra, precio_venta, material_id, punto_id)
VALUES
    ('44444444-4444-4444-4444-444444444444', 1000.00, 'KG', 250.50, 70, 85, 500.00, 800.00, '22222222-2222-2222-2222-222222222222', '11111111-1111-1111-1111-111111111111'),
    ('44444444-4444-4444-4444-444444444445', 500.00, 'KG', 125.75, 75, 90, 300.00, 450.00, '22222222-2222-2222-2222-222222222223', '11111111-1111-1111-1111-111111111111'),
    ('44444444-4444-4444-4444-444444444446', 800.00, 'KG', 400.25, 80, 95, 200.00, 350.00, '22222222-2222-2222-2222-222222222224', '11111111-1111-1111-1111-111111111111'),
    ('44444444-4444-4444-4444-444444444447', 300.00, 'KG', 45.00, 60, 80, 800.00, 1200.00, '22222222-2222-2222-2222-222222222225', '11111111-1111-1111-1111-111111111111');
