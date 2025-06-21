<?php
// Include the database connection file
require_once("DBConnection.php");

// Execute a query to select the current user's details from the user_list table
$qry = $conn->query("SELECT * FROM `user_list` where user_id = '{$_SESSION['user_id']}'");

// Loop through the fetched array and assign each key-value pair to a variable with the key's name
foreach($qry->fetch_array() as $k => $v){
    $$k = $v;
}
?>
<div class="content py-3">
    <div class="card shadow rounded-0">
        <div class="card-body">
        <h3>Manage Account</h3>
            <hr>
            <div class="col-md-6">
                <form action="" id="user-form">
                    <input type="hidden" name="id" value="<?php echo isset($user_id) ? $user_id : '' ?>">
                    <div class="form-group">
                        <label for="fullname" class="control-label">Full Name</label>
                        <input type="text" name="fullname" id="fullname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($fullname) ? $fullname : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="username" class="control-label">Username</label>
                        <input type="text" name="username" id="username" required class="form-control form-control-sm rounded-0" value="<?php echo isset($username) ? $username : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">New Password</label>
                        <input type="password" name="password" id="password" class="form-control form-control-sm rounded-0" value="">
                    </div>
                    <div class="form-group">
                        <small>Leave the New Password field blank if you don't want update your password.</small>
                    </div>
                    <div class="form-group">
                        <label for="old_password" class="control-label">Old Password</label>
                        <input type="password" name="old_password" id="old_password" class="form-control form-control-sm rounded-0" value="">
                    </div>
                    <div class="form-group d-flex w-100 justify-content-end">
                        <button class="btn btn-sm btn-primary rounded-0 my-1">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // This function runs once the document is fully loaded
    $(function(){
        // Handle form submission for updating user credentials
        $('#user-form').submit(function(e){
            e.preventDefault(); // Prevent the default form submission behavior
            $('.pop_msg').remove(); // Remove any existing pop-up messages

            var _this = $(this); // Reference to the form
            var _el = $('<div>'); // Create a new div element for the pop-up message
            _el.addClass('pop_msg'); // Add a class to the div

            // Disable all buttons in the modal and change the submit button text to indicate form submission
            $('#uni_modal button').attr('disabled',true);
            $('#uni_modal button[type="submit"]').text('submitting form...');

            // Send an AJAX POST request to update the user credentials
            $.ajax({
                url:'./Actions.php?a=update_credentials', // URL for the update action
                method:'POST', // HTTP method
                data:$(this).serialize(), // Serialize the form data
                dataType:'JSON', // Expected response data type
                error:err=>{
                    // Handle any errors that occur during the AJAX request
                    console.log(err);
                    _el.addClass('alert alert-danger'); // Add error classes to the div
                    _el.text("An error occurred."); // Set the error message text
                    _this.prepend(_el); // Prepend the error message to the form
                    _el.show('slow'); // Show the error message
                    // Re-enable the buttons and reset the submit button text
                    $('#uni_modal button').attr('disabled',false);
                    $('#uni_modal button[type="submit"]').text('Save');
                },
                success:function(resp){
                    // Handle the successful response from the server
                    if(resp.status == 'success'){
                        location.reload(); // Reload the page if the update is successful
                    } else {
                        _el.addClass('alert alert-danger'); // Add error classes to the div
                        _el.text(resp.msg); // Set the error message text
                    }
                    _el.hide(); // Hide the message initially
                    _this.prepend(_el); // Prepend the message to the form
                    _el.show('slow'); // Show the message
                    // Re-enable the buttons and reset the submit button text
                    $('#uni_modal button').attr('disabled',false);
                    $('#uni_modal button[type="submit"]').text('Save');
                }
            });
        });
    });
</script>
