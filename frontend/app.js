document.addEventListener('DOMContentLoaded', () => {

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
        capaOscura.classList.remove('activa');
        modales.forEach(m => m.classList.remove('activa'));
        document.body.style.overflow = '';
    };

    const btnLoginHeader = document.getElementById('btn-login-header');
    if (btnLoginHeader) {
        btnLoginHeader.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModal('modal-login');
        });
    }

    const btnRegistroHeader = document.getElementById('btn-registro-header');
    if (btnRegistroHeader) {
        btnRegistroHeader.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModal('modal-registro');
        });
    }

    const btnRegistroCta = document.getElementById('btn-registrate-cta');
    if (btnRegistroCta) {
        btnRegistroCta.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModal('modal-registro');
        });
    }

    const btnVerPerfil = document.querySelectorAll('.btn-ver-perfil');
    btnVerPerfil.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const tarjeta = e.target.closest('.tarjeta-especialista');

            const nombre = tarjeta.querySelector('h3').textContent;
            const rol = tarjeta.querySelector('.rol-especialista').textContent;
            const imgUrl = tarjeta.querySelector('.imagen-perfil-flotante').src;
            const ubicacion = tarjeta.querySelector('.ubicacion span').textContent;
            const rating = tarjeta.querySelector('.calificacion span').textContent.split(' ')[0];

            document.getElementById('nombre-perfil-modal').textContent = nombre;
            document.getElementById('rol-perfil-modal').textContent = rol;
            document.getElementById('img-perfil-modal').src = imgUrl;
            document.getElementById('ubicacion-perfil-modal').textContent = ubicacion;
            document.getElementById('rating-perfil-modal').textContent = rating;

            abrirModal('modal-perfil');
        });
    });

    botonesCerrar.forEach(btn => {
        btn.addEventListener('click', cerrarModales);
    });

    capaOscura.addEventListener('click', cerrarModales);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') cerrarModales();
    });

    // Simulated logic removed to avoid conflict with actual backend logic

    const formRegistro = document.getElementById('form-registro');
    if (formRegistro) {
        formRegistro.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Registrando...';
            btn.disabled = true;

            setTimeout(() => {
                alert('¡Cuenta creada exitosamente! Puedes iniciar sesión ahora.');
                cerrarModales();
                formRegistro.reset();
                btn.textContent = 'Registrarse';
                btn.disabled = false;
                abrirModal('modal-login');
            }, 1200);
        });
    }
    const inputBusqueda = document.querySelector('.barra-busqueda input');
    const tarjetasEspecialistas = document.querySelectorAll('.tarjeta-especialista');
    const tarjetasCategorias = document.querySelectorAll('.tarjeta-categoria');
    const enlaceVerTodos = document.querySelector('.enlace-ver-todos');

    const filtrarEspecialistas = (termino) => {
        const terminoNormalizado = termino.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();

        tarjetasEspecialistas.forEach(tarjeta => {
            const contenidoTarjeta = tarjeta.textContent.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            if (termino === '' || contenidoTarjeta.includes(terminoNormalizado)) {
                tarjeta.style.display = 'block';
            } else {
                tarjeta.style.display = 'none';
            }
        });
    };

    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', (e) => {
            filtrarEspecialistas(e.target.value.trim());
        });
    }

    tarjetasCategorias.forEach(tarjeta => {
        tarjeta.addEventListener('click', () => {
            const nombreCategoria = tarjeta.querySelector('h3').textContent.trim();

            tarjetasCategorias.forEach(t => {
                t.style.borderColor = 'rgba(255,255,255,0.5)';
                t.style.boxShadow = '';
            });
            tarjeta.style.borderColor = 'var(--color-primario)';
            tarjeta.style.boxShadow = '0 0 0 4px rgba(79, 70, 229, 0.2)';

            document.getElementById('especialistas').scrollIntoView({ behavior: 'smooth' });

            filtrarEspecialistas(nombreCategoria);
            if (inputBusqueda) inputBusqueda.value = '';
        });
    });

    if (enlaceVerTodos) {
        enlaceVerTodos.addEventListener('click', (e) => {
            e.preventDefault();
            tarjetasCategorias.forEach(t => {
                t.style.borderColor = 'rgba(255,255,255,0.5)';
                t.style.boxShadow = '';
            });
            filtrarEspecialistas('');
        });
    }
});
