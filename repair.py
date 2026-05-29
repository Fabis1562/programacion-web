import os

dir_path = r'c:\Users\Christian Fabian\Documents\programacion web'

def apply_replacements(filename, replacements):
    filepath = os.path.join(dir_path, filename)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    for old, new in replacements.items():
        content = content.replace(old, new)
        
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

css_rep = {
    '  primario:': '--primario:',
    '  primario-hover:': '--primario-hover:',
    '  secundario:': '--secundario:',
    '  secundario-hover:': '--secundario-hover:',
    '  exito:': '--exito:',
    '  exito-hover:': '--exito-hover:',
    '  texto-oscuro:': '--texto-oscuro:',
    '  texto-claro:': '--texto-claro:',
    '  fondo-claro:': '--fondo-claro:',
    '  borde:': '--borde:',
    '  blanco:': '--blanco:',
    '  fuente-sans:': '--fuente-sans:',
}
apply_replacements('styles.css', css_rep)

ordered_html_rep = {
    '<meta charset="UTF 8">': '<meta charset="UTF-8">',
    '  contenido="width=device width, initial scale=1.0"': ' content="width=device-width, initial-scale=1.0"',
    'font awesome/6.4.0': 'font-awesome/6.4.0',
    'fa solid': 'fa-solid',
    'fa regular': 'fa-regular',
    'fa brands': 'fa-brands',
    'fa search': 'fa-search',
    'fa circle check': 'fa-circle-check',
    'fa location dot': 'fa-location-dot',
    'fa user group': 'fa-user-group',
    'fa shield halved': 'fa-shield-halved',
    'fa facebook f': 'fa-facebook-f',
    'fa linkedin in': 'fa-linkedin-in',
    'fa instagram': 'fa-instagram',
    'fa envelope': 'fa-envelope',
    'fa twitter': 'fa-twitter',
    'fa phone': 'fa-phone',
    'fa star': 'fa-star',
    'fa check': 'fa-check',
    'tyle="background-color:': 'style="background-color:',
    'photo 1540569014015 19a7be504e3a': 'photo-1540569014015-19a7be504e3a',
    'photo 1580489944761 15a19d654956': 'photo-1580489944761-15a19d654956',
    'photo 1504328345606 18bbc8c9d7d1': 'photo-1504328345606-18bbc8c9d7d1',
    'modal  contenido': 'modalContenido',
}
apply_replacements('app.html', ordered_html_rep)

js_rep = {
    'DOM contenidoLoaded': 'DOMContentLoaded',
    'text - contenido': 'textContent',
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
    'var(  primary)': 'var(--primario)',
    r'/[\u0300 \u036f]/g': r'/[\u0300-\u036f]/g',
    """'1 /  1'""": """'1 / -1'""",
}
apply_replacements('app.js', js_rep)

print("Repair completed successfully!")
