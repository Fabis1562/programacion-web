document.addEventListener('DOMContentLoaded', async () => {

    const loadComponent = async (placeholderId, componentPath) => {
        const placeholder = document.getElementById(placeholderId);
        if (placeholder) {
            try {
                // Agregar parámetro para evitar caché
                const cacheBuster = '?v=' + new Date().getTime();
                const response = await fetch(componentPath + cacheBuster);
                const html = await response.text();
                placeholder.outerHTML = html;
            } catch (error) {
                console.error(`Error cargando ${componentPath}:`, error);
            }
        }
    };

    await loadComponent('placeholder-header', 'components/header.html');
    await loadComponent('placeholder-footer', 'components/footer.html');
    await loadComponent('placeholder-modales', 'components/modales.html');

    try {
        const response = await fetch('../backend/api/verificar_sesion.php');
        const data = await response.json();
        if (data.logged_in) {
            const nav = document.querySelector('.acciones-navegacion');
            if (nav) {
                // Fetch notificaciones
                let badgeHtml = '';
                try {
                    const notifRes = await fetch('../backend/api/obtener_notificaciones.php');
                    const notifData = await notifRes.json();
                    if (notifData.status === 'success' && notifData.no_leidos > 0) {
                        badgeHtml = `<span style="background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; position: absolute; top: -5px; right: -10px;">${notifData.no_leidos}</span>`;
                    }
                } catch(e) {}

                let adminLink = '';
                if (data.usuario && data.usuario.rol === 'admin') {
                    adminLink = `<a href="admin.html" class="boton boton-esquema" style="color: #6366f1; border-color: #6366f1;"><i class="fa-solid fa-shield-halved"></i> Panel Admin</a>`;
                }

                nav.innerHTML = `
                    ${adminLink}
                    <a href="buscar.html?v=8" class="boton boton-esquema">Explorar Especialistas</a>
                    <a href="dashboard.html" class="enlace-inicio-sesion" style="position: relative;">Mi Perfil ${badgeHtml}</a>
                    <a href="#" class="boton boton-primario" id="btn-logout" style="background:#ef4444; border-color:#ef4444;">Salir <i class="fa-solid fa-right-from-bracket"></i></a>
                `;
                
                document.getElementById('btn-logout').addEventListener('click', async (e) => {
                    e.preventDefault();
                    await fetch('../backend/api/logout.php');
                    window.location.href = 'index.html';
                });
            }
        }
    } catch (e) {
        console.error("Error al verificar sesión", e);
    }

    // Configurar menú móvil
    const btnMenuMovil = document.getElementById('btn-menu-movil');
    const menuNavegacion = document.getElementById('menu-navegacion');
    if (btnMenuMovil && menuNavegacion) {
        btnMenuMovil.addEventListener('click', () => {
            menuNavegacion.classList.toggle('activo');
        });
    }

    document.dispatchEvent(new Event('ComponentesCargados'));
});
