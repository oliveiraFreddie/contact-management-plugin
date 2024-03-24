<?php

// Gerenciador de Contactos page
function pone_render_page() {
    ?>
    <div class="wrap">
        <h2>Gerenciador de Contactos</h2>
        <p>Bem-vindo ao Gerenciador de Contactos! Este plugin permite que você gerencie seus contatos de forma fácil e eficiente.</p>
        <p>Você pode adicionar novos contatos e visualizar uma lista de contatos existentes.</p>
    </div>
    <?php
}

// Adicionar Contactos page
function pone_render_add_contact_page() {
    ?>
    <div class="wrap">
        <h2>Adicionar Contato</h2>
        <form method="post" action="">
            <label for="contact_name">Nome:</label><br>
            <input type="text" id="contact_name" name="contact_name" required><br><br>
            <label for="contact_info">Contato:</label><br>
            <input type="text" id="contact_info" name="contact_info" required><br><br>
            <input type="submit" name="submit_contact" value="Adicionar Contato">
        </form>
    </div>
    <?php
}

// Tabela de contactos
function pone_create_contact_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contacts';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        contact_info varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Função para recuperar os contatos do banco de dados
function pone_get_contacts() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contacts';

    $contacts = $wpdb->get_results("SELECT * FROM $table_name");

    return $contacts;
}

// Função para validar número de telefone
function pone_validate_phone_number($phone_number) {
    // Remova todos os caracteres não numéricos do número de telefone
    $cleaned_phone_number = preg_replace('/[^0-9]/', '', $phone_number);
    // Verifique se o número de telefone tem 9 dígitos
    if (strlen($cleaned_phone_number) === 9) {
        return $cleaned_phone_number;
    } else {
        return false;
    }
}

// Função para renderizar a página de lista de contatos
function pone_render_contact_list_page() {
    $contacts = pone_get_contacts();
    ?>
    <div class="wrap">
        <h2>Lista de Contatos</h2>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Contato</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact) : ?>
                    <tr>
                        <td><?php echo $contact->id; ?></td>
                        <td><?php echo $contact->name; ?></td>
                        <td><?php echo $contact->contact_info; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Salvar os contactos
if (isset($_POST['submit_contact'])) {
    $name = sanitize_text_field($_POST['contact_name']);
    $phone_number = sanitize_text_field($_POST['contact_info']);

    // Valide o número de telefone
    $validated_phone_number = pone_validate_phone_number($phone_number);

    if ($validated_phone_number) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contacts';

        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'contact_info' => $validated_phone_number
            )
        );

        // Exiba uma mensagem de sucesso
        echo '<div class="updated"><p>O número de telefone foi adicionado com sucesso!</p></div>';
    } else {
        // Se o número de telefone não for válido, exiba uma mensagem de erro
        echo '<div class="error"><p>O número de telefone inserido não é válido. Por favor, insira um número de telefone válido com 9 dígitos.</p></div>';
    }
}

// Lógica para excluir um contato
if(isset($_POST['delete_contact'])) {
    $contact_id = $_POST['contact_id'];
    pone_delete_contact($contact_id);
    // Exiba uma mensagem de sucesso
    echo '<div class="updated"><p>Contato excluído com sucesso!</p></div>';
}

// Renderiza a página de edição/exclusão de contato
function pone_render_edit_delete_contact_page() {
    // Recuperar os contatos
    $contacts = pone_get_contacts();
    ?>
    <div class="wrap">
        <h2>Editar/Excluir Contato</h2>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Contato</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact) : ?>
                    <tr>
                        <td><?php echo $contact->id; ?></td>
                        <td><?php echo $contact->name; ?></td>
                        <td><?php echo $contact->contact_info; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="contact_id" value="<?php echo $contact->id; ?>">
                                <input type="text" name="new_name" placeholder="Novo Nome">
                                <input type="text" name="new_contact_info" placeholder="Novo Contato">
                                <button type="submit" name="edit_contact">Editar</button>
                                <button type="submit" name="delete_contact">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Adicione um gancho para processar o formulário quando a página for carregada
add_action('admin_init', 'pone_process_edit_delete_contact');

// Editar um contato
function pone_edit_contact($contact_id, $new_name, $new_contact_info) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contacts';

    $wpdb->update(
        $table_name,
        array(
            'name' => $new_name,
            'contact_info' => $new_contact_info
        ),
        array('id' => $contact_id)
    );
}

// Excluir um contato
function pone_delete_contact($contact_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contacts';

    $wpdb->delete(
        $table_name,
        array('id' => $contact_id)
    );
}

// Lógica para editar um contato
if(isset($_POST['edit_contact'])) {
    $contact_id = $_POST['contact_id'];
    $new_name = sanitize_text_field($_POST['new_name']);
    $new_contact_info = sanitize_text_field($_POST['new_contact_info']);

    // Valide o número de telefone
    $validated_phone_number = pone_validate_phone_number($new_contact_info);

    if ($validated_phone_number) {
        pone_edit_contact($contact_id, $new_name, $validated_phone_number);
        // Exiba uma mensagem de sucesso
        echo '<div class="updated"><p>Contato editado com sucesso!</p></div>';
    } else {
        // Se o número de telefone não for válido, exiba uma mensagem de erro
        echo '<div class="error"><p>O número de telefone inserido não é válido. Por favor, insira um número de telefone válido com 9 dígitos.</p></div>';
    }
}
?>
