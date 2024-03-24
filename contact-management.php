<?php
/*
Plugin Name: Gerenciador de Contactos
Description: Plugin para gerenciar contatos no WordPress.
Version: 1.0.0
Author: Frederico Santos
Author URI: https://frederico-santos.vercel.app/
*/

register_activation_hook(__FILE__, 'pone_create_contact_table');

include_once(plugin_dir_path(__FILE__) . 'functions.php');

add_action('admin_menu', 'pone_register_menus');

function pone_register_menus() {
    add_menu_page(
        'Gerenciador de Contactos',
        'Gerenciador de Contactos',
        'manage_options',
        'pone_hello_page',
        'pone_render_page',
        'dashicons-admin-users'
    );

    add_submenu_page(
        'pone_hello_page', 
        'Lista de Contatos',
        'Lista de Contatos',
        'manage_options',
        'pone_contact_list_page',
        'pone_render_contact_list_page'
    );

    add_submenu_page(
        'pone_hello_page', 
        'Adicionar Contato',
        'Adicionar Contato',
        'manage_options',
        'pone_add_contact_page',
        'pone_render_add_contact_page'
    );
}
?>
