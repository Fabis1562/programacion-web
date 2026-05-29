import os
import re

dir_path = r'c:\Users\Christian Fabian\Documents\programacion web'

# 1. Fix CSS
css_file = os.path.join(dir_path, 'styles.css')
with open(css_file, 'r', encoding='utf-8') as f:
    css = f.read()

css_replacements = {
    r'\.how-it-works': '.comoFunciona',
    r'\.steps-grid': '.pasosCuadricula',
    r'\.step-icon': '.iconoPaso',
    r'\.step-blue': '.pasoAzul',
    r'\.step-green': '.pasoVerde',
    r'\.step-orange': '.pasoNaranja',
    r'\.step\b': '.paso',
    r'\.stats-contenedor': '.estadisticasContenedor',
    r'\.stats\b': '.estadisticas',
    r'\.stat-item': '.elementoEstadistica',
    r'\.cta-professional': '.llamadaAccionProfesional',
    r'\.cta-icon': '.iconoLlamadaAccion',
    r'\.footer-grid': '.cuadriculaPiePagina',
    r'\.footer-brand': '.marcaPiePagina',
    r'\.social-icons': '.iconosSociales',
    r'\.footer-links': '.enlacesPiePagina',
    r'\.footer-contact': '.contactoPiePagina',
    r'\.footer-bottom': '.inferiorPiePagina',
    r'\.footer-legal': '.legalPiePagina',
    r'\.footer\b': '.piePagina',
    r'\.modal-contenido': '.modalContenido',
    r'\.close-btn': '.botonCerrar',
    r'\.form-group': '.grupoFormulario'
}

for k, v in css_replacements.items():
    css = re.sub(k, v, css)
with open(css_file, 'w', encoding='utf-8') as f:
    f.write(css)

# 2. Fix HTML
html_file = os.path.join(dir_path, 'app.html')
with open(html_file, 'r', encoding='utf-8') as f:
    html = f.read()

html_replacements = {
    'cuadricula-piePagina': 'cuadriculaPiePagina',
    'marca-piePagina': 'marcaPiePagina',
    'enlaces-piePagina': 'enlacesPiePagina',
    'contacto-piePagina': 'contactoPiePagina',
    'inferior-piePagina': 'inferiorPiePagina',
    'legal-piePagina': 'legalPiePagina',
    'modal  contenido': 'modalContenido',
    'close btn': 'botonCerrar',
    'form group': 'grupoFormulario',
    'boton primario btn block': 'boton botonPrimario botonBloque',
    'btn btn green btn block': 'boton botonVerde botonBloque',
    'data target': 'data-target',
    'profile contenido': 'profileContenido',
    '  bg light': '--fondo-claro',
    '  primary': '--primario',
    '  border': '--borde'
}

for k, v in html_replacements.items():
    html = html.replace(k, v)
with open(html_file, 'w', encoding='utf-8') as f:
    f.write(html)

# 3. Fix JS
js_file = os.path.join(dir_path, 'app.js')
with open(js_file, 'r', encoding='utf-8') as f:
    js = f.read()

js_replacements = {
    'DOM contenidoLoaded': 'DOMContentLoaded',
    'text contenido': 'textContent',
    '.close btn': '.botonCerrar',
    'data target': 'data-target',
    '.login link': '.enlaceInicioSesion',
    '.Actiones de navegacion .btn primary': '.accionesNavegacion .botonPrimario',
    '.cartas de especialistas': '.tarjetasEspecialistas',
    '.cartas de categorias ': '.tarjetasCategorias',
    '.especialistas organizados': '.especialistasOrganizados',
    '.featured specialists': '.seccionEspecialistas',
    '.Seccion de especialistas .link color azul': '.seccionEspecialistas .enlaceColorAzul',
    '. botones de informacion .btn white': '.botonesInformacion .botonOfrecerServicios',
    '. informacion h1': '.informacion h1',
    '. informacion p': '.informacion p',
    '. informacion': '.informacion',
    '.cta professional .btn green': '.llamadaAccionProfesional .botonVerde',
    'var(  text light)': 'var(--texto-claro)',
    'var(  border)': 'var(--borde)',
    'var(  white)': 'var(--blanco)',
    'var(  primary)': 'var(--primario)'
}
for k, v in js_replacements.items():
    js = js.replace(k, v)
with open(js_file, 'w', encoding='utf-8') as f:
    f.write(js)

print("Files updated successfully!")
