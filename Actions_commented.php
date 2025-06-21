<?php
/*
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('DBConnection.php');

/**
 * The Actions class encapsulates various functionalities related to user actions.
 * These include login, password reset, user management, and category management.
 */
class Actions extends DBConnection {

    /**
     * Constructor: Initializes database connection.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Destructor: Closes database connection.
     */
    public function __destruct() {
        parent::__destruct();
    }

    /**
     * Method for user login.
     */
    function login(){
        // Extract POST data
        extract($_POST);
        
        // SQL query to fetch user details
        $sql = "SELECT * FROM user_list where username = '{$username}' and `password` = '".md5($password)."' ";
        
        // Execute query and fetch result
        @$qry = $this->db->query($sql)->fetch_array();
        
        // Check if query result is empty (no matching user found)
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            // Set status and message for successful login
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            
            // Store user details in session variables
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                    $_SESSION[$k] = $v;
            }
        }
        
        // Return JSON-encoded response
        return json_encode($resp);
    }

    /**
     * Method for resetting user password.
     */
    function reset_password() {
        // Extract POST data
        extract($_POST);
        
        // Check if new passwords match
        if ($new_password !== $confirm_password) {
            $resp['status'] = 'error';
            $resp['msg'] = 'Passwords do not match.';
            echo json_encode($resp);
            return;
        }
        
        // Hash the new password
        $hashed_password = md5($new_password);
        
        // Prepare and execute SQL update statement
        $update_stmt = $this->db->prepare('UPDATE user_list SET password = ? WHERE email = ?');
        $update_stmt->bind_param('ss', $hashed_password, $email); // 'ss' indicates two string parameters
        $update_stmt->execute();
        
        // Check if password reset was successful
        if ($update_stmt->affected_rows > 0) {
            $resp['status'] = 'success';
            $resp['msg'] = 'Password reset successfully.';
        } else {
            $resp['status'] = 'error';
            $resp['msg'] = 'Failed to reset password.';
        }
        
        // Return JSON-encoded response
        echo json_encode($resp);
    }    

    /**
     * Method for logging out user.
     */
    function logout(){
        // Destroy session and redirect to home page
        session_destroy();
        header("location:./");
    }
    

    /**
     * Method for saving user details.
     */
    function save_user(){
        global $conn;
        extract($_POST);
        $data = "";
        // Loop through POST data to build SQL query
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                if(!empty($id)){
                    if(!empty($data)) $data .= ",";
                    $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        // Generate initial password if creating a new user
        if(empty($id)){
            $initial_password = substr(md5(uniqid()), 0, 8); // Generate an initial password
            $hashed_password = md5($initial_password); // Hash the initial password
            $cols[] = 'password';
            $values[] = "'{$hashed_password}'";
        }
        // Construct SQL query for inserting or updating user details
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        // Check if username already exists
        @$check = $conn->query("SELECT count(user_id) as `count` FROM user_list where `username` = '{$username}' ".($id > 0 ? " and user_id != '{$id}' " : ""))->fetch_array()['count'];
        if(@$check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            // Execute SQL query to save user details
            if(empty($id)){
                $sql = "INSERT INTO `user_list` {$data}";
            }else{
                $sql = "UPDATE `user_list` set {$data} where user_id = '{$id}'";
            }
            @$save = $conn->query($sql);
            // Check if user details were saved successfully
            if($save){
                $resp['status'] = 'success';
                if(empty($id)){
                    $resp['msg'] = 'New User successfully saved.';
                    $resp['initial_password'] = $initial_password; // Send back the initial password
                }else{
                    $resp['msg'] = 'User Details successfully updated.';
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: '.$conn->error;
                $resp['sql'] = $sql;
            }
        }
        // Return JSON-encoded response
        echo json_encode($resp);
    }
    
    /**
     * Method for deleting user.
     */
    function delete_user(){
        extract($_POST);

        // Execute SQL query to delete user
        @$delete = $this->db->query("DELETE FROM `user_list` where user_id = '{$id}'");
        
        // Check if user was deleted successfully
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->db->error;
        }
        
        // Return JSON-encoded response
        return json_encode($resp);
    }
    
    /**
     * Method for updating user credentials.
     */
    function update_credentials(){
        extract($_POST);
        $data = "";
        
        // Loop through POST data to build SQL query for updating credentials
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        
        // Check if old password matches stored password
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            // Execute SQL query to update user credentials
            $sql = "UPDATE `user_list` set {$data} where user_id = '{$_SESSION['user_id']}'";
            @$save = $this->db->query($sql);
            
            // Check if credentials were updated successfully
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                
                // Update session variables with new credentials
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->db->error;
                $resp['sql'] =$sql;
            }
        }
        
        // Return JSON-encoded response
        return json_encode($resp);
    }
    
    /**
     * Method for saving category details.
     */
    function save_category(){
        extract($_POST);
        $data = "";
        
        // Loop through POST data to build SQL query for saving category details
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = addslashes(trim($v));
                if(empty($id)){
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                }else{
                    if(!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        
        // Construct SQL query for inserting or updating category details
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `category_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `category_list` set {$data} where category_id = '{$id}'";
        }
        
        // Check if category already exists
        @$check= $this->db->query("SELECT COUNT(category_id) as count from `category_list` where `name` = '{$name}' ".($id > 0 ? " and category_id != '{$id}'" : ""))->fetch_array()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Category already exists.';
        }else{
            // Execute SQL query to save or update category details
            @$save = $this->db->query($sql);
            
            // Check if category details were saved or updated successfully
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Category successfully saved.";
                else
                    $resp['msg'] = "Category successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Category Failed.";
                else
                    $resp['msg'] = "Updating Category Failed.";
                $resp['error']=$this->db->error;
            }
        }
        
        // Return JSON-encoded response
        return json_encode($resp);
    }
}
function delete_category(){
    extract($_POST); // Extracts variables from POST data

    // Execute SQL query to update delete_flag
    @$update = $this->db->query("UPDATE `category_list` SET `delete_flag` = 1 WHERE category_id = '{$id}'");

    // Check if update was successful
    if($update){
        $resp['status'] = 'success'; // Set success status
        $_SESSION['flashdata']['type'] = 'success'; // Set flash data type
        $_SESSION['flashdata']['msg'] = 'Category successfully deleted.'; // Set success message
    }else{
        $resp['status'] = 'failed'; // Set failed status
        $resp['error'] = $this->db->error; // Get database error if update fails
    }

    // Return JSON-encoded response
    return json_encode($resp);
}

function save_product(){
    extract($_POST); // Extracts variables from POST data
    $data = ""; // Initialize variable for data

    $image_paths = []; // Initialize array for image paths

    // Check if images were uploaded
    if (!empty($_FILES['images']['tmp_name'][0])) {
        $upload_directory = 'bsms/images/1. General images directory/'; // Define upload directory
        if (!is_dir($upload_directory)) {
            mkdir($upload_directory, 0777, true); // Create directory if not exists
        }
        // Loop through uploaded files
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['images']['name'][$key]; // Get file name
            $file_tmp = $_FILES['images']['tmp_name'][$key]; // Get file temporary path
            $file_path = $upload_directory . basename($file_name); // Create file path
            // Move uploaded file to specified directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                $image_paths[] = $file_path; // Store uploaded file path
            }
        }
    }

    // Prepare data for database insertion or update
    foreach($_POST as $k => $v){
        if(!in_array($k, ['id'])) { // Exclude 'id' from data insertion or update
            $v = addslashes(trim($v)); // Trim and escape values for SQL safety
            if (empty($id)) { // If no ID (new product), prepare for insertion
                $cols[] = "`{$k}`"; // Collect column names
                $vals[] = "'{$v}'"; // Collect column values
            } else { // If ID exists, prepare for update
                if (!empty($data)) $data .= ", "; // Append comma if data already exists
                $data .= " `{$k}` = '{$v}' "; // Append key-value pair for update
            }
        }
    }

    // Handle inserting or updating product data
    if(isset($cols) && isset($vals)){
        $cols_join = implode(",", $cols); // Join column names for insertion
        $vals_join = implode(",", $vals); // Join column values for insertion
    }
    if(empty($id)){
        $sql = "INSERT INTO `product_list` ({$cols_join}) VALUES ({$vals_join})"; // SQL for insertion
    }else{
        $sql = "UPDATE `product_list` SET {$data} WHERE product_id = '{$id}'"; // SQL for update
    }

    // Check if product code or name already exists
    $check_product_code = $this->db->query("SELECT COUNT(product_id) AS count FROM `product_list` WHERE `product_code` = '{$product_code}' AND delete_flag = 0 " . ($id > 0 ? " AND product_id != '{$id}'" : ""))->fetch_array()['count'];
    $check_product_name = $this->db->query("SELECT COUNT(product_id) AS count FROM `product_list` WHERE `name` = '{$name}' AND delete_flag = 0 " . ($id > 0 ? " AND product_id != '{$id}'" : ""))->fetch_array()['count'];

    // Handle duplicate product code or name
    if($check_product_code > 0){
        $resp['status'] = 'failed'; // Set failed status
        $resp['msg'] = 'Product Code already exists.'; // Set error message
    } elseif($check_product_name > 0){
        $resp['status'] = 'failed'; // Set failed status
        $resp['msg'] = 'Product Name already exists.'; // Set error message
    } else {
        // Execute SQL query to save or update product details
        $save = $this->db->query($sql);

        // Check if product details were saved or updated successfully
        if($save){
            $resp['status'] = "success"; // Set success status
            if(empty($id)){
                $resp['msg'] = "Product successfully saved."; // Set success message for new product
                $new_id = $this->db->insert_id; // Get newly inserted product ID
            } else {
                $resp['msg'] = "Product successfully updated."; // Set success message for updated product
                $new_id = $id; // Use existing product ID for update
            }

            // Update image paths in database for the product
            foreach ($image_paths as $image_path) {
                $this->db->query("UPDATE `product_list` SET `image_path` = '{$image_path}' WHERE `product_id` = '{$new_id}'");
            }
        } else {
            $resp['status'] = "failed"; // Set failed status
            if(empty($id)){
                $resp['msg'] = "Saving New Product Failed."; // Set error message for new product
            } else {
                $resp['msg'] = "Updating Product Failed."; // Set error message for updated product
            }
            $resp['error'] = $this->db->error; // Get database error if save/update fails
        }
    }

    // Return JSON-encoded response
    return json_encode($resp);
}

function delete_image(){
    extract($_POST); // Extracts variables from POST data
    $resp = array(); // Initialize response array

    // Check if image ID is provided
    if(isset($id)){
        // Query to fetch image path based on product ID
        $qry = $this->db->query("SELECT image_path FROM `product_list` WHERE product_id = '{$id}'");
        
        // Check if image record exists
        if($qry->num_rows > 0){
            $image = $qry->fetch_assoc(); // Fetch image details
            $image_path = $image['image_path']; // Get image path from database
            
            // Check if image file exists
            if(file_exists($image_path)){
                // Delete image file from server
                if(unlink($image_path)){
                    // Update database record to remove image path
                    $delete = $this->db->query("UPDATE `product_list` SET `image_path` = NULL WHERE product_id = '{$id}'");
                    
                    // Check if image path update was successful
                    if($delete){
                        $resp['status'] = 'success'; // Set success status
                        $resp['msg'] = 'Image successfully deleted.'; // Set success message
                    }else{
                        $resp['status'] = 'failed'; // Set failed status
                        $resp['msg'] = 'Failed to delete image record from database.'; // Set error message
                        $resp['error'] = $this->db->error; // Get database error
                    }
                }else{
                    $resp['status'] = 'failed'; // Set failed status
                    $resp['msg'] = 'Failed to delete image file.'; // Set error message
                }
            }else{
                $resp['status'] = 'failed'; // Set failed status
                $resp['msg'] = 'Image file does not exist.'; // Set error message
            }
        }else{
            $resp['status'] = 'failed'; // Set failed status
            $resp['msg'] = 'Image record not found.'; // Set error message
        }
    }else{
        $resp['status'] = 'failed'; // Set failed status
        $resp['msg'] = 'No image ID provided.'; // Set error message
    }
    
    // Return JSON-encoded response
    return json_encode($resp);
}
// Function to delete a product
function delete_product(){
    extract($_POST); // Extract POST variables

    // Update delete_flag to mark product as deleted
    @$update = $this->db->query("UPDATE `product_list` set delete_flag = 1 where product_id = '{$id}'");
    if($update){
        $resp['status']='success'; // Success status
        $_SESSION['flashdata']['type'] = 'success'; // Flash message type
        $_SESSION['flashdata']['msg'] = 'Product successfully deleted.'; // Success message
    }else{
        $resp['status']='failed'; // Failed status
        $resp['error']=$this->db->error; // Error details
    }
    return json_encode($resp); // Return JSON response
}

// Function to save or update stock
function save_stock(){
    extract($_POST); // Extract POST variables
    $data = ""; // Initialize data variable
    
    // Loop through POST data to process each key-value pair
    foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            $v = addslashes(trim($v)); // Trim and addslashes to sanitize data
            
            // Check if it's a new record or update existing record
            if(empty($id)){
                $cols[] = "`{$k}`"; // Collect column names
                $vals[] = "'{$v}'"; // Collect column values as SQL strings
            }else{
                if(!empty($data)) $data .= ", "; // Prepare data for UPDATE query
                $data .= " `{$k}` = '{$v}' "; // Construct SET clause for UPDATE query
            }
        }
    }
    
    // Prepare SQL query based on whether it's an INSERT or UPDATE operation
    if(isset($cols) && isset($vals)){
        $cols_join = implode(",",$cols); // Join columns for INSERT query
        $vals_join = implode(",",$vals); // Join values for INSERT query
    }
    if(empty($id)){
        $sql = "INSERT INTO `stock_list` ({$cols_join}) VALUES ($vals_join)"; // INSERT query
    }else{
        $sql = "UPDATE `stock_list` set {$data} where stock_id = '{$id}'"; // UPDATE query
    }
    
    // Execute SQL query
    @$save = $this->db->query($sql);
    if($save){
        $resp['status']="success"; // Success status
        if(empty($id))
            $resp['msg'] = "Stock successfully saved."; // Success message for save
        else
            $resp['msg'] = "Stock successfully updated."; // Success message for update
    }else{
        $resp['status']="failed"; // Failed status
        if(empty($id))
            $resp['msg'] = "Saving New Stock Failed."; // Failure message for save
        else
            $resp['msg'] = "Updating Stock Failed."; // Failure message for update
        $resp['error']=$this->db->error; // Error details
    }
    return json_encode($resp); // Return JSON response
}

// Function to delete a stock item
function delete_stock(){
    extract($_POST); // Extract POST variables

    // Delete stock item from database
    @$delete = $this->db->query("DELETE FROM `stock_list` where stock_id = '{$id}'");
    if($delete){
        $resp['status']='success'; // Success status
        $_SESSION['flashdata']['type'] = 'success'; // Flash message type
        $_SESSION['flashdata']['msg'] = 'Stock successfully deleted.'; // Success message
    }else{
        $resp['status']='failed'; // Failed status
        $resp['error']=$this->db->error; // Error details
    }
    return json_encode($resp); // Return JSON response
}

// Function to save a transaction
function save_transaction(){
    extract($_POST); // Extract POST variables
    $data = ""; // Initialize data variable
    $receipt_no = time(); // Generate unique receipt number based on current time
    $i = 0;
    
    // Ensure receipt number is unique
    while(true){
        $i++;
        $chk = $this->db->query("SELECT count(transaction_id) `count` FROM `transaction_list` where receipt_no = '{$receipt_no}' ")->fetch_array()['count'];
        if($chk > 0){
            $receipt_no = time().$i; // Append increment to ensure uniqueness
        }else{
            break;
        }
    }
    
    // Assign generated receipt number and user ID to POST data
    $_POST['receipt_no'] = $receipt_no;
    $_POST['user_id'] = $_SESSION['user_id'];
    
    // Loop through POST data to process each key-value pair
    foreach($_POST as $k => $v){
        if(!in_array($k,array('id')) && !is_array($_POST[$k])){
            $v = addslashes(trim($v)); // Trim and addslashes to sanitize data
            
            // Check if it's a new record or update existing record
            if(empty($id)){
                $cols[] = "`{$k}`"; // Collect column names
                $vals[] = "'{$v}'"; // Collect column values as SQL strings
            }else{
                if(!empty($data)) $data .= ", "; // Prepare data for UPDATE query
                $data .= " `{$k}` = '{$v}' "; // Construct SET clause for UPDATE query
            }
        }
    }
    
    // Prepare SQL query based on whether it's an INSERT or UPDATE operation
    if(isset($cols) && isset($vals)){
        $cols_join = implode(",",$cols); // Join columns for INSERT query
        $vals_join = implode(",",$vals); // Join values for INSERT query
    }
    if(empty($id)){
        $sql = "INSERT INTO `transaction_list` ({$cols_join}) VALUES ($vals_join)"; // INSERT query
    }else{
        $sql = "UPDATE `transaction_list` set {$data} where stock_id = '{$id}'"; // UPDATE query
    }
    
    // Execute SQL query
    @$save = $this->db->query($sql);
    if($save){
        $resp['status']="success"; // Success status
        $_SESSION['flashdata']['type']="success"; // Flash message type
        if(empty($id))
            $_SESSION['flashdata']['msg'] = "Transaction successfully saved."; // Success message for save
        else
            $_SESSION['flashdata']['msg'] = "Transaction successfully updated."; // Success message for update
        
        // Process transaction items
        if(empty($id)){
            $last_id = $this->db->insert_id; // Get last inserted ID
            $tid = empty($id) ? $last_id : $id; // Assign transaction ID
            $data = ""; // Initialize data variable
            
            // Loop through product IDs and quantities to prepare INSERT query data
            foreach($product_id as $k => $v){
                if(!empty($data)) $data .=","; // Prepare data for INSERT query
                $data .= "('{$tid}','{$v}','{$quantity[$k]}','{$price[$k]}')"; // Construct values for INSERT query
            }
            
            // Delete existing transaction items and insert new ones
            if(!empty($data))
                $this->db->query("DELETE FROM transaction_items where transaction_id = '{$tid}'"); // Delete existing items
            $sql = "INSERT INTO transaction_items (`transaction_id`,`product_id`,`quantity`,`price`) VALUES {$data}"; // INSERT query for transaction items
            $save = $this->db->query($sql); // Execute INSERT query for transaction items
            $resp['transaction_id'] = $tid; // Assign transaction ID to response
        }
    }else{
        $resp['status']="failed"; // Failed status
        if(empty($id))
            $resp['msg'] = "Saving New Transaction Failed."; // Failure message for save
        else
            $resp['msg'] = "Updating Transaction Failed."; // Failure message for update
        $resp['error']=$this->db->error; // Error details
    }
    return json_encode($resp); // Return JSON response
}

// Function to delete a transaction
function delete_transaction(){
    extract($_POST); // Extract POST variables

    // Delete transaction from database
    @$delete = $this->db->query("DELETE FROM `transaction_list` where transaction_id = '{$id}'");
    if($delete){
        $resp['status']='success'; // Success status
        $_SESSION['flashdata']['type'] = 'success'; // Flash message type
        $_SESSION['flashdata']['msg'] = 'Transaction successfully deleted.'; // Success message
    }else{
        $resp['status']='failed'; // Failed status
        $resp['error']=$this->db->error; // Error details
    }
    return json_encode($resp); // Return JSON response
}

// Function to save or update a shift
function save_shift() {
    global $conn; // Access global database connection
    
    // Extract POST data
    extract($_POST);
    
    // Initialize response array
    $resp = array();
    
    // Validate required fields
    if (!isset($cashier_id) || !isset($starting_cash) || !isset($starting_inventory)) {
        $resp['status'] = 'error'; // Error status
        $resp['msg'] = 'Required fields missing.'; // Error message
        return json_encode($resp); // Return JSON response
    }
    
    // Sanitize input (to prevent SQL injection, although prepared statements are recommended)
    $cashier_id = mysqli_real_escape_string($conn, $cashier_id);
    $starting_cash = mysqli_real_escape_string($conn, $starting_cash);
    $starting_inventory = mysqli_real_escape_string($conn, $starting_inventory);
    
    // Insert or update shift data into database
    if (empty($shift_id)) {
        // INSERT query for new shift
        $sql = "INSERT INTO `cashier_shifts` (`cashier_id`, `starting_cash`, `starting_inventory`, `ending_cash`, `ending_inventory`, `sales`, `notes`, `shift_date`, `time_in`)
                VALUES ('$cashier_id', '$starting_cash', '$starting_inventory', NULL, NULL, NULL, '$notes', CURDATE(), CURRENT_TIMESTAMP())";
    } else {
        // UPDATE query for existing shift
        $sql = "UPDATE `cashier_shifts` 
                SET `cashier_id` = '$cashier_id', `starting_cash` = '$starting_cash', `starting_inventory` = '$starting_inventory', 
                    `ending_cash` = NULL, `ending_inventory` = NULL, `sales` = NULL, `notes` = '$notes',
                    `shift_date` = CURDATE(), `time_in` = CURRENT_TIMESTAMP()
                WHERE `shift_id` = '$shift_id'";
    }
    
    // Execute SQL query
    $save = $conn->query($sql);
    
    if ($save) {
        $resp['status'] = 'success'; // Success status
        if (empty($shift_id)) {
            $resp['msg'] = 'Shift started successfully.'; // Success message for new shift
        } else {
            $resp['msg'] = 'Shift updated successfully.'; // Success message for update shift
        }
    } else {
        $resp['status'] = 'error'; // Error status
        if (empty($shift_id)) {
            $resp['msg'] = 'Failed to start shift.'; // Failure message for new shift
        } else {
            $resp['msg'] = 'Failed to update shift.'; // Failure message for update shift
        }
        $resp['error'] = $conn->error; // Error details
    }
    
    return json_encode($resp); // Return JSON response
}

// Function to get shift details
function get_shift_details() {
    global $conn; // Access global database connection
    
    // Get shift ID from POST data
    $shift_id = $_POST['shift_id'];
    $resp = array(); // Initialize response array
    
    // Query to fetch shift details
    $sql = "SELECT * FROM `cashier_shifts` WHERE shift_id = '$shift_id'";
    $qry = $conn->query($sql); // Execute query
    
    if ($qry->num_rows > 0) {
        $shift = $qry->fetch_assoc(); // Fetch shift data
        $resp['status'] = 'success'; // Success status
    
        // Ensure time_out is formatted properly or empty if not set
        $shift['time_out'] = $shift['time_out'] ? date('Y-m-d H:i:s', strtotime($shift['time_out'])) : null;
    
        $resp['data'] = $shift; // Assign shift data to response
    } else {
        $resp['status'] = 'error'; // Error status
        $resp['msg'] = 'Shift details not found.'; // Error message
    }
    
    echo json_encode($resp); // Return JSON response
}

// Function to update shift details with validation
function update_shift() {
    global $conn; // Access global database connection
    
    $current_user_id = $_SESSION['user_id']; // Current user ID
    $current_user_type = $_SESSION['type']; // Current user type
    
    extract($_POST); // Extract POST variables
    $resp = array(); // Initialize response array
    
    // Fetch the shift data to verify ownership
    $shift_check = $conn->query("SELECT cashier_id FROM `cashier_shifts` WHERE `shift_id` = '$shift_id'");
    $shift_data = $shift_check->fetch_assoc(); // Fetch shift data
    
    // Check for unauthorized access
    if ($current_user_type != 1 && $current_user_id != $shift_data['cashier_id']) {
        $resp['status'] = 'error'; // Error status
        $resp['msg'] = 'Unauthorized access.'; // Error message
        echo json_encode($resp); // Return JSON response
        return;
    }
    
    // Initialize variables with default values or empty strings if not set
    $starting_cash = isset($starting_cash) ? mysqli_real_escape_string($conn, $starting_cash) : '';
    $starting_inventory = isset($starting_inventory) ? mysqli_real_escape_string($conn, $starting_inventory) : '';
    $ending_cash = isset($ending_cash) ? mysqli_real_escape_string($conn, $ending_cash) : '';
    $ending_inventory = isset($ending_inventory) ? mysqli_real_escape_string($conn, $ending_inventory) : '';
    $sales = isset($sales) ? mysqli_real_escape_string($conn, $sales) : '';
    $notes = isset($notes) ? mysqli_real_escape_string($conn, $notes) : '';
    
    // Validate and update time_in if set
    if (isset($time_in)) {
        // Example validation: Ensure $time_in is in valid datetime format
        if (!strtotime($time_in)) {
            $resp['status'] = 'error'; // Error status
            $resp['msg'] = 'Invalid format for time in.'; // Error message
            echo json_encode($resp); // Return JSON response
            return;
        }
    
        $time_in = date('Y-m-d H:i:s', strtotime($time_in)); // Format time_in as needed
        $sql_time_in = ", `time_in` = '$time_in'"; // SQL update for time_in
    } else {
        $sql_time_in = ""; // No update for time_in
    }
    
    // Validate and update time_out if set
    if (isset($time_out)) {
        // Example validation: Ensure $time_out is in valid datetime format
        if (!strtotime($time_out)) {
            $resp['status'] = 'error'; // Error status
            $resp['msg'] = 'Invalid format for time out.'; // Error message
            echo json_encode($resp); // Return JSON response
            return;
        }
    
        $time_out = date('Y-m-d H:i:s', strtotime($time_out)); // Format time_out as needed
        $sql_time_out = ", `time_out` = '$time_out'"; // SQL update for time_out
    } else {
        $sql_time_out = ""; // No update for time_out
    }
    
    // Construct SQL query based on user type
    if ($current_user_type == 1) {
        // Admin can update all fields including time_in and time_out
        $sql = "UPDATE `cashier_shifts` 
                SET `starting_cash` = '$starting_cash', 
                    `starting_inventory` = '$starting_inventory'
                    $sql_time_out,
                    `notes` = '$notes', 
                    `ending_cash` = '$ending_cash', 
                    `ending_inventory` = '$ending_inventory', 
                    `sales` = '$sales' 
                WHERE `shift_id` = '$shift_id'";
    } else {
        // Cashier can update their specific fields including time_out
        $sql = "UPDATE `cashier_shifts` 
                SET `ending_cash` = '$ending_cash', 
                    `ending_inventory` = '$ending_inventory', 
                    `sales` = '$sales', 
                    `notes` = '$notes'
                    $sql_time_out
                WHERE `shift_id` = '$shift_id'";
    }
    
    // Perform the update query
    $update = $conn->query($sql);
    
    if ($update) {
        $resp['status'] = 'success'; // Success status
        $resp['msg'] = 'Shift updated successfully.'; // Success message
    } else {
        $resp['status'] = 'error'; // Error status
        $resp['msg'] = 'Failed to update shift.'; // Failure message
        $resp['error'] = $conn->error; // Error details
    }
    
    echo json_encode($resp); // Return JSON response
}
// Delete shift function with validation
function delete_shift() {
    global $conn; // Access global database connection

    $current_user_type = $_SESSION['type']; // Get current user type from session

    // Check if current user is not an admin (type 1)
    if ($current_user_type != 1) {
        $response['status'] = 'error'; // Error status
        $response['msg'] = 'Unauthorized access.'; // Error message
        echo json_encode($response); // Return JSON response
        return;
    }

    $shift_id = $_POST['shift_id']; // Get shift ID from POST data
    $response = array(); // Initialize response array

    // SQL query to delete shift by shift_id using prepared statement
    $sql = "DELETE FROM `cashier_shifts` WHERE shift_id = ?";
    $stmt = $conn->prepare($sql); // Prepare SQL statement

    // Check if preparing statement failed
    if ($stmt === false) {
        $response['status'] = 'error'; // Error status
        $response['msg'] = "Error: " . $conn->error; // Error message
        echo json_encode($response); // Return JSON response
        return;
    }

    $stmt->bind_param("i", $shift_id); // Bind shift_id parameter to the prepared statement

    // Execute the prepared statement
    if ($stmt->execute()) {
        $response['status'] = 'success'; // Success status
        $response['msg'] = "Shift deleted successfully"; // Success message
    } else {
        $response['status'] = 'error'; // Error status
        $response['msg'] = "Error deleting shift: " . $stmt->error; // Error message
    }

    $stmt->close(); // Close the prepared statement

    echo json_encode($response); // Return JSON response
}

$a = isset($_GET['a']) ? $_GET['a'] : ''; // Get action parameter from GET request
$action = new Actions(); // Create instance of Actions class

// Switch case to handle different actions based on $a parameter
switch ($a) {
    case 'login':
        echo $action->login();
        break;
    case 'customer_login':
        echo $action->customer_login();
        break;
    case 'logout':
        echo $action->logout();
        break;
    case 'customer_logout':
        echo $action->customer_logout();
        break;
    case 'save_user':
        echo $action->save_user();
        break;
    case 'delete_user':
        echo $action->delete_user();
        break;
    case 'update_credentials':
        echo $action->update_credentials();
        break;
    case 'save_category':
        echo $action->save_category();
        break;
    case 'delete_category':
        echo $action->delete_category();
        break;
    case 'save_product':
        echo $action->save_product();
        break;
    case 'delete_product':
        echo $action->delete_product();
        break;
    case 'save_stock':
        echo $action->save_stock();
        break;
    case 'delete_stock':
        echo $action->delete_stock();
        break;
    case 'save_transaction':
        echo $action->save_transaction();
        break;
    case 'delete_transaction':
        echo $action->delete_transaction();
        break;
    case 'delete_image':
        echo $action->delete_image();
        break;
    case 'reset_password':
        echo $action->reset_password();
        break;
    case 'save_shift':
        echo $action->save_shift();
        break;
    case 'update_shift':
        echo $action->update_shift();
        break;
    case 'get_shift_details':
        echo $action->get_shift_details();
        break;
    case 'delete_shift':
        echo $action->delete_shift();
        break;
    default:
        echo json_encode(array('status' => 'error', 'msg' => 'Invalid action'));
        break;
}

*/
?>
