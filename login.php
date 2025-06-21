<?php
// Start a new session or resume the existing session
session_start();

// Check if the user is already logged in by verifying if 'user_id' is set in the session and greater than 0
if(isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0){
    // If the user is logged in, redirect them to the home page
    header("Location:./");
    // Stop further script execution
    exit;
}

// Include the database connection script
require_once('DBConnection.php');

// Determine the current page to load, default to 'home' if not specified in the URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN | Julie's POS and Management System</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/script.js"></script>
    <style>
        html, body {
            height: 100%;
        }
        #loading-screen {
            position: fixed;
            width: 100%;
            height: 100%;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        #loading-gif {
            width: 100%;
            height: 100%;
            object-fit: cover; 
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('./images/Login_blur.jpg') !important;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            filter: blur(5px); 
            z-index: -1; 
        }
        @keyframes glowing {
            /* 0% {
                text-shadow: 0 0 10px #FA8072, 0 0 20px #FA8072, 0 0 30px #FA8072, 0 0 40px #FA8072, 0 0 50px #FA8072, 0 0 60px #FA8072, 0 0 70px #FA8072, 0 0 80px #FA8072;
            } */
            /* 50% {
                text-shadow: 0 0 20px #FA8072, 0 0 30px #FA8072, 0 0 40px #FA8072, 0 0 50px #FA8072, 0 0 60px #FA8072, 0 0 70px #FA8072, 0 0 80px #FA8072, 0 0 90px #FA8072;
            } */
            100% {
                text-shadow: 0 0 10px #FA8072, 0 0 20px #FA8072, 0 0 30px #FA8072, 0 0 40px #FA8072, 0 0 50px #FA8072, 0 0 60px #FA8072, 0 0 70px #FA8072, 0 0 80px #FA8072;
            }
        }
        #sys_title {
            font-family: 'Pacifico', cursive;
            color: #F9E2AF;
            text-shadow: 2px 2px 4px rgba(0, 2, 3, 0.3);
            padding: 5rem;
            font-size: 6rem;
            margin-bottom: 5;
            animation: glowing 10s linear infinite;
        }

        @media (max-width: 700px) {
            #sys_title {
                font-size: 3rem !important;
            }
        }

        .card-body {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background: linear-gradient(to right bottom, #d16ba5, #c777b9, #ba83ca, #aa8fd8, #9a9ae1, #8aa7ec, #79b3f4, #69bff8, #52cffe, #41dfff, #46eefa, #5ffbf1);
            padding: 20px;
            animation: glowing-border 5s ease-out infinite alternate;
        }

        @keyframes glowing-border {
            /* /* 0% {
                box-shadow: 0 0 10px #F9E2AF, 0 0 20px #F9E2AF, 0 0 30px #F9E2AF, 0 0 40px #F9E2AF, 0 0 50px #F9E2AF;
            } */
            100% {
                box-shadow: 0 0 20px #F9E2AF, 0 0 30px #F9E2AF, 0 0 40px #F9E2AF, 0 0 50px #F9E2AF, 0 0 60px #F9E2AF;
            } */
        }

        .form-control {
            border-radius: 10px;
            border: none;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 12px;
            transition: background-color 4s ease, box-shadow 4s ease;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background-color: #86A8E7;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 14px;
            transition: background-color 2s ease, transform 2s ease;
        }

        .btn-primary:hover {
            background-color: #FF4EAD;
            transform: translateY(-2px);
        }

        .fancy-input {
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .fancy-button {
            font-family: 'Arial', sans-serif;
            color: white;
            font-weight: bold;
            border-radius: 15px;
        }
    </style>
</head>
<body class="">

    <div id="loading-screen">
        <img id="loading-gif" src="./images/Julies Loading.gif" alt="Loading...">
    </div>

    <div class="h-100 d-flex justify-content-center align-items-center">
        <div class='w-100'>
            <div class="rounded-border">
                <h1 class="py-5 text-center text-light px-4" id="sys_title">Julie's POS and Management System</h1>
            </div>
            <div class="card my-3 col-md-4 offset-md-4">
                <div class="card-body">
                    <form action="" id="login-form">
                        <center><small>Please enter your credentials.</small></center>
                        <div class="form-group">
                            <label for="username" class="control-label">Username</label>
                            <input type="text" id="username" autofocus name="username" class="form-control form-control-sm rounded-0 fancy-input" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="control-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control form-control-sm rounded-0 fancy-input" required>
                        </div>
                        <div class="form-group d-flex justify-content-end">
                            <button type="button" class="fancy-button btn btn-sm btn-secondary rounded-0 my-1" id="forgot-btn">Forgot Password?</button>
                            <button type="submit" class="fancy-button btn btn-sm btn-primary rounded-0 my-1">Login</button>
                        </div>
                    </form>
                </div>
            </div>
    <div id="forgot-form" style="display: none;">
        <div class="card my-3 col-md-4 offset-md-4">
            <div class="card-body">
                <form action="" id="forgot-password-form">
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control form-control-sm rounded-0 fancy-input" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password" class="control-label">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control form-control-sm rounded-0 fancy-input" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password" class="control-label">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control form-control-sm rounded-0 fancy-input" required>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                        <button type="button" class="fancy-button btn btn-sm btn-secondary rounded-0 my-1" id="back-to-login-btn">Back to Login</button>
                        <button type="submit" class="fancy-button btn btn-sm btn-primary rounded-0 my-1">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
    $(document).ready(function() {
        // When the document is fully loaded, execute the following function
        
        // Click event handler for the 'Forgot Password' button
        $('#forgot-btn').click(function() {
            // Hide the login form
            $('#login-form').hide();
            // Show the forgot password form
            $('#forgot-form').show();
        });

        // Click event handler for the 'Back to Login' button
        $('#back-to-login-btn').click(function() {
            // Hide the forgot password form
            $('#forgot-form').hide();
            // Show the login form
            $('#login-form').show();
        });

        // Submit event handler for the login form
        $('#login-form').submit(function(e) {
            // Prevent the default form submission
            e.preventDefault();
            var _this = $(this);
            // Remove any existing messages
            $('.pop_msg').remove();
            // Create a new div element for displaying messages
            var el = $('<div>');
            el.addClass("pop_msg alert");
            el.hide();
            // Send an AJAX POST request to the login action
            $.ajax({
                url: './Actions.php?a=login',
                method: 'POST',
                data: _this.serialize(), // Serialize the form data
                dataType: 'JSON',
                error: err => {
                    console.log(err);
                    alert("An error occurred.");
                },
                success: function(resp) {
                    // Handle the response from the server
                    if (resp.status == 'success') {
                        // If login is successful, redirect to the home page
                        location.replace('./');
                    } else if (resp.msg) {
                        // If there is an error message, display it
                        el.addClass('alert-danger');
                        el.text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                    } else {
                        alert("An error occurred.");
                        console.log(resp);
                    }
                }
            });
        });

        // Submit event handler for the forgot password form
        $('#forgot-password-form').submit(function(e) {
            // Prevent the default form submission
            e.preventDefault();
            var _this = $(this);
            // Remove any existing messages
            $('.pop_msg').remove();
            // Create a new div element for displaying messages
            var el = $('<div>');
            el.addClass("pop_msg alert");
            el.hide();
            // Send an AJAX POST request to the reset password action
            $.ajax({
                url: './Actions.php?a=reset_password',
                method: 'POST',
                data: _this.serialize(), // Serialize the form data
                dataType: 'json',
                error: function(err) {
                    console.error("AJAX error:", err);
                    alert("An error occurred. Please check the console for details.");
                },
                success: function(resp) {
                    // Handle the response from the server
                    if (resp.status == 'success') {
                        // If password reset is successful, display a success message and switch to the login form
                        el.addClass('alert-success');
                        el.text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        $('#forgot-form').hide();
                        $('#login-form').show();
                    } else if (resp.msg) {
                        // If there is an error message, display it
                        el.addClass('alert-danger');
                        el.text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                    } else {
                        alert("An error occurred.");
                        console.log(resp);
                    }
                }
            });
        });

        // Hide the loading screen after 1.5 seconds
        setTimeout(() => {
            $('#loading-screen').fadeOut('slow');
        }, 1500);
    });
</script>
</body>
</html>
