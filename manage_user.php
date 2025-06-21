<?php
require_once("DBConnection.php");

if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `user_list` where user_id = '{$_GET['id']}'");
    foreach($qry->fetch_assoc() as $k => $v){
        $$k = $v;
    }
     // Set default type to 0 if not set (for existing users)
    if (!isset($type)) {
        $type = 0;
    }
}
?>
<div class="container-fluid">
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
            <label for="email" class="control-label">Email</label>
            <input type="email" name="email" id="email" required class="form-control form-control-sm rounded-0" value="<?php echo isset($email) ? $email : '' ?>">
        </div>
        <div class="form-group">
            <label for="phone_number" class="control-label">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" required class="form-control form-control-sm rounded-0" value="<?php echo isset($phone_number) ? $phone_number : '' ?>">
        </div>
        <div class="form-group">
            <label for="type" class="control-label">Type</label>
            <select name="type" id="type" class="form-select form-select-sm rounded-0" required>
                <option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Administrator</option>
                <option value="0" <?php echo isset($type) && $type == 0 ? 'selected' : '' ?>>Cashier</option>
            </select>
        </div>

    </form>
</div>


<script>
    $(function(){
        $('#user-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=save_user',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    $('#uni_modal button').attr('disabled',false)
                    $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($user_id) ?>" != 1){
                            _this.get(0).reset();
                            // Display initial password
                            alert("User created successfully. Initial Password: " + resp.initial_password);
                        }
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    $('#uni_modal button').attr('disabled',false)
                    $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
