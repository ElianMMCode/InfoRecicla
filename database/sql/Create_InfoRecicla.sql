-- jorge sp 
-- =========================================================
-- InfoRecicla - Esquema completo con IDs NO secuenciales
-- Regenerado a partir de Create_InfoRecicla.sql + ajustes
-- =========================================================
-- 0) Base de datos
CREATE DATABASE IF NOT EXISTS InfoRecicla 
DEFAULT CHARACTER SET utf8mb4
DEFAULT COLLATE utf8mb4_unicode_ci;

USE InfoRecicla;

-- =========================================================
-- 1) Catálogos y referencia
-- =========================================================
CREATE TABLE
  tipos_material (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    nombre VARCHAR(80) NOT NULL,
    descripcion VARCHAR(300) NULL,
    UNIQUE KEY uq_tipo_mat_nombre (nombre)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
  categorias_material (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    nombre VARCHAR(80) NOT NULL,
    descripcion VARCHAR(300) NULL,
    UNIQUE KEY uq_cat_mat_nombre (nombre)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
  categorias_publicaciones (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    nombre VARCHAR(80) NOT NULL,
    descripcion VARCHAR(300) NULL,
    UNIQUE KEY uq_cat_pub_nombre (nombre)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
  etiquetas (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    nombre VARCHAR(80) NOT NULL,
    descripcion VARCHAR(300) NULL,
    UNIQUE KEY uq_tag_nombre (nombre)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =========================================================
-- 2) Núcleo de usuarios / perfiles / puntos ECA
-- =========================================================
CREATE TABLE
  usuarios (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    correo VARCHAR(160) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,  
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    rol ENUM ('Ciudadano', 'GestorECA', 'Administrador') NOT NULL,
    tipo_documento VARCHAR(30) NULL,
    numero_documento VARCHAR(30) NULL,
    telefono VARCHAR(20) NULL,
    recibe_notificaciones BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_nacimiento DATE NULL,
    direccion VARCHAR(200) NULL,
    avatar_url VARCHAR(300) NULL,
    nombre_usuario VARCHAR(60) NULL,
    genero ENUM (
      'masculino',
      'femenino',
      'otro'
    ) NULL,
    localidad VARCHAR(60) NULL,
    estado ENUM ('activo', 'inactivo', 'bloqueado') NOT NULL DEFAULT 'activo',
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuarios_email (correo),
    UNIQUE KEY uq_usuarios_username (nombre_usuario),
    KEY idx_usuarios_doc (tipo_documento, numero_documento)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Perfil de ciudadano 

CREATE TABLE
  puntos_eca (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    gestor_id CHAR(36) NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion VARCHAR(500) NULL,
    direccion VARCHAR(200) NULL,
    ciudad VARCHAR(60) NULL,
    localidad VARCHAR(60) NULL,
    latitud DECIMAL(10, 6) NULL,
    longitud DECIMAL(10, 6) NULL,
    nit VARCHAR(20) NULL,
    horario_atencion VARCHAR(150) NULL,
    sitio_web VARCHAR(200) NULL,
    logo_url VARCHAR(300) NULL,
    foto_url VARCHAR(300) NULL,
    mostrar_mapa BOOLEAN NOT NULL DEFAULT TRUE,
    estado ENUM ('activo', 'inactivo', 'bloqueado') NOT NULL DEFAULT 'activo',
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_eca_owner_usuario FOREIGN KEY (gestor_id) REFERENCES usuarios (id),
    KEY idx_eca_localizacion (ciudad, localidad)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Guardados (bookmarks)
CREATE TABLE
  guardados (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    usuario_id CHAR(36) NOT NULL,
    tipo ENUM ('publicacion', 'punto_eca') NOT NULL,
    referencia_id CHAR(36) NOT NULL,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_guardados_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id),
    KEY idx_guardados_usuario (usuario_id, tipo),
    KEY idx_guardados_ref (tipo, referencia_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =========================================================
-- 3) Publicaciones 
-- =========================================================
CREATE TABLE
  publicaciones (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    usuario_id CHAR(36) NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    contenido TEXT NOT NULL,
    estado ENUM ('borrador', 'publicado', 'archivado') NOT NULL DEFAULT 'publicado',
    categoria_id CHAR(36) NULL,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pub_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id),
    CONSTRAINT fk_pub_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_publicaciones (id),
    KEY idx_pub_usuario (usuario_id, estado),
    FULLTEXT KEY ftx_pub_titulo_contenido (titulo, contenido)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
  publicaciones_etiquetas (
    publicacion_id CHAR(36) NOT NULL,
    etiqueta_id CHAR(36) NOT NULL,
    PRIMARY KEY (publicacion_id, etiqueta_id),
    CONSTRAINT fk_pe_publicacion FOREIGN KEY (publicacion_id) REFERENCES publicaciones (id),
    CONSTRAINT fk_pe_etiqueta FOREIGN KEY (etiqueta_id) REFERENCES etiquetas (id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
  publicaciones_multimedia (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    publicacion_id CHAR(36) NOT NULL,
    tipo ENUM ('imagen', 'video', 'documento', 'enlace') NOT NULL,
    url VARCHAR(400) NOT NULL,
    titulo VARCHAR(150) NULL,
    descripcion VARCHAR(500) NULL,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pub_multi_pub FOREIGN KEY (publicacion_id) REFERENCES publicaciones (id),
    KEY idx_pub_multi_pub (publicacion_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
  votos (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    tipo ENUM ('publicacion', 'punto_eca') NOT NULL,
    referencia_id CHAR(36) NOT NULL,
    usuario_id CHAR(36) NOT NULL,
    valor ENUM ('like', 'dislike') NOT NULL,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_votos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id),
    UNIQUE KEY uq_voto_unico (tipo, referencia_id, usuario_id),
    KEY idx_voto_ref (tipo, referencia_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
  comentarios (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    tipo ENUM ('publicacion', 'punto_eca') NOT NULL,
    referencia_id CHAR(36) NOT NULL,
    usuario_id CHAR(36) NOT NULL,
    texto TEXT NOT NULL,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_coment_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id),
    KEY idx_coment_ref (tipo, referencia_id, creado)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =========================================================
-- 4) Materiales / Inventario / Movimientos
-- =========================================================
CREATE TABLE
  materiales (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    nombre VARCHAR(120) NOT NULL,
    descripcion VARCHAR(400) NULL,
    unidad_medida ENUM ('kg', 'unidad', 'l', 'm3') NOT NULL DEFAULT 'kg',
    tipo_id CHAR(36) NULL,
    categoria_id CHAR(36) NULL,
    imagen_url VARCHAR(300) NULL,
    precio_compra DECIMAL(12, 2) NULL,
    precio_venta DECIMAL(12, 2) NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_mat_tipo FOREIGN KEY (tipo_id) REFERENCES tipos_material (id),
    CONSTRAINT fk_mat_cat FOREIGN KEY (categoria_id) REFERENCES categorias_material (id),
    UNIQUE KEY uq_mat_nombre (nombre)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
  inventario (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    punto_eca_id CHAR(36) NOT NULL,
    material_id CHAR(36) NOT NULL,
    capacidad_max DECIMAL(12, 3) NULL,
    stock_actual DECIMAL(12, 3) NOT NULL DEFAULT 0,
    umbral_alerta DECIMAL(12, 3) NULL,
    umbral_critico DECIMAL(12, 3) NULL,
    unidad_medida ENUM ('kg', 'unidad', 'l', 'm3') NOT NULL DEFAULT 'kg',
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_inv_eca FOREIGN KEY (punto_eca_id) REFERENCES puntos_eca (id),
    CONSTRAINT fk_inv_material FOREIGN KEY (material_id) REFERENCES materiales (id),
    UNIQUE KEY uq_inv_eca_material (punto_eca_id, material_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Movimientos (entradas/salidas) y agenda de despachos
CREATE TABLE compras (
  id              CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  inventario_id   CHAR(36) NOT NULL,
  fecha           DATE NOT NULL,
  kg              DECIMAL(12,3) NOT NULL,
  precio_unit     DECIMAL(12,2) NULL,
  creado          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comp_inv FOREIGN KEY (inventario_id) REFERENCES inventario(id),
  KEY idx_compras_inv_fecha (inventario_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =========================================================
-- 7) Plantas de reciclaje y relación con materiales
-- =========================================================
CREATE TABLE centros_acopio (
  id                  CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  nombre              VARCHAR(150)   NOT NULL,
  tipo                ENUM('Planta','Proveedor','Otro') NOT NULL DEFAULT 'Otro',
  nit                 VARCHAR(20)    NULL,
  alcance             ENUM('global','eca') NOT NULL DEFAULT 'global',
  owner_punto_eca_id  CHAR(36) NULL,
  contacto            VARCHAR(100)  NULL,
  telefono            VARCHAR(20)   NULL,
  email               VARCHAR(120)  NULL,
  sitio_web           VARCHAR(200)  NULL,
  horario_atencion    VARCHAR(150)  NULL,
  direccion           VARCHAR(200)  NULL,
  ciudad              VARCHAR(60)   NULL,
  localidad           VARCHAR(60)   NULL,
  latitud             DECIMAL(10,6) NULL,
  longitud            DECIMAL(10,6) NULL,
  estado              ENUM('activo','inactivo','bloqueado') NOT NULL DEFAULT 'activo',
  notas               VARCHAR(300)  NULL,
  creado              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_cac_owner_eca FOREIGN KEY (owner_punto_eca_id) REFERENCES puntos_eca(id),
  INDEX idx_cac_tipo (tipo),
  INDEX idx_cac_estado (estado),
  INDEX idx_cac_alcance (alcance),
  INDEX idx_cac_ciudad (ciudad),
  INDEX idx_cac_localidad (localidad),
  UNIQUE KEY uq_cac_scoped (nombre, tipo, alcance, owner_punto_eca_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE salidas (
  id                CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  inventario_id     CHAR(36) NOT NULL,
  fecha             DATE NOT NULL,
  kg                DECIMAL(12,3) NOT NULL,
  centro_acopio_id  CHAR(36) NULL,   -- destino externo (centro de acopio)
  creado            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sal_inv  FOREIGN KEY (inventario_id)    REFERENCES inventario(id),
  CONSTRAINT fk_sal_cac  FOREIGN KEY (centro_acopio_id) REFERENCES centros_acopio(id),
  KEY idx_salidas_inv_fecha (inventario_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Relación N:N materiales aceptados por cada centro de acopio
CREATE TABLE materiales_centros_acopio (
  centro_acopio_id CHAR(36) NOT NULL,
  material_id      CHAR(36) NOT NULL,
  PRIMARY KEY (centro_acopio_id, material_id),
  CONSTRAINT fk_mca_cac      FOREIGN KEY (centro_acopio_id) REFERENCES centros_acopio(id),
  CONSTRAINT fk_mca_material FOREIGN KEY (material_id)      REFERENCES materiales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 8) Programación de recolección (adaptado a UUID)
-- =========================================================
CREATE TABLE programacion_recoleccion (
  id                    CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
  punto_eca_id          CHAR(36) NOT NULL,
  material_id           CHAR(36) NOT NULL,
  centro_acopio_id      CHAR(36) NULL, -- sustituye antiguas plantas/proveedores
  fecha                 DATE NOT NULL,
  hora                  TIME NULL,
  frecuencia            ENUM('manual','semanal','quincenal','mensual','unico') NOT NULL DEFAULT 'manual',
  notas                 VARCHAR(300) NULL,
  creado                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_prog_rec_eca   FOREIGN KEY (punto_eca_id)        REFERENCES puntos_eca(id),
  CONSTRAINT fk_prog_rec_mat   FOREIGN KEY (material_id)         REFERENCES materiales(id),
  CONSTRAINT fk_prog_rec_cac   FOREIGN KEY (centro_acopio_id)    REFERENCES centros_acopio(id),
  KEY idx_prog_rec (punto_eca_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  -- =========================================================
-- 6) Conversaciones y mensajes
-- =========================================================
CREATE TABLE
  conversaciones (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    titulo VARCHAR(150) NULL,
    creado_por_id CHAR(36) NOT NULL,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_conv_creador FOREIGN KEY (creado_por_id) REFERENCES usuarios (id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE
  mensajes (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID ()),
    conversacion_id CHAR(36) NOT NULL,
    remitente_id CHAR(36) NOT NULL,
    cuerpo TEXT NOT NULL,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mensajes_conversacion_id FOREIGN KEY (conversacion_id) REFERENCES conversaciones (id),
    CONSTRAINT fk_mensajes_remitente_id FOREIGN KEY (remitente_id) REFERENCES usuarios (id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
