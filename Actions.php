<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('DBConnection.php');

/**
 * Your actions.php script is structured as a class named Actions that extends the DBConnection class.
 * This approach encapsulates various functionalities into methods within a class, following Object-Oriented Programming (OOP) principles.
 * Here's a breakdown of the format and how it applies OOP concepts:
 */

/**
 * Class Definition:
 * The Actions class is defined, encapsulating related methods (functions) that perform specific actions, such as user login, password reset, and CRUD operations for users, categories, suppliers, products, and stock entries.
 * This class extends another class, DBConnection, which means it inherits methods and properties from DBConnection.
 */
class Actions extends DBConnection {

    /**
     * Constructor of the Actions class:
     * The constructor initializes the database connection using the parent's (DBConnection) constructor.
     * This ensures that when an instance of Actions is created, the database connection is established.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Destructor of the Actions class:
     * The destructor closes the database connection using the parent's (DBConnection) destructor.
     * This ensures that the database connection is properly closed when an instance of Actions is destroyed.
     */
    public function __destruct() {
        parent::__destruct();
    }

    /**
     * Method for user login:
     * This method encapsulates the login logic within a method of the Actions class.
     * It uses prepared statements to securely query the database for the user's credentials.
     * If the credentials match, the user session is initialized; otherwise, an error message is returned.
     */
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM user_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->db->query($sql)->fetch_array();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }

    function reset_password() {
        extract($_POST);
        
        if ($new_password !== $confirm_password) {
            $resp['status'] = 'error';
            $resp['msg'] = 'Passwords do not match.';
            echo json_encode($resp);
            return;
        }
        
        $hashed_password = md5($new_password);
        
        $update_stmt = $this->db->prepare('UPDATE user_list SET password = ? WHERE email = ?');
        $update_stmt->bind_param('ss', $hashed_password, $email); // 'ss' indicates two string parameters
        $update_stmt->execute();
        
        if ($update_stmt->affected_rows > 0) {
            $resp['status'] = 'success';
            $resp['msg'] = 'Password reset successfully.';
        } else {
            $resp['status'] = 'error';
            $resp['msg'] = 'Failed to reset password.';
        }
        
        echo json_encode($resp);
    }    

    function logout(){
        session_destroy();
        header("location:./");
    }
    
    function save_user(){
        global $conn;
        extract($_POST);
        $data = "";
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
        if(empty($id)){
            $initial_password = substr(md5(uniqid()), 0, 8); // Generate an initial password
            $hashed_password = md5($initial_password); // Hash the initial password
            $cols[] = 'password';
            $values[] = "'{$hashed_password}'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
    
        @$check = $conn->query("SELECT count(user_id) as `count` FROM user_list where `username` = '{$username}' 
        ".($id > 0 ? " and user_id != '{$id}' " : ""))->fetch_array()['count'];
        if(@$check > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `user_list` {$data}";
            }else{
                $sql = "UPDATE `user_list` set {$data} where user_id = '{$id}'";
            }
            @$save = $conn->query($sql);
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
        echo json_encode($resp);
    }
    
    function delete_user(){
        extract($_POST);

        @$delete = $this->db->query("DELETE FROM `user_list` where user_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->db->error;
        }
        return json_encode($resp);
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `user_list` set {$data} where user_id = '{$_SESSION['user_id']}'";
            @$save = $this->db->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
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
        return json_encode($resp);
    }
    function save_category(){
        extract($_POST);
        $data = "";
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
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `category_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `category_list` set {$data} where category_id = '{$id}'";
        }
        @$check= $this->db->query("SELECT COUNT(category_id) as count from `category_list` where `name` = '{$name}' ".($id > 0 ? " and category_id != '{$id}'" : ""))->fetch_array()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Category already exists.';
        }else{
            @$save = $this->db->query($sql);
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
        return json_encode($resp);
    }
    function delete_category(){
        extract($_POST);

        @$update = $this->db->query("UPDATE `category_list` set `delete_flag` = 1 where category_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Category successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->db->error;
        }
        return json_encode($resp);
    }
    function save_product(){
        extract($_POST);
        $data = "";
    
        $image_paths = [];
        if (!empty($_FILES['images']['tmp_name'][0])) {
            $upload_directory = 'bsms/images/1. General images directory/'; 
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0777, true);
            }
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['images']['name'][$key];
                $file_tmp = $_FILES['images']['tmp_name'][$key];
                $file_path = $upload_directory . basename($file_name);
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $image_paths[] = $file_path;
                }
            }
        }
        // Prepare data for database insertion or update
        foreach($_POST as $k => $v){
            if(!in_array($k, ['id'])) {
                $v = addslashes(trim($v));
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        // Handle inserting or updating product data
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `product_list` ({$cols_join}) VALUES ({$vals_join})";
        }else{
            $sql = "UPDATE `product_list` SET {$data} WHERE product_id = '{$id}'";
        }
    
        $check_product_code = $this->db->query("SELECT COUNT(product_id) AS count FROM `product_list` WHERE `product_code` = '{$product_code}' AND delete_flag = 0 " . ($id > 0 ? " AND product_id != '{$id}'" : ""))->fetch_array()['count'];
        $check_product_name = $this->db->query("SELECT COUNT(product_id) AS count FROM `product_list` WHERE `name` = '{$name}' AND delete_flag = 0 " . ($id > 0 ? " AND product_id != '{$id}'" : ""))->fetch_array()['count'];
    
        if($check_product_code > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Product Code already exists.';
        } elseif($check_product_name > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Product Name already exists.';
        } else {
            $save = $this->db->query($sql);
            if($save){
                $resp['status'] = "success";
                if(empty($id)){
                    $resp['msg'] = "Product successfully saved.";
                    $new_id = $this->db->insert_id;
                } else {
                    $resp['msg'] = "Product successfully updated.";
                    $new_id = $id;
                }
    
                foreach ($image_paths as $image_path) {
                    $this->db->query("UPDATE `product_list` SET `image_path` = '{$image_path}' WHERE `product_id` = '{$new_id}'");
                }
            } else {
                $resp['status'] = "failed";
                if(empty($id)){
                    $resp['msg'] = "Saving New Product Failed.";
                } else {
                    $resp['msg'] = "Updating Product Failed.";
                }
                $resp['error'] = $this->db->error;
            }
        }
        return json_encode($resp);
    }
     
    function delete_image(){
        extract($_POST);
        $resp = array();
        
        if(isset($id)){
            $qry = $this->db->query("SELECT image_path FROM `product_list` WHERE product_id = '{$id}'");
            if($qry->num_rows > 0){
                $image = $qry->fetch_assoc();
                $image_path = $image['image_path'];
                if(file_exists($image_path)){
                    if(unlink($image_path)){
                        $delete = $this->db->query("UPDATE `product_list` SET `image_path` = NULL WHERE product_id = '{$id}'");
                        if($delete){
                            $resp['status'] = 'success';
                            $resp['msg'] = 'Image successfully deleted.';
                        }else{
                            $resp['status'] = 'failed';
                            $resp['msg'] = 'Failed to delete image record from database.';
                            $resp['error'] = $this->db->error;
                        }
                    }else{
                        $resp['status'] = 'failed';
                        $resp['msg'] = 'Failed to delete image file.';
                    }
                }else{
                    $resp['status'] = 'failed';
                    $resp['msg'] = 'Image file does not exist.';
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Image record not found.';
            }
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'No image ID provided.';
        }
        
        return json_encode($resp);
    }
    

    function delete_product(){
        extract($_POST);

        @$update = $this->db->query("UPDATE `product_list` set delete_flag = 1 where product_id = '{$id}'");
        if($update){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Product successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->db->error;
        }
        return json_encode($resp);
    }
    function save_stock(){
        extract($_POST);
        $data = "";
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
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `stock_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `stock_list` set {$data} where stock_id = '{$id}'";
        }
        
        @$save = $this->db->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id))
                $resp['msg'] = "Stock successfully saved.";
            else
                $resp['msg'] = "Stock successfully updated.";
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Stock Failed.";
            else
                $resp['msg'] = "Updating Stock Failed.";
            $resp['error']=$this->db->error;
        }
        return json_encode($resp);
    }
    function delete_stock(){
        extract($_POST);

        @$delete = $this->db->query("DELETE FROM `stock_list` where stock_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Stock successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->db->error;
        }
        return json_encode($resp);
    }
    function save_transaction(){
        extract($_POST);
        $data = "";
        $receipt_no = time();
        $i = 0;
        while(true){
            $i++;
            $chk = $this->db->query("SELECT count(transaction_id) `count` FROM `transaction_list` where receipt_no = '{$receipt_no}' ")->fetch_array()['count'];
            if($chk > 0){
                $receipt_no = time().$i;
            }else{
                break;
            }
        }
        $_POST['receipt_no'] = $receipt_no;
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id')) && !is_array($_POST[$k])){
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
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `transaction_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `transaction_list` set {$data} where stock_id = '{$id}'";
        }
        
        @$save = $this->db->query($sql);
        if($save){
            $resp['status']="success";
            $_SESSION['flashdata']['type']="success";
            if(empty($id))
                $_SESSION['flashdata']['msg'] = "Transaction successfully saved.";
            else
                $_SESSION['flashdata']['msg'] = "Transaction successfully updated.";
            if(empty($id))
            $last_id = $this->db->insert_id;
                $tid = empty($id) ? $last_id : $id;
            $data ="";
            foreach($product_id as $k => $v){
                if(!empty($data)) $data .=",";
                $data .= "('{$tid}','{$v}','{$quantity[$k]}','{$price[$k]}')";
            }
            if(!empty($data))
            $this->db->query("DELETE FROM transaction_items where transaction_id = '{$tid}'");
            $sql = "INSERT INTO transaction_items (`transaction_id`,`product_id`,`quantity`,`price`) VALUES {$data}";
            $save = $this->db->query($sql);
            $resp['transaction_id'] = $tid;
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Transaction Failed.";
            else
                $resp['msg'] = "Updating Transaction Failed.";
            $resp['error']=$this->db->error;
        }
        return json_encode($resp);
    }
    function delete_transaction(){
        extract($_POST);

        @$delete = $this->db->query("DELETE FROM `transaction_list` where transaction_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Transaction successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->db->error;
        }
        return json_encode($resp);
        }
    function save_shift() {
        global $conn;
    
        // Extract POST data
        extract($_POST);
    
        // Initialize response array
        $resp = array();
    
        // Validate input
        if (!isset($cashier_id) || !isset($starting_cash) || !isset($starting_inventory)) {
            $resp['status'] = 'error';
            $resp['msg'] = 'Required fields missing.';
            return json_encode($resp);
        }
    
        $cashier_id = mysqli_real_escape_string($conn, $cashier_id);
        $starting_cash = mysqli_real_escape_string($conn, $starting_cash);
        $starting_inventory = mysqli_real_escape_string($conn, $starting_inventory);
    
        if (empty($shift_id)) {
            $sql = "INSERT INTO `cashier_shifts` (`cashier_id`, `starting_cash`, `starting_inventory`, `ending_cash`, `ending_inventory`, `sales`, `notes`, `shift_date`, `time_in`)
                    VALUES ('$cashier_id', '$starting_cash', '$starting_inventory', NULL, NULL, NULL, '$notes', CURDATE(), CURRENT_TIMESTAMP())";
        } else {
            $sql = "UPDATE `cashier_shifts` 
                    SET `cashier_id` = '$cashier_id', `starting_cash` = '$starting_cash', `starting_inventory` = '$starting_inventory', 
                        `ending_cash` = NULL, `ending_inventory` = NULL, `sales` = NULL, `notes` = '$notes',
                        `shift_date` = CURDATE(), `time_in` = CURRENT_TIMESTAMP()
                    WHERE `shift_id` = '$shift_id'";
        }
    
        $save = $conn->query($sql);
    
        if ($save) {
            $resp['status'] = 'success';
            if (empty($shift_id)) {
                $resp['msg'] = 'Shift started successfully.';
            } else {
                $resp['msg'] = 'Shift updated successfully.';
            }
        } else {
            $resp['status'] = 'error';
            if (empty($shift_id)) {
                $resp['msg'] = 'Failed to start shift.';
            } else {
                $resp['msg'] = 'Failed to update shift.';
            }
            $resp['error'] = $conn->error;
        }
    
        return json_encode($resp);
    }

    function get_shift_details() {
        global $conn;
    
        $shift_id = $_POST['shift_id'];
        $resp = array();
    
        $sql = "SELECT * FROM `cashier_shifts` WHERE shift_id = '$shift_id'";
        $qry = $conn->query($sql);
    
        if ($qry->num_rows > 0) {
            $shift = $qry->fetch_assoc();
            $resp['status'] = 'success';
    
            $shift['time_out'] = $shift['time_out'] ? date('Y-m-d H:i:s', strtotime($shift['time_out'])) : null;
    
            $resp['data'] = $shift;
        } else {
            $resp['status'] = 'error';
            $resp['msg'] = 'Shift details not found.';
        }
    
        echo json_encode($resp);
    }
    
// Update shift function with validation
function update_shift() {
    global $conn;

    $current_user_id = $_SESSION['user_id'];
    $current_user_type = $_SESSION['type'];

    extract($_POST);

    $resp = array();

    // Fetch the shift data to verify ownership
    $shift_check = $conn->query("SELECT cashier_id FROM `cashier_shifts` WHERE `shift_id` = '$shift_id'");
    $shift_data = $shift_check->fetch_assoc();

    if ($current_user_type != 1 && $current_user_id != $shift_data['cashier_id']) {
        $resp['status'] = 'error';
        $resp['msg'] = 'Unauthorized access.';
        echo json_encode($resp);
        return;
    }

    // Initialize variables with default values or empty strings if not set
    $starting_cash = isset($starting_cash) ? mysqli_real_escape_string($conn, $starting_cash) : '';
    $starting_inventory = isset($starting_inventory) ? mysqli_real_escape_string($conn, $starting_inventory) : '';
    $ending_cash = isset($ending_cash) ? mysqli_real_escape_string($conn, $ending_cash) : '';
    $ending_inventory = isset($ending_inventory) ? mysqli_real_escape_string($conn, $ending_inventory) : '';
    $sales = isset($sales) ? mysqli_real_escape_string($conn, $sales) : '';
    $notes = isset($notes) ? mysqli_real_escape_string($conn, $notes) : '';

    if (isset($time_in)) {
        if (!strtotime($time_in)) {
            $resp['status'] = 'error';
            $resp['msg'] = 'Invalid format for time in.';
            echo json_encode($resp);
            return;
        }

        $time_in = date('Y-m-d H:i:s', strtotime($time_in)); 
        $sql_time_in = ", `time_in` = '$time_in'";
    } else {
        $sql_time_in = ""; // No update for time_in
    }

    if (isset($time_out)) {
        if (!strtotime($time_out)) {
            $resp['status'] = 'error';
            $resp['msg'] = 'Invalid format for time out.';
            echo json_encode($resp);
            return;
        }

        $time_out = date('Y-m-d H:i:s', strtotime($time_out)); 
        $sql_time_out = ", `time_out` = '$time_out'";
    } else {
        $sql_time_out = ""; // No update for time_out
    }
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
        $resp['status'] = 'success';
        $resp['msg'] = 'Shift updated successfully.';
    } else {
        $resp['status'] = 'error';
        $resp['msg'] = 'Failed to update shift.';
        $resp['error'] = $conn->error;
    }

    echo json_encode($resp);
}

    // Delete shift function with validation
    function delete_shift() {
        global $conn;

        $current_user_type = $_SESSION['type'];

        if ($current_user_type != 1) {
            $response['status'] = 'error';
            $response['msg'] = 'Unauthorized access.';
            echo json_encode($response);
            return;
        }

        $shift_id = $_POST['shift_id'];
        $response = array();

        $sql = "DELETE FROM `cashier_shifts` WHERE shift_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $response['status'] = 'error';
            $response['msg'] = "Error: " . $conn->error;
            echo json_encode($response);
            return;
        }

        $stmt->bind_param("i", $shift_id);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['msg'] = "Shift deleted successfully";
        } else {
            $response['status'] = 'error';
            $response['msg'] = "Error deleting shift: " . $stmt->error;
        }

        $stmt->close();

        echo json_encode($response);
    }

}
    $a = isset($_GET['a']) ? $_GET['a'] : '';
    $action = new Actions();
    
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
    