document.addEventListener('ComponentesCargados', () => {

    const capaOscura = document.getElementById('capa-oscura');
    const modales = document.querySelectorAll('.modal');
    const botonesCerrar = document.querySelectorAll('.boton-cerrar');

    const abrirModal = (idModal) => {
        const modal = document.getElementById(idModal);
        if (modal) {
            capaOscura.classList.add('activa');
            modal.classList.add('activa');
            document.body.style.overflow = 'hidden';
        }
    };

    const cerrarModales = () => {
        if (capaOscura) capaOscura.classList.remove('activa');
        modales.forEach(m => m.classList.remove('activa'));
        document.body.style.overflow = '';
    };

    const btnLoginHeader = document.getElementById('btn-login-header');
    if (btnLoginHeader) btnLoginHeader.addEventListener('click', (e) => { e.preventDefault(); abrirModal('modal-login'); });

    const btnRegistroHeader = document.getElementById('btn-registro-header');
    if (btnRegistroHeader) btnRegistroHeader.addEventListener('click', (e) => { e.preventDefault(); abrirModal('modal-registro'); });

    const btnRegistroCta = document.getElementById('btn-registrate-cta');
    if (btnRegistroCta) btnRegistroCta.addEventListener('click', (e) => { e.preventDefault(); abrirModal('modal-registro'); });

    if (botonesCerrar) {
        botonesCerrar.forEach(btn => btn.addEventListener('click', cerrarModales));
    }
    if (capaOscura) capaOscura.addEventListener('click', cerrarModales);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') cerrarModales();
    });

    const formLogin = document.getElementById('form-login');
    if (formLogin) {
        formLogin.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button');
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Cargando...';
            btn.disabled = true;
            
            try {
                const response = await fetch('../backend/api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await response.json();
                
                if (data.status === 'success') {
                    cerrarModales();
                    formLogin.reset();
                    // Recargar la página o ir al dashboard
                    window.location.href = 'dashboard.html';
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error al conectar con el servidor.');
            } finally {
                btn.textContent = 'Iniciar Sesión';
                btn.disabled = false;
            }
        });
    }

    const formRegistro = document.getElementById('form-registro');
    if (formRegistro) {
        formRegistro.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button');
            const nombre = document.getElementById('reg-nombre').value;
            const email = document.getElementById('reg-email').value;
            const password = document.getElementById('reg-password').value;
            
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Registrando...';
            btn.disabled = true;
            
            try {
                const response = await fetch('../backend/api/registro.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre, email, password })
                });
                const data = await response.json();
                
                if (data.status === 'success' || response.status === 201) {
                    alert('¡Cuenta creada exitosamente! Puedes iniciar sesión ahora.');
                    cerrarModales();
                    formRegistro.reset();
                    abrirModal('modal-login');
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error al conectar con el servidor.');
            } finally {
                btn.textContent = 'Registrarse';
                btn.disabled = false;
            }
        });
    }

    // Lógica para calificar especialista
    let especialistaSeleccionadoId = null;
    let calificacionSeleccionada = 0;

    document.addEventListener('abrirPerfil', (e) => {
        especialistaSeleccionadoId = e.detail.id;
        calificacionSeleccionada = 0;
        // Resetear estrellas y reseña
        const estrellas = document.querySelectorAll('#estrellas-calificacion .estrella');
        estrellas.forEach(star => star.style.color = '#ccc');
        const msg = document.getElementById('mensaje-calificacion');
        if(msg) msg.style.display = 'none';
        const areaResena = document.getElementById('area-resena');
        if(areaResena) areaResena.style.display = 'none';
        const txtResena = document.getElementById('texto-resena');
        if(txtResena) txtResena.value = '';
    });

    const estrellas = document.querySelectorAll('#estrellas-calificacion .estrella');
    estrellas.forEach(estrella => {
        estrella.addEventListener('click', (e) => {
            calificacionSeleccionada = parseInt(e.target.dataset.valor);
            
            // Pintar estrellas
            estrellas.forEach(s => {
                if(parseInt(s.dataset.valor) <= calificacionSeleccionada) s.style.color = '#FFC107';
                else s.style.color = '#ccc';
            });

            const areaResena = document.getElementById('area-resena');
            if(areaResena) areaResena.style.display = 'block';
        });
    });

    const btnEnviarResena = document.getElementById('btn-enviar-resena');
    if (btnEnviarResena) {
        btnEnviarResena.addEventListener('click', async () => {
            if (especialistaSeleccionadoId && calificacionSeleccionada > 0) {
                const comentario = document.getElementById('texto-resena') ? document.getElementById('texto-resena').value : '';
                
                btnEnviarResena.disabled = true;
                btnEnviarResena.textContent = 'Enviando...';
                
                try {
                    await fetch('../backend/api/calificar_especialista.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ especialista_id: especialistaSeleccionadoId, calificacion: calificacionSeleccionada, comentario })
                    });
                    
                    document.getElementById('area-resena').style.display = 'none';
                    const msg = document.getElementById('mensaje-calificacion');
                    if(msg) msg.style.display = 'block';
                } catch(error) {
                    console.error("Error al calificar", error);
                } finally {
                    btnEnviarResena.disabled = false;
                    btnEnviarResena.textContent = 'Enviar Reseña';
                }
            }
        });
    }

    // Lógica para enviar mensaje
    const btnAbrirMensaje = document.getElementById('btn-abrir-mensaje');
    const areaMensaje = document.getElementById('area-mensaje');
    const btnConfirmarMensaje = document.getElementById('btn-confirmar-mensaje');
    const textoMensaje = document.getElementById('texto-mensaje');
    const feedbackMensaje = document.getElementById('feedback-mensaje');

    document.addEventListener('abrirPerfil', () => {
        if(areaMensaje) areaMensaje.style.display = 'none';
        if(btnAbrirMensaje) btnAbrirMensaje.style.display = 'block';
        if(feedbackMensaje) feedbackMensaje.style.display = 'none';
        if(textoMensaje) textoMensaje.value = '';
    });

    if(btnAbrirMensaje) {
        btnAbrirMensaje.addEventListener('click', () => {
            btnAbrirMensaje.style.display = 'none';
            areaMensaje.style.display = 'block';
        });
    }

    if(btnConfirmarMensaje) {
        btnConfirmarMensaje.addEventListener('click', async () => {
            const contenido = textoMensaje.value.trim();
            if(!contenido) return;

            btnConfirmarMensaje.disabled = true;
            btnConfirmarMensaje.textContent = 'Enviando...';

            try {
                const res = await fetch('../backend/api/enviar_mensaje.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ especialista_id: especialistaSeleccionadoId, contenido })
                });
                const data = await res.json();
                
                feedbackMensaje.style.display = 'block';
                if(res.ok || res.status === 201) {
                    feedbackMensaje.style.color = 'green';
                    feedbackMensaje.textContent = '¡Mensaje enviado con éxito!';
                    textoMensaje.value = '';
                    setTimeout(() => {
                        areaMensaje.style.display = 'none';
                        btnAbrirMensaje.style.display = 'block';
                        feedbackMensaje.style.display = 'none';
                    }, 3000);
                } else {
                    feedbackMensaje.style.color = 'red';
                    feedbackMensaje.textContent = data.message || 'Error al enviar.';
                }
            } catch(e) {
                feedbackMensaje.style.display = 'block';
                feedbackMensaje.style.color = 'red';
                feedbackMensaje.textContent = 'Error de conexión.';
            } finally {
                btnConfirmarMensaje.disabled = false;
                btnConfirmarMensaje.textContent = 'Enviar';
            }
        });
    }
});
