package org.sena.inforecicla.util;

import lombok.Getter;
import lombok.RequiredArgsConstructor;

@RequiredArgsConstructor
public enum Permission {

    // --- GESTIÓN DE PUBLICACIONES ---
    READ_ALL_PUBLICACION("publicacion:read_all"),
    CREATE_ALL_PUBLICACION("publicacion:create_all"),
    UPDATE_ALL_PUBLICACION("publicacion:update_all"),
    DELETE_ALL_PUBLICACION("publicacion:delete_all"),

    // --- GESTIÓN DE COMENTARIOS ---
    READ_ALL_COMENTARIO("comentario:read_all"),
    CREATE_ALL_COMENTARIO("comentario:create_all"),
    // Admin (Puede borrar/editar cualquier comentario ofensivo)
    UPDATE_ALL_COMENTARIO("comentario:update_all"),
    DELETE_ALL_COMENTARIO("comentario:delete_all"),
    // Usuario/Ciudadano (Solo sus propios comentarios)
    UPDATE_ONE_COMENTARIO("comentario:update_one"),
    DELETE_ONE_COMENTARIO("comentario:delete_one"),

    // --- GESTIÓN DE CATEGORÍAS (Configuración) ---
    READ_ALL_CATEGORIA_PUBLICACION("categoria_publicacion:read_all"),
    CREATE_ALL_CATEGORIA_PUBLICACION("categoria_publicacion:create_all"),
    UPDATE_ALL_CATEGORIA_PUBLICACION("categoria_publicacion:update_all"),
    DELETE_ALL_CATEGORIA_PUBLICACION("categoria_publicacion:delete_all"),

    // --- GESTIÓN DE ETIQUETAS ---
    READ_ALL_ETIQUETA("etiqueta:read_all"),
    CREATE_ALL_ETIQUETA("etiqueta:create_all"),
    UPDATE_ALL_ETIQUETA("etiqueta:update_all"),
    DELETE_ALL_ETIQUETA("etiqueta:delete_all"),

    // --- GESTIÓN DE USUARIOS ---
    READ_ALL_USUARIO("usuario:read_all"),
    CREATE_ALL_USUARIO("usuario:create_all"),
    UPDATE_ALL_USUARIO("usuario:update_all"),
    DELETE_ALL_USUARIO("usuario:delete_all"),

    // --- GESTIÓN DE ROLES Y SEGURIDAD ---
    READ_ALL_ROL("rol:read_all"),
    CREATE_ALL_ROL("rol:create_all"),
    UPDATE_ALL_ROL("rol:update_all"),
    DELETE_ALL_ROL("rol:delete_all"),

    // --- GESTIÓN DE MATERIALES ---
    READ_ALL_MATERIAL("material:read_all"),
    // Admin (Gestión global de tipos de materiales)
    CREATE_ALL_MATERIAL("material:create_all"),
    UPDATE_ALL_MATERIAL("material:update_all"),
    DELETE_ALL_MATERIAL("material:delete_all"),
    // Gestor (Gestión de sus propios materiales en el punto)
    CREATE_ONE_MATERIAL("material:create_one"),
    UPDATE_ONE_MATERIAL("material:update_one"),
    DELETE_ONE_MATERIAL("material:delete_one"),

    // --- GESTIÓN DE PUNTOS ECA ---
    // Admin (Visión y control global)
    READ_ALL_PUNTO_ECA("punto_eca:read_all"),
    CREATE_ALL_PUNTO_ECA("punto_eca:create_all"),
    UPDATE_ALL_PUNTO_ECA("punto_eca:update_all"),
    DELETE_ALL_PUNTO_ECA("punto_eca:delete_all"),
    // Gestor (Control de su propio punto)
    READ_ONE_PUNTO_ECA("punto_eca:read_one"),
    UPDATE_ONE_PUNTO_ECA("punto_eca:update_one"),

    // --- GESTIÓN DE MENSAJERÍA Y CHAT ---
    READ_ALL_CONVERSACION("conversacion:read_all"),
    DELETE_ALL_CONVERSACION("conversacion:delete_all"), // Moderación

    READ_ALL_MENSAJE("mensaje:read_all"),
    CREATE_ALL_MENSAJE("mensaje:create_all"), // Permiso para enviar mensajes
    DELETE_ALL_MENSAJE("mensaje:delete_all"); // Moderación

    @Getter
    private final String permission;
}
