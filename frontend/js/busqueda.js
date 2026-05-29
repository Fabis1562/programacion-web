document.addEventListener('DOMContentLoaded', () => {
    const contenedorEspecialistas = document.getElementById('contenedor-especialistas');
    const inputBusqueda = document.getElementById('input-busqueda');
    let especialistasData = []; // Guardar los datos para poder filtrarlos

    // 1. Cargar los especialistas desde nuestro Backend en PHP
    async function cargarEspecialistas() {
        try {
            // Nota: Esta ruta asume que tu backend está en la misma dirección base
            // Si hay problemas, podría necesitar ser la ruta absoluta: 'http://localhost/programacion%20web/backend/api/obtener_especialistas.php'
            const response = await fetch('../backend/api/obtener_especialistas.php');
            const result = await response.json();

            if (result.status === 'success') {
                especialistasData = result.data;
                renderizarEspecialistas(especialistasData);
            } else {
                contenedorEspecialistas.innerHTML = `<p style="color:red; text-align:center; grid-column: 1/-1;">Error: ${result.message}</p>`;
            }
        } catch (error) {
            console.error('Error fetching especialistas:', error);
            contenedorEspecialistas.innerHTML = `<p style="color:red; text-align:center; grid-column: 1/-1;">No se pudo conectar con el servidor.</p>`;
        }
    }

    // 2. Dibujar las tarjetas en el HTML
    function renderizarEspecialistas(especialistas) {
        contenedorEspecialistas.innerHTML = ''; // Limpiar el texto de "Cargando..."

        if (especialistas.length === 0) {
            contenedorEspecialistas.innerHTML = '<p style="text-align:center; grid-column: 1/-1; color: var(--color-texto-secundario);">No se encontraron especialistas.</p>';
            return;
        }

        especialistas.forEach(esp => {
            // Imagen por defecto si no tienen una
            const imagen = esp.imagen_url || 'https://images.unsplash.com/photo-1540569014015-19a7be504e3a?auto=format&fit=crop&q=80&w=600';
            const badgeVerificado = esp.verificado ? '<i class="fa-solid fa-circle-check" style="color: #4CAF50; margin-left: 5px;" title="Verificado"></i>' : '';

            const tarjetaHtml = `
                <div class="tarjeta-especialista" style="cursor: pointer;" onclick="abrirPerfilEspecialista('${esp.id}')">
                    <div class="cabecera-tarjeta">
                        <img src="${imagen}" class="imagen-fondo-tarjeta" alt="${esp.nombre}">
                        <div style="position: absolute; top: 10px; right: 10px; background: white; border-radius: 50%; padding: 8px; width: 35px; height: 35px; display: flex; justify-content: center; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <i class="${esp.icono_clase}" style="color: var(--color-primario);"></i>
                        </div>
                    </div>
                    <div class="cuerpo-tarjeta">
                        <h3>${esp.nombre} ${badgeVerificado}</h3>
                        <p class="rol-especialista" style="color: var(--color-primario); font-weight: 500;">${esp.categoria_nombre} <span style="color: var(--color-texto-secundario); font-weight: normal; font-size: 0.9em;">- ${esp.rol_especifico}</span></p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0; font-size: 0.9em; color: var(--color-texto-secundario);">
                            <span><i class="fa-solid fa-star" style="color: #FFC107;"></i> ${esp.rating}</span>
                            <span><i class="fa-solid fa-location-dot"></i> ${esp.ubicacion}</span>
                        </div>
                        
                        <button class="boton boton-primario boton-bloque btn-ver-perfil" onclick="abrirPerfilEspecialista('${esp.id}')">Ver Perfil</button>
                    </div>
                </div>
            `;
            contenedorEspecialistas.innerHTML += tarjetaHtml;
        });
    }

    window.abrirPerfilEspecialista = (idStr) => {
        const id = parseInt(idStr);
        const esp = especialistasData.find(e => parseInt(e.id) === id);
        if (esp) {
            document.getElementById('nombre-perfil-modal').textContent = esp.nombre;
            document.getElementById('rol-perfil-modal').textContent = `${esp.categoria_nombre} - ${esp.rol_especifico}`;
            document.getElementById('img-perfil-modal').src = esp.imagen_url || 'https://images.unsplash.com/photo-1540569014015-19a7be504e3a?auto=format&fit=crop&q=80&w=600';
            document.getElementById('ubicacion-perfil-modal').textContent = esp.ubicacion;
            document.getElementById('rating-perfil-modal').textContent = esp.rating;
            document.getElementById('bio-perfil-modal').textContent = esp.bio || 'Este especialista aún no ha agregado una descripción a su perfil.';
            
            // Reutilizar la función de abrir modal de modales.js si es posible
            const capaOscura = document.getElementById('capa-oscura');
            const modal = document.getElementById('modal-perfil');
            if(capaOscura && modal) {
                capaOscura.classList.add('activa');
                modal.classList.add('activa');
                document.body.style.overflow = 'hidden';
                
                // Disparar evento para que modales.js sepa a quién estamos viendo
                document.dispatchEvent(new CustomEvent('abrirPerfil', { detail: { id: id } }));
            }
        }
    };

    // 3. Lógica de Búsqueda y Filtros
    const selectOrden = document.getElementById('filtro-orden');

    function aplicarFiltros() {
        const termino = inputBusqueda ? inputBusqueda.value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") : '';
        const orden = selectOrden ? selectOrden.value : 'todos';

        let filtrados = especialistasData.filter(esp => {
            const nombre = esp.nombre.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            const rol = (esp.rol_especifico || '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            const categoria = esp.categoria_nombre.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            
            const matchTexto = nombre.includes(termino) || rol.includes(termino) || categoria.includes(termino);
            
            // Si el filtro es "verificados", solo incluir los verificados
            if (orden === 'verificados' && !esp.verificado) return false;
            
            return matchTexto;
        });

        if (orden === 'mejor_calificados') {
            filtrados.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
        }

        renderizarEspecialistas(filtrados);
    }

    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', aplicarFiltros);
    }
    
    if (selectOrden) {
        selectOrden.addEventListener('change', aplicarFiltros);
    }

    // Iniciar la carga al abrir la página
    cargarEspecialistas();
});
