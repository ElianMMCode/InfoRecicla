package org.sena.inforecicla.util; // Asegúrate que el paquete coincida

import lombok.Getter;
import lombok.RequiredArgsConstructor;
import org.sena.inforecicla.util.Permission;


import java.util.Arrays;
import java.util.List;

@RequiredArgsConstructor // Crea el constructor automáticamente
public enum Role {

    CIUDADANO(Arrays.asList(
            // Publicaciones (Lectura)
            Permission.READ_ALL_PUBLICACION,
            Permission.READ_ALL_CATEGORIA_PUBLICACION, // Necesario para filtrar
            Permission.READ_ALL_ETIQUETA,

            // Comentarios (Interacción básica)
            Permission.READ_ALL_COMENTARIO,
            Permission.CREATE_ALL_COMENTARIO, // Generalmente un ciudadano puede comentar
            Permission.DELETE_ONE_COMENTARIO, // Borrar su propio comentario
            Permission.UPDATE_ONE_COMENTARIO, // Editar su propio comentario

            // Mensajería
            Permission.CREATE_ALL_MENSAJE, // Poder enviar mensajes
            Permission.READ_ALL_CONVERSACION
    )),

    ADMINISTRADOR(Arrays.asList(
            // --- Permisos Comentarios ---
            Permission.CREATE_ALL_COMENTARIO,
            Permission.READ_ALL_COMENTARIO,
            Permission.DELETE_ALL_COMENTARIO,
            Permission.UPDATE_ALL_COMENTARIO,

            // --- Permisos Publicaciones ---
            Permission.READ_ALL_PUBLICACION,
            Permission.CREATE_ALL_PUBLICACION,
            Permission.DELETE_ALL_PUBLICACION,
            Permission.UPDATE_ALL_PUBLICACION,

            // --- Permisos Categorias ---
            Permission.READ_ALL_CATEGORIA_PUBLICACION,
            Permission.CREATE_ALL_CATEGORIA_PUBLICACION,
            Permission.UPDATE_ALL_CATEGORIA_PUBLICACION,
            Permission.DELETE_ALL_CATEGORIA_PUBLICACION,

            // --- Permisos Usuarios ---
            Permission.READ_ALL_USUARIO,
            Permission.CREATE_ALL_USUARIO,
            Permission.UPDATE_ALL_USUARIO,
            Permission.DELETE_ALL_USUARIO,

            // --- Permisos Roles ---
            Permission.READ_ALL_ROL,
            Permission.CREATE_ALL_ROL,
            Permission.UPDATE_ALL_ROL,
            Permission.DELETE_ALL_ROL,

            // --- Permisos Materiales ---
            Permission.READ_ALL_MATERIAL,
            Permission.CREATE_ALL_MATERIAL,
            Permission.UPDATE_ALL_MATERIAL,
            Permission.DELETE_ALL_MATERIAL,

            // --- Permisos Puntos Eca ---
            Permission.READ_ALL_PUNTO_ECA,
            Permission.CREATE_ALL_PUNTO_ECA,
            Permission.UPDATE_ALL_PUNTO_ECA,
            Permission.DELETE_ALL_PUNTO_ECA, // <--- Agregué la coma que faltaba

            // --- Permisos Etiquetas ---
            Permission.CREATE_ALL_ETIQUETA,
            Permission.READ_ALL_ETIQUETA,
            Permission.UPDATE_ALL_ETIQUETA,
            Permission.DELETE_ALL_ETIQUETA,

            // --- Permisos Mensajes y conversaciones ---
            Permission.READ_ALL_MENSAJE,
            Permission.DELETE_ALL_MENSAJE,
            Permission.READ_ALL_CONVERSACION,
            Permission.DELETE_ALL_CONVERSACION
    )),

    GESTOR_ECA(Arrays.asList(
            // Gestión de su Negocio
            Permission.READ_ALL_MATERIAL,
            Permission.CREATE_ONE_MATERIAL,
            Permission.UPDATE_ONE_MATERIAL,
            Permission.DELETE_ONE_MATERIAL,

            // Puntos Eca
            Permission.READ_ONE_PUNTO_ECA,
            Permission.UPDATE_ONE_PUNTO_ECA,

            // Marketing (Publicaciones y Multimedia) - Recomendado agregarlo
            Permission.READ_ALL_PUBLICACION,
            Permission.CREATE_ALL_PUBLICACION,
            Permission.UPDATE_ALL_PUBLICACION,

            // Interacción
            Permission.READ_ALL_COMENTARIO,
            Permission.CREATE_ALL_COMENTARIO,
            Permission.READ_ALL_CONVERSACION,
            Permission.CREATE_ALL_MENSAJE,

            // Lectura de configuración
            Permission.READ_ALL_CATEGORIA_PUBLICACION,
            Permission.READ_ALL_ETIQUETA,
            Permission.READ_ALL_USUARIO
    ));

    @Getter
    private final List<Permission> permissions;
}
