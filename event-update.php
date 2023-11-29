<?php
function update_event() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'oou_events';
    $event_id = $_POST['id'];
    $event_name = $_POST['event_name'];
    $event_code = $_POST['event_code'];
    $event_date = $_POST['event_date'];
    $event_slot = $_POST['event_slot'];

    $wpdb->update(
        $table_name,
        array(
            'event_code' => $event_code,
            'event_name' => $event_name,
            'event_date' => $event_date,
            'event_slot' => $event_slot,
        ),
        array('id' => $event_id)
    );
    return($wpdb);
}
?>