<?php 
function add_events_menu() {
    add_menu_page(
        'OOU Events',
        'OOU Events',
        'manage_options',
        'oou',
        'events_page',
        'dashicons-buddicons-buddypress-logo'
    );
    // add_submenu_page(
    //     'oou', // Parent menu slug
    //     'Members',
    //     'Members',
    //     'manage_options',
    //     'oou-members',
    //     'members_page'
    // );
    add_submenu_page( null,//parent page slug
        'oou', // Parent menu slug
        'Edit Event',//$page_title
        'Edit Event',// $menu_title
        'manage_options',// $capability
        'edit-event',// $menu_slug,
        'edit_event'// $function
    );
    add_submenu_page( null,//parent page slug
        'oou', // Parent menu slug
        'Delete Event',//$page_title
        'Delete Event',// $menu_title
        'manage_options',// $capability
        'delete-event',// $menu_slug,
        'delete_event'// $function
    );
    add_submenu_page( null,//parent page slug
        'Delete Registrant',//$page_title
        'Delete Registrant',// $menu_title
        'manage_options',// $capability
        'delete-registrant',// $menu_slug,
        'delete_registrant'// $function
    );
    add_submenu_page( null,//parent page slug
        'View Event',//$page_title
        'View Event',// $menu_title
        'manage_options',// $capability
        'view-event',// $menu_slug,
        'view_event'// $function
    );
}
add_action('admin_menu', 'add_events_menu');

function get_events() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'oou_events';
    return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
}
function show_event($event_id){
    global $wpdb;
    $table_name = $wpdb->prefix . 'oou_events';
    $event = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $event_id), ARRAY_A);
    return $event;
}
function show_members($event_id){
    global $wpdb;
    $table_name = $wpdb->prefix . 'oou_events';
    $event = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $event_id), ARRAY_A);
    return $event;
}

function get_registrants_count($event_id) {
    global $wpdb;
    $registrants_table_name = $wpdb->prefix . 'oou_event_registrants';
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $registrants_table_name WHERE event_id = %d", $event_id));
    return $count;
}

//Shortcode 


function event_registration_shortcode($atts) {
    // Check if the user is logged in
    if (is_user_logged_in()) {
        // Get the user ID
        $user_id = get_current_user_id();

        // Extract the event_id from the shortcode attributes
        $atts = shortcode_atts(array(
            'event_id' => 0,
        ), $atts);

        $event_id = intval($atts['event_id']);

        // Check if the user is already registered for the event
        global $wpdb;
        $registrants_table_name = $wpdb->prefix . 'oou_event_registrants';
        $events_table_name = $wpdb->prefix . 'oou_events';

        // Get the count of registrants for the event
        $registrants_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $registrants_table_name WHERE event_id = %d AND member_id = %d",
            $event_id,
            $user_id
        ));

        // Get the available slots for the event
        $available_slots = $wpdb->get_var($wpdb->prepare(
            "SELECT event_slot FROM $events_table_name WHERE id = %d",
            $event_id
        ));

        // Check if the user has slots greater than 0
        $user_slots = get_user_meta($user_id, 'slots', true);
        $registration_allowed = $available_slots > 0 && $user_slots > 0;

        // Display the registration form or a message based on registration status
        ob_start();
        ?>
        <form method="post">
            <input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>">
            <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id); ?>">
            <button type="submit" name="register_event" <?php echo $registration_allowed && $registrants_count == 0 ? '' : 'disabled'; ?>>
                <?php
                if ($registrants_count > 0) {
                    // User is already registered
                    echo 'Already Registered';
                } else {
                    // Check for remaining slots
                    if ($available_slots == 0) {
                        echo 'Event Full';
                    } elseif ($user_slots == 0) {
                        echo 'No Slots Remaining';
                    } else {
                        echo 'Register for Event';
                    }
                }
                ?>
            </button>
        </form>
        <?php
        ob_end_flush();

        // Handle the registration when the form is submitted
        if (isset($_POST['register_event']) && $registration_allowed && $registrants_count == 0) {
            // Get the event_id and user_id from the form submission
            $event_id = intval($_POST['event_id']);
            $user_id = intval($_POST['user_id']);

            // Perform the registration (insert into your registration table)
            $wpdb->insert(
                $registrants_table_name,
                array(
                    'event_id' => $event_id,
                    'member_id'  => $user_id,
                ),
                array('%d', '%d')
            );

            // Deduct the user's slot by 1
            $user_slots -= 1;
            update_user_meta($user_id, 'slots', $user_slots);

            // You can add additional logic or feedback messages here
            echo 'Registration successful! Slots remaining: ' . $user_slots;

            // If user slots reach 0, update the registration button to be disabled
            if ($user_slots == 0) {
                echo '<script>document.querySelector(\'[name="register_event"]\').setAttribute(\'disabled\', \'disabled\');</script>';
            }
        } elseif (!$registration_allowed) {
            // Display a message if the event is full or user slots are 0
            echo $user_slots == 0 ? 'You have no slots available.' : 'Event is full. Registration not allowed.';
        } elseif ($registrants_count > 0) {
            // Display a message if the user is already registered
            echo 'You are already registered for this event.';
        }
    } else {
        // Display a message if the user is not logged in
        return 'Please log in to register for the event.';
    }
}

add_shortcode('oou_event', 'event_registration_shortcode');



?>