<?php
/*
Plugin Name: One of Us Exclusive Events Registration
Description: A custom plugin that will handle exclusive event registration for One of Us Member.
Version: 1.0
Author: Gegejosper Ceniza
*/
define('ROOTDIRMC', plugin_dir_path(__FILE__));
function create_events_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'oou_events';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        event_name VARCHAR(100) NOT NULL,
        event_code VARCHAR(100) NOT NULL,
        event_date DATE NOT NULL,
        event_slot VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_events_table');

function create_event_registrants_table() {
    global $wpdb;
    $oou_event_registrants_tb = $wpdb->prefix . 'oou_event_registrants';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $oou_event_registrants_tb (
        id INT NOT NULL AUTO_INCREMENT,
        event_id INT NOT NULL,
        member_id INT NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_event_registrants_table');

//Add Custom Fields in the User
function add_custom_user_fields($user) {
    ?>
    <h3>Custom User Fields</h3>
    <table class="form-table">
        <tr>
            <th><label for="registration_date">Registration Date</label></th>
            <td>
                <input type="date" name="registration_date" id="registration_date" class="regular-text" value="<?php echo esc_attr(get_user_meta($user->ID, 'registration_date', true)); ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="slots">Slots</label></th>
            <td>
                <input type="number" name="slots" id="slots" class="regular-text" value="<?php echo esc_attr(get_user_meta($user->ID, 'slots', true)); ?>" />
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'add_custom_user_fields');
add_action('edit_user_profile', 'add_custom_user_fields');
add_action('user_new_form', 'add_custom_user_fields');

// Save custom fields during user registration or update in WP admin
function save_custom_user_fields($user_id) {
    if (isset($_POST['registration_date'])) {
        update_user_meta($user_id, 'registration_date', sanitize_text_field($_POST['registration_date']));
    }
    if (isset($_POST['slots'])) {
        update_user_meta($user_id, 'slots', sanitize_text_field($_POST['slots']));
    }
}
add_action('user_register', 'save_custom_user_fields');
add_action('profile_update', 'save_custom_user_fields');


// Schedule an event to run daily
function schedule_slot_update_event() {
    if (!wp_next_scheduled('update_user_slots_event')) {
        wp_schedule_event(time(), 'daily', 'update_user_slots_event');
    }
}
add_action('wp', 'schedule_slot_update_event');
// Hook to handle the scheduled event
function update_user_slots_event() {
    // Enable debugging for testing purposes
    //define('WP_DEBUG', true);

    // Get all users
    $users = get_users();

    foreach ($users as $user) {
        $user_id = $user->ID;

        // Get the registration date and slots for each user
        $registration_date = get_user_meta($user_id, 'registration_date', true);
        $slots = get_user_meta($user_id, 'slots', true);

        // Check if the registration date has expired
        if ($registration_date && strtotime($registration_date) < current_time('timestamp')) {
            // Update slot value to 5 for the next year
            $expiration_date = date('Y-m-d', strtotime('+1 year', strtotime($registration_date)));
            update_user_meta($user_id, 'slots', 5);

            // Optionally, update the registration date to the next year's registration date
            update_user_meta($user_id, 'registration_date', $expiration_date);

            // Log information to the debug log
            error_log("User ID: $user_id - Slots updated to 5. Registration Date: $expiration_date");
        }
    }
}
add_action('update_user_slots_event', 'update_user_slots_event');


add_action('wp_ajax_update_all_user_slots', 'update_all_user_slots_ajax');
function update_all_user_slots_ajax() {
    $user_query = new WP_User_Query(array('fields' => 'ID'));
    $user_ids = $user_query->get_results();

    foreach ($user_ids as $user_id) {
        update_user_slots_event($user_id);
    }

    echo 'All user slots updated successfully.';
    wp_die();
}

// Function to manually update user slots
function manually_update_user_slots() {
    update_user_slots_event();
    // Optionally, redirect to a page after the update
    wp_redirect(home_url('/'));
    exit();
}

// Hook the function to a custom admin page or wherever you want the button
add_action('admin_menu', 'add_custom_admin_menu');
function add_custom_admin_menu() {
    add_menu_page(
        'Update User Slots',
        'Update User Slots',
        'manage_options',
        'update-user-slots',
        'manually_update_user_slots'
    );
}

// function enqueue_bootstrap_js() {
//     // Enqueue Bootstrap JavaScript and Popper.js
//     wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'), '4.3.1', true);
//     wp_enqueue_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array('jquery'), '1.14.7', true);
// }

// add_action('admin_enqueue_scripts', 'enqueue_bootstrap_js');

function enqueue_bootstrap_toast() {
    $current_screen = get_current_screen();
    //echo $current_screen;
    // Check if the current screen is one of your plugin's pages
    //if ($current_screen && $current_screen->id === 'oou') {
    // Enqueue jQuery from the WordPress core
    wp_enqueue_script('jquery');
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');

    // Enqueue Bootstrap JS (popper.js and bootstrap.min.js are required)
    wp_enqueue_script('popper', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js', array('jquery'), null, true);
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('popper'), null, true);
    //}
}

add_action('admin_enqueue_scripts', 'enqueue_bootstrap_toast');

function enqueue_custom_scripts() {
    wp_enqueue_script('custom-events-scripts', plugin_dir_url(__FILE__) . 'scripts.js', array('jquery'), '1.0', true);
}

add_action('admin_enqueue_scripts', 'enqueue_custom_scripts');

function enqueue_toastr() {
    // Enqueue Toastr CSS
    wp_enqueue_style('toastr-css', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css');

    // Enqueue Toastr JS
    wp_enqueue_script('toastr-js', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js', array('jquery'), null, true);

    // Add any additional script initialization if needed
    wp_add_inline_script('toastr-js', 'toastr.options = { "closeButton": false };', 'after');

}
add_action('admin_enqueue_scripts', 'enqueue_toastr');



// Add CSS for styling the plugin page
function enqueue_custom_styles() {
    // Enqueue Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
    wp_enqueue_style('custom-events-css', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0.1');
}

add_action('admin_enqueue_scripts', 'enqueue_custom_styles');

// actions for linking
add_action('wp_ajax_update_event', 'update_event');

require_once(ROOTDIRMC.'functions.php');
require_once(ROOTDIRMC.'events.php');
require_once(ROOTDIRMC.'event-create.php');
require_once(ROOTDIRMC.'event-update.php');
require_once(ROOTDIRMC.'event-delete.php');
require_once(ROOTDIRMC.'event-show.php');
require_once(ROOTDIRMC.'registrant-delete.php');
require_once(ROOTDIRMC.'wp-user.php');
?>