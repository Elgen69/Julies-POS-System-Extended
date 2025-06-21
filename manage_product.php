<?php
// Required to connect to the database
require_once("DBConnection.php");

// Check if a product ID is set via GET request
if(isset($_GET['id'])){
    $product_id = $_GET['id'];
    // Fetch the product details from the database
    $qry = $conn->query("SELECT * FROM `product_list` where product_id = '$product_id'");
    foreach($qry->fetch_array() as $k => $v){
        $$k = $v; // Dynamically set variables for each field in the product record
    }
}
?>

<div class="container-fluid">
    <form action="" id="product-form" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo isset($product_id) ? $product_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="product_code" class="control-label">Code</label>
                        <input type="text" name="product_code" autofocus id="product_code" required class="form-control form-control-sm rounded-0" value="<?php echo isset($product_code) ? $product_code : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="category_id" class="control-label">Category</label>
                        <select name="category_id" id="category_id" class="form-select form-select-sm rounded-0 select2" required>
                            <option <?php echo (!isset($category_id)) ? 'selected' : '' ?> disabled>Please Select Here</option>
                            <?php
                            $cat_qry = $conn->query("SELECT * FROM category_list where `status` = 1 and `delete_flag` = 0  order by `name` asc");
                            while($row= $cat_qry->fetch_assoc()):
                            ?>
                                <option value="<?php echo $row['category_id'] ?>" <?php echo (isset($category_id) && $category_id == $row['category_id'] ) ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Name</label>
                        <input type="text" name="name"  id="name" required class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="price" class="control-label">Price</label>
                        <input type="number" step="any" name="price"  id="price" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($price) ? $price : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="alert_restock" class="control-label">QTY Alert for Restock</label>
                        <input type="number" step="any" name="alert_restock"  id="alert_restock" required class="form-control form-control-sm rounded-0 text-end" value="<?php echo isset($alert_restock) ? $alert_restock : '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="description" class="control-label">Description</label>
                        <textarea name="description" id="description" cols="30" rows="3" class="form-control rounded-0" required><?php echo isset($description) ? $description : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status" class="control-label">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm rounded-0" required>
                            <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="images" class="control-label">Images</label>
                        <input type="file" name="images[]" id="images" class="form-control form-control-sm rounded-0" multiple>
                    </div>
                    <?php if(isset($product_id)): ?>
                    <div class="form-group">
                        <label for="existing_images" class="control-label">Existing Images</label>
                        <div id="existing_images">
                            <?php
                            $images_qry = $conn->query("SELECT image_path FROM product_list WHERE product_id = '{$product_id}'");
                            while($img_row = $images_qry->fetch_assoc()):
                            ?>
                                <div class="img-item">
                                    <img src="<?php echo $img_row['image_path'] ?>" alt="Product Image" class="img-thumbnail" style="width:100px; height:100px;">
                                    <button type="button" class="btn btn-danger btn-sm remove-image" data-id="<?php echo $product_id ?>">Remove</button>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(function(){
    // Handle form submission for product details
    $('#product-form').submit(function(e){
        e.preventDefault(); // Prevent default form submission behavior
        $('.pop_msg').remove(); // Remove any previous pop-up messages
        var _this = $(this);
        var _el = $('<div>').addClass('pop_msg'); // Create a div element for pop-up messages

        $('#uni_modal button').attr('disabled', true); // Disable the form submit button
        $('#uni_modal button[type="submit"]').text('submitting form...'); // Change button text to indicate form submission

        $.ajax({
            url: './Actions.php?a=save_product', // URL to send the request to
            data: new FormData($(this)[0]), // Serialize the form data
            cache: false, // Do not cache the response
            contentType: false, // Do not set the content type header
            processData: false, // Do not process the data
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: err => {
                console.log(err); // Log any errors to the console
                _el.addClass('alert alert-danger').text("An error occurred."); // Display an error message
                _this.prepend(_el); // Add the error message to the form
                _el.show('slow'); // Show the error message
                $('#uni_modal button').attr('disabled', false); // Re-enable the submit button
                $('#uni_modal button[type="submit"]').text('Save'); // Reset the button text
            },
            success: function(resp){
                if(resp.status == 'success'){
                    _el.addClass('alert alert-success'); // Display a success message
                    $('#uni_modal').on('hide.bs.modal', function(){
                        location.reload(); // Reload the page when the modal is hidden
                    });
                    if("<?php echo isset($product_id) ?>" != 1){
                        _this.get(0).reset(); // Reset the form if the product ID is not set
                        $('.select2').val('').trigger('change'); // Reset any select2 elements
                    }
                }else{
                    _el.addClass('alert alert-danger'); // Display an error message
                }
                _el.text(resp.msg); // Set the message text

                _el.hide(); // Hide the message initially
                _this.prepend(_el); // Add the message to the form
                _el.show('slow'); // Show the message
                $('#uni_modal button').attr('disabled', false); // Re-enable the submit button
                $('#uni_modal button[type="submit"]').text('Save'); // Reset the button text
            }
        });
    });

    // Handle image removal for products
    $('.remove-image').click(function(){
        var _this = $(this);
        $.ajax({
            url: './Actions.php?a=delete_image', // URL to send the request to
            method: 'POST',
            data: {id: _this.data('id')}, // Send the image ID to be deleted
            dataType: 'json',
            error: err => {
                console.log(err); // Log any errors to the console
                alert("An error occurred."); // Display an error alert
            },
            success: function(resp){
                if(resp.status == 'success'){
                    _this.closest('.img-item').remove(); // Remove the image item if deletion is successful
                }else{
                    alert(resp.msg); // Display an error message
                }
            }
        });
    });
});
</script>

