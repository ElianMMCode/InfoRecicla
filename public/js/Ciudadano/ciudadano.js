
// ========= Utilidad: toast =========
const toast = new bootstrap.Toast(document.getElementById('toastOK'), {
    delay: 1800
});
const showToast = (msg) => {
    document.getElementById('toastText').textContent = msg;
    toast.show();
};

// ========= Modal: guardar perfil (simulado) =========
document.getElementById('editarPerfilForm').addEventListener('submit', (e) => {
    e.preventDefault();

    // (A futuro) Validaciones + envío a tu API (PATCH /api/citizens/profile)
    const nombre = document.getElementById('editNombre').value.trim();
    const correo = document.getElementById('editCorreo').value.trim();
    const loc = document.getElementById('editLocalidad').value.trim();
    // const avatarFile = document.getElementById('editAvatar').files[0];

    // Reflejar en encabezado (optimistic UI)
    document.getElementById('userName').textContent = nombre || '—';
    document.getElementById('userEmail').textContent = correo || '—';
    document.getElementById('userLocalidad').textContent = loc ? (loc + ', Bogotá') : '—';

    // Cerrar modal y toast
    bootstrap.Modal.getInstance(document.getElementById('editarPerfilModal')).hide();
    showToast('Perfil actualizado');
});

// ========= Ajustes: guardar preferencias (simulado) =========
document.getElementById('btnGuardarAjustes').addEventListener('click', () => {
    const prefs = {
        receive_notifications: document.getElementById('prefNoti').checked ? 1 : 0,
        display_name: document.getElementById('displayName').value.trim()
    };
    // (A futuro) POST/PATCH /api/citizens/preferences
    console.log('Preferencias:', prefs);
    showToast('Preferencias guardadas');
});

// ========= Carga inicial simulada desde "BD" =========
// Aquí irían tus fetch reales para rellenar todo con datos del usuario
(function seedFromDB() {
    // Ejemplo: marcar preferencia desde perfil
    document.getElementById('prefNoti').checked = true;
    document.getElementById('displayName').value = 'JuanR';
    // TODO: fetch publicaciones/guardados/comentarios y renderizar
})();
