// custom-events-scripts.js
jQuery(document).ready(function ($) {
    $(document).on('click', '.delete-event', function () {
        const event_id = jQuery(this).data('event-id');
        deleteEvent(event_id);
    });
    $(document).on('click', '.delete-event', function () {
        const event_id = jQuery(this).data('event-id');
        deleteEvent(event_id);
    });
    $('#update_slots').click(function() {
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
    $('#editEventModal').on('show.bs.modal', function (event) {
        // Get the button that triggered the modal
        var button = $(event.relatedTarget);
        //console.log(button);
        // Update the modal input fields with the latest data attributes
        $('#edit_event_code').val(button.data('event_code'));
        $('#edit_event_name').val(button.data('event_name'));
        $('#edit_event_date').val(button.data('event_date'));
        $('#edit_event_slot').val(button.data('event_slot'));
        $('#edit_event_id').val(button.data('event_id'));
    });
    $('#editEventModal').on('hidden.bs.modal', function () {
        // Destroy the modal when it is hidden
        $(this).data('bs.modal', null);
    });
    $(document).on('click', '.edit-event', function() {
        // $('#edit_event_code').val($(this).data('event_code'));
        // $('#edit_event_name').val($(this).data('event_name'));
        // $('#edit_event_date').val($(this).data('event_date'));
        // $('#edit_event_slot').val($(this).data('event_slot'));
        // $('#edit_event_id').val($(this).data('event_id'));
        $('#editEventModal').show();
    });
    // Edit Event Modal
    // $('#editEventModal').on('show.bs.modal', function (event) {
    //     var button = $(event.relatedTarget);
    //     var eventID = button.data('eventid');
        
    // });
    

    // Delete Event Modal
    jQuery('#deleteEventModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var eventID = button.data('eventid');
        // Use eventID to fetch event data via AJAX and populate modal content
        // Example: $.get('ajax.php', { action: 'get_event_data', id: eventID }, function(data) {
        //    $('#deleteEventModal .modal-content').html(data);
        // });
    });
});

function saveChanges() {
    const event_id = jQuery('#edit_event_id').val();
    const event_name = jQuery('#edit_event_name').val();
    const event_code = jQuery('#edit_event_code').val();
    const event_date = jQuery('#edit_event_date').val();
    const event_slot = jQuery('#edit_event_slot').val();
    // Perform AJAX request to update the event
    jQuery.post(ajaxurl, {
        action: 'update_event',
        id: event_id,
        event_name: event_name,
        event_code: event_code,
        event_date: event_date,
        event_slot: event_slot,
    }, function (data) {
        //console.log(data);
        // Handle the response (you can update the UI or perform additional actions)
        jQuery('#editEventModal').modal('hide');
        jQuery('#alert-message').removeClass('alert-warning');
        jQuery('#alert-message').addClass('alert-success');
        jQuery('#action-message').text('Event updated...');
        jQuery('#event-message').show();
        jQuery('#col_code_' + event_id).text(event_code);
        jQuery('#col_name_' + event_id).text(event_name);
        jQuery('#col_date_' + event_id).text(event_date);
        jQuery('#col_slot_' + event_id).text(event_slot);
        jQuery('#col_link_' + event_id)
            .attr('data-event_code', event_code)
            .attr('data-event_date', event_date)
            .attr('data-event_name', event_name)
            .attr('data-event_slot', event_slot);
        //var toastEdit = document.getElementById('toastEdit');
        //toastr.success("Event updated...");
        //var toast = new bootstrap.Toast(toastEdit)

        //toast.show()
    });
}
function deleteEvent(event_id) {
    // Perform AJAX request to delete the event
    jQuery.post(ajaxurl, {
        action: 'delete_event',
        id: event_id,
    }, function (response) {
       //Remove the row with the specified ID
        jQuery('#row_' + event_id).remove();
        jQuery('#alert-message').removeClass('alert-success');
        jQuery('#alert-message').addClass('alert-warning');
        jQuery('#action-message').text('Event deleted...');
        jQuery('#event-message').show();
        //console.log(response);

        // Check if the response was successful
        if (response.success) {
            // Handle success (e.g., show a message)
            console.log(response.data); // This will contain your success message
        } else {
            // Handle error
            console.error(response.data); // This will contain your error message
        }
    });
}

function deleteRegistrant(registrant_id) {
    // Perform AJAX request to delete the event
    jQuery.post(ajaxurl, {
        action: 'delete_registrant',
        id: registrant_id,
    }, function (response) {
       //Remove the row with the specified ID
        jQuery('#row_' + registrant_id).remove();
        jQuery('#alert-message').removeClass('alert-success');
        jQuery('#alert-message').addClass('alert-warning');
        jQuery('#action-message').text('Registrant deleted...');
        jQuery('#registrant-message').show();
        //console.log(response);

        // Check if the response was successful
        if (response.success) {
            // Handle success (e.g., show a message)
            console.log(response.data); // This will contain your success message
        } else {
            // Handle error
            console.error(response.data); // This will contain your error message
        }
    });
}