<?php
function create_event($event_code, $event_name, $event_date, $event_slot) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'oou_events';
    $wpdb->insert(
        $table_name,
        array(
            'event_code' => $event_code,
            'event_name' => $event_name,
            'event_date' => $event_date,
            'event_slot' => $event_slot,
        )
    );
}
?>