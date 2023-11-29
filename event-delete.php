<?php
// function delete_event() {
//     global $wpdb;
//     $table_name = $wpdb->prefix . 'oou_events';
//     $event_id = $_POST['id'];

//     $result = $wpdb->delete(
//         $table_name,
//         array('id' => $event_id)
//     );

//     if ($result !== false) {
//         wp_send_json_success('Event deleted successfully!');
//     } else {
//         wp_send_json_error('Error deleting event.');
//     }
// }

function delete_event() {
    global $wpdb;
    $events_table_name = $wpdb->prefix . 'oou_events';
    $registrants_table_name = $wpdb->prefix . 'oou_event_registrants';

    $event_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Delete event from the events table
    $event_deleted = $wpdb->delete(
        $events_table_name,
        array('id' => $event_id)
    );

    // Delete associated registrants from the registrants table
    $registrants_deleted = $wpdb->delete(
        $registrants_table_name,
        array('event_id' => $event_id)
    );

    if ($event_deleted !== false && $registrants_deleted !== false) {
        wp_send_json_success('Event and associated registrants deleted successfully!');
    } else {
        wp_send_json_error('Error deleting event and associated registrants.');
    }
}

add_action('wp_ajax_delete_event', 'delete_event');
?>