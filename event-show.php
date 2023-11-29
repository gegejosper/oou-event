<?php 
function view_event() {
    if (isset($_GET['id'])) {
        $event_id = intval($_GET['id']);
        $event = show_event($event_id);
        if ($event) {
?>
    <div class="container">
        <div class="card-body">
            <h5 class="card-title">Event Details <a href="<?php echo admin_url('admin.php?page=oou'); ?>"> <i class="fas fa-reply text-black"></i> back</a></h5>
            <div class="table-responsive">
                <table class="table align-middle table-bordered mb-4">
                    <thead class="fw-bold text-gray-600">
                        <tr>
                            <th>Event Code</th>
                            <th>Event Name</th>
                            <th>Event Date</th>
                            <th>Slots</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bolder"><?php echo $event['event_code']; ?></td>
                            <td class="fw-bolder"><?php echo $event['event_name']; ?></td>
                            <td class="fw-bolder"><?php echo $event['event_date']; ?></td>
                            <td class="fw-bolder"><?php echo $event['event_slot']; ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <h4>Registrants</h4>
                <div id="registrant-message" class="mt-3">
                    <div class="alert alert-success d-flex align-items-center" id="alert-message" role="alert">
                        <div id="action-message">
                        </div>
                    </div>
                </div>
                <table class="table align-middle table-bordered mb-0">
                    <thead class="fw-bold text-gray-600">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        global $wpdb;
                        $registrants_table_name = $wpdb->prefix . 'oou_event_registrants';
                        $users_table_name = $wpdb->prefix . 'users';

                        $registrants = $wpdb->get_results(
                            $wpdb->prepare(
                                "SELECT r.id, r.member_id, u.display_name, u.user_email FROM $registrants_table_name r 
                                INNER JOIN $users_table_name u ON r.member_id = u.ID
                                WHERE r.event_id = %d",
                                $event_id
                            ),
                            ARRAY_A
                        );

                        if ($registrants) {
                            $count = 1;
                            foreach ($registrants as $registrant) {
                                ?>
                                <tr id="row_<?php echo $registrant['id']; ?>">
                                    <td><?php echo $count; ?></td>
                                    <td><?php echo $registrant['display_name']; ?></td>
                                    <td><?php echo $registrant['user_email']; ?></td>
                                    <td>
                                        <a href="mailto:<?php echo $registrant['user_email']; ?>" class="btn btn-icon btn-success"><i class="fas fa-envelope text-black"></i></a>
                                        <button
                                            data-registrant-id="<?php echo $registrant['id']; ?>" 
                                            class="btn btn-icon btn-danger delete-registrant">
                                            <i class="fas fa-trash-alt text-black"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                                $count++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="4">No registrants for this event.</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
<?php
    } else {
        echo '<div class="wrap"><p>Event not found.</p></div>';
    }
    } else {
    echo '<div class="wrap"><p>No event ID provided.</p></div>';
    }
}
?>
