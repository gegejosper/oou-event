<?php
function delete_registrant() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'oou_event_registrants';
    $event_id = $_POST['id'];

    $result = $wpdb->delete(
        $table_name,
        array('id' => $event_id)
    );

    if ($result !== false) {
        wp_send_json_success('Registrant deleted successfully!');
    } else {
        wp_send_json_error('Error deleting registrant.');
    }
}

add_action('wp_ajax_delete_registrant', 'delete_registrant');
?>