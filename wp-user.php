<?php
// Add custom columns to the Users page
add_filter('manage_users_columns', 'add_custom_user_columns');
function add_custom_user_columns($columns) {
    $columns['registration_date'] = 'Registration Date';
    $columns['slots'] = 'Slots';
    return $columns;
}

// Populate custom columns with data
add_filter('manage_users_custom_column', 'populate_custom_user_columns', 10, 3);
function populate_custom_user_columns($value, $column_name, $user_id) {
    switch ($column_name) {
        case 'registration_date':
            $registration_date = get_user_meta($user_id, 'registration_date', true);
            return $registration_date ? date('Y-m-d', strtotime($registration_date)) : 'N/A';
        case 'slots':
            $slots = get_user_meta($user_id, 'slots', true);
            return is_numeric($slots) ? $slots : 'N/A';
        default:
            return $value;
    }
}

add_action('admin_footer-users.php', 'add_custom_button_to_users_page');
function add_custom_button_to_users_page() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            // Add your button HTML and functionality here
            var buttonHTML = '<button type="button" id="update_slots" class="button button-primary">Update Slots</button>';
            $('.tablenav.bottom').append(buttonHTML);

            // Handle button click event
            $('#update_slots').on('click', function() {
                var confirmed = confirm('This will update slots for all users. Are you sure?');
                    if (confirmed) {
                        // Make an AJAX request to update slots for all users
                        $.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: {
                                action: 'update_all_user_slots',
                            },
                            success: function(response) {
                                alert(response); // You can customize this part to handle the response
                                location.reload();
                            },
                        });
                    }
            });
        });
    </script>
    <?php
}
?>