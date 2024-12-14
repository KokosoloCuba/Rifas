<?php
/*
Plugin Name: Rifas y Ventas de Números
Plugin URI: https://tusitioweb.com
Description: Un plugin para gestionar rifas y ventas de números, incluyendo pagos por Transfermóvil y sorteos.
Version: 1.0
Author: Tu Nombre
Author URI: https://tusitioweb.com
License: GPL2
*/

// Registrar los assets (CSS y JS)
function rifas_enqueue_assets() {
    wp_enqueue_style('rifas-styles', plugin_dir_url(__FILE__) . 'styles.css');
    wp_enqueue_script('rifas-scripts', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'rifas_enqueue_assets');

// Shortcode para mostrar la interfaz de rifas
function rifas_render_interface() {
    ob_start();
    ?>
    <div id="rifas-plugin">
        <!-- Encabezado -->
        <header>
            <h1>Bienvenidos a Nuestra Rifa</h1>
            <p>¡Escoge tu número, participa y gana!</p>
        </header>

        <!-- Selección de Números -->
        <section id="seleccion-numeros">
            <h2>Elige tu número</h2>
            <div class="numeros-grid">
                <!-- Números generados dinámicamente -->
            </div>
        </section>

        <!-- Confirmación de Compra -->
        <section id="confirmacion-compra" class="oculto">
            <h2>Confirmación de Compra</h2>
            <p>Has seleccionado el número <span id="numero-seleccionado"></span>.</p>
            <p>Por favor realiza el pago mediante una de las siguientes opciones para confirmar tu participación:</p>
            <ul>
                <li>Efectivo.</li>
                <li>Transferencia a la cuenta de Transfermóvil: <strong>123456789</strong>.</li>
            </ul>
            <form id="form-compra">
                <label for="email">Ingresa tu correo electrónico:</label>
                <input type="email" id="email" name="email" required>
                <label for="telefono">Ingresa tu número de teléfono:</label>
                <input type="tel" id="telefono" name="telefono" pattern="[0-9]{8}" required>
                <small>Formato: 12345678</small>
                <button type="submit">Confirmar</button>
            </form>
        </section>

        <!-- Sorteo -->
        <section id="sorteo" class="oculto">
            <h2>Sorteo</h2>
            <div id="rueda-sorteo">
                <!-- Aquí se renderiza la rueda de números -->
            </div>
            <button id="iniciar-sorteo">Iniciar Sorteo</button>
            <p id="ganador" class="oculto">¡El número ganador es <span id="numero-ganador"></span>!</p>
        </section>

        <footer>
            <p>&copy; 2024 Rifas y Números. Todos los derechos reservados.</p>
        </footer>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('rifas_plugin', 'rifas_render_interface');

// Añadir página de configuración al menú de administración
function rifas_add_admin_menu() {
    add_menu_page(
        'Configuración Rifas',
        'Rifas Configuración',
        'manage_options',
        'rifas-configuracion',
        'rifas_render_admin_page',
        'dashicons-tickets',
        20
    );
}
add_action('admin_menu', 'rifas_add_admin_menu');

// Renderizar la página de configuración
function rifas_render_admin_page() {
    ?>
    <div class="wrap">
        <h1>Configuración de Rifas</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('rifas_settings_group');
            do_settings_sections('rifas-configuracion');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Registrar configuraciones
function rifas_register_settings() {
    register_setting('rifas_settings_group', 'rifas_transfermovil_account');
    register_setting('rifas_settings_group', 'rifas_cantidad_numeros');

    add_settings_section(
        'rifas_general_section',
        'Configuración General',
        null,
        'rifas-configuracion'
    );

    add_settings_field(
        'rifas_transfermovil_account',
        'Cuenta Transfermóvil',
        'rifas_transfermovil_account_callback',
        'rifas-configuracion',
        'rifas_general_section'
    );

    add_settings_field(
        'rifas_cantidad_numeros',
        'Cantidad de Números',
        'rifas_cantidad_numeros_callback',
        'rifas-configuracion',
        'rifas_general_section'
    );
}
add_action('admin_init', 'rifas_register_settings');

function rifas_transfermovil_account_callback() {
    $value = get_option('rifas_transfermovil_account', '');
    echo '<input type="text" name="rifas_transfermovil_account" value="' . esc_attr($value) . '" placeholder="123456789" />';
}

function rifas_cantidad_numeros_callback() {
    $value = get_option('rifas_cantidad_numeros', '50');
    echo '<input type="number" name="rifas_cantidad_numeros" value="' . esc_attr($value) . '" placeholder="50" min="1" />';
}
?>
