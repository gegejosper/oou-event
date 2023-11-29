<?php 
function events_page() {
    if (isset($_POST['submit'])) {
        // Handle form submissions for CRUD operations
        if ($_POST['action'] != 'delete') {
            $event_code = sanitize_text_field($_POST['event_code']);
            $event_name = sanitize_text_field($_POST['event_name']);
            $event_date = sanitize_text_field($_POST['event_date']);
            $event_slot = sanitize_text_field($_POST['event_slot']);
        }
        if ($_POST['action'] === 'add') {
            create_event($event_code, $event_name, $event_date, $event_slot);
        } elseif ($_POST['action'] === 'edit') {
            $event_id = (int) $_POST['event_id'];
            update_event($event_code, $event_name, $event_date, $event_slot);
        } elseif ($_POST['action'] === 'delete') {
            $event_id = (int) $_POST['event_id'];
            delete_event($event_id);
        }
    }

    // Display your HTML and CSS for the plugin page here
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Manage Events</h1>

        <!-- Form for adding/editing events -->
        <div class="container">
        <form method="post" action="">
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="event_code">Event Code:</label>
                        <input type="text" name="event_code" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="event_name">Event Name:</label>
                        <input type="text" name="event_name" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="event_date">Event Date:</label>
                        <input type="date" name="event_date" class="form-control" required>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="event_slot">Slot:</label>
                        <input type="text" name="event_slot" class="form-control" required>
                    </div>
                </div>
            </div>

            <input type="hidden" name="action" value="add">
            <button type="submit" name="submit" class="button button-primary">Add Event</button>
        </form>
        </div>
        
        
        <div id="event-message" class="mt-3">
            <div class="alert alert-success d-flex align-items-center" id="alert-message" role="alert">
                <div id="action-message">
                </div>
            </div>
        </div>

        <!-- Table for displaying events -->
        <table class="wp-list-table widefat fixed striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Code</th>
                    <th>Event Name</th>
                    <th>Event Date</th>
                    <th>Slots</th>
                    <th>Registrants</th>
                    <th>Shortcode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $events = get_events();
                foreach ($events as $event) {
                    $registrants_count = get_registrants_count($event['id']);
                    ?>
                    <tr id="row_<?php echo $event['id']; ?>">
                        <td><?php echo $event['id']; ?></td>
                        <td id="col_code_<?php echo $event['id']; ?>"><?php echo $event['event_code']; ?></td>
                        <td id="col_name_<?php echo $event['id']; ?>"><?php echo $event['event_name']; ?></td>
                        <td id="col_date_<?php echo $event['id']; ?>"><?php echo $event['event_date']; ?></td>
                        <td id="col_slot_<?php echo $event['id']; ?>"><?php echo $event['event_slot']; ?></td>
                        <td><?php echo $registrants_count;?></td>
                        <td>[oou_event event_id=<?php echo $event['id']; ?>]</td>
                        <td class="d-flex justify-content-center">
                        <a href="<?php echo admin_url('admin.php?page=view-event&id=' . $event['id']); ?>" class="btn btn-icon btn-success mr-1 view-event">
                            <i class="fas fa-search text-black"></i>
                        </a>
                        <button type="button" id="col_link_<?php echo $event['id']; ?>" data-toggle="modal" data-target="#editEventModal" 
                        data-event_id="<?php echo $event['id']; ?>" 
                        data-event_code="<?php echo $event['event_code']; ?>"
                        data-event_name="<?php echo $event['event_name']; ?>"
                        data-event_date="<?php echo $event['event_date']; ?>"
                        data-event_slot="<?php echo $event['event_slot']; ?>"
                        class="btn btn-icon btn-warning mr-1 edit-event"><i class="fas fa-edit text-white"></i> </button>
                        <button
                            data-event-id="<?php echo $event['id']; ?>" 
                            class="btn btn-icon btn-danger delete-event">
                            <i class="fas fa-trash-alt text-black"></i>
                        </button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        
        <div class="d-flex justify-content-center mt-3">
            <button type="button" id="update_slots" class="button button-primary">Update Slots</button>
        </div>

        <!-- Edit Event Modal -->
        <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editEventForm">
                        <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                            <label for="event_location">Event Code:</label>
                            <input type="text" name="edit_event_code" id="edit_event_code" class="form-control" required>
                        </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="event_name">Event Name:</label>
                                <input type="text" name="edit_event_name" id="edit_event_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="event_date">Event Date:</label>
                                <input type="date" name="edit_event_date" id="edit_event_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="event_location">Slot:</label>
                                <input type="text" name="edit_event_slot" id="edit_event_slot" class="form-control" required>
                            </div>
                        </div>
                    </div>
                        <input type="hidden" id="edit_event_id" name="edit_event_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveChanges()">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php
}
?>
