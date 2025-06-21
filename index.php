<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location:./login.php");
    exit;
}
require_once('DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
if($_SESSION['type'] != 1 && in_array($page,array('maintenance','products','stocks'))){
    header("Location:./");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords(str_replace('_','',$page)) ?> | Julie's Management System</title>
    <link rel="stylesheet" href="./Font-Awesome-master/css/all.min.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./select2/css/select2.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./DataTables/datatables.min.css">
    <script src="./DataTables/datatables.min.js"></script>
    <script src="./select2/js/select2.full.min.js"></script>
    <script src="./Font-Awesome-master/js/all.min.js"></script>
    <script src="./js/script.js"></script>
    <style>
        :root{
            --bs-success-rgb:71, 222, 152 !important;
        }
        html,body{
            height:100%;
            width:100%;
            
        }
        @media (min-width: 992px) {
            .container, .container-lg, .container-md, .container-sm {
                max-width: 1250px;
            }
        }
        .modal-priority {
            z-index: 1050;
        }
        .modal-dialog {
            max-width: 800px; 
        }

        @media screen{
            body{
                background-image:url('./images/Index_BG(2).png') !important;
                background-size:cover;
                background-repeat:no-repeat;
                background-position:center center;
                backdrop-filter: brightness(0.7);
            }
        }
        main{
            height:100%;
            display:flex;
            flex-flow:column;
        }
        #page-container{
            flex: 1 1 auto; 
            overflow:auto;
        }
        #topNavBar{
            flex: 0 1 auto; 
        }
        .thumbnail-img{
            width:50px;
            height:50px;
            margin:2px
        }
        .truncate-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }
        .truncate-3 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .modal-dialog.large {
            width: 80% !important;
            max-width: unset;
        }
        .modal-dialog.mid-large {
            width: 50% !important;
            max-width: unset;
        }
        @media (max-width:720px){
            .modal-dialog.large {
                width: 100% !important;
                max-width: unset;
            }
            .modal-dialog.mid-large {
                width: 100% !important;
                max-width: unset;
            }  
        }
        .display-select-image{
            width:60px;
            height:60px;
            margin:2px
        }
        img.display-image {
            width: 100%;
            height: 45vh;
            object-fit: cover;
            background: black;
        }
        /* width */
        ::-webkit-scrollbar {
        width: 5px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
        background: #f1f1f1; 
        }
        
        /* Handle */
        ::-webkit-scrollbar-thumb {
        background: #898; 
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
        background: #555; 
        }
        .img-del-btn{
            right: 2px;
            top: -3px;
        }
        .img-del-btn>.btn{
            font-size: 10px;
            padding: 0px 2px !important;
        }

        #plist .item.disabled {
        cursor: not-allowed;
        background-color: #f8d7da;
        }
        .disabled {
        pointer-events: none;
        opacity: 0.6;
        }
        .custom-navbar {
        background: linear-gradient(0deg, #707070, #9c4b4b); 
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 10px 0; 
        opacity: 80%;
        z-index: 1000;
        }

        .custom-navbar .navbar-nav .nav-link {
            color: #ffffff; 
            font-weight: bold;
            transition: all 0.5s ease;
        }

        .custom-navbar .navbar-nav .nav-link:hover {
            color: #ff00ff; 
        }

        .dark-mode .card.rounded-0.shadow {
            background-color: #444; /* Dark background for cards */
            color: #fff; /* Text color for cards in dark mode */
            opacity: 90%;
        }

        .dark-mode .table tbody tr:nth-child(odd) {
            background-color: #555; /* Dark background for odd rows */
            color: #ff0; /* Text color for odd rows in dark mode */
            /* opacity: 90%; */
        }

        .dark-mode .table tbody tr:nth-child(even) {
            background-color: #666; /* Dark background for even rows */
            color: #fff; /* Text color for even rows in dark mode */
            opacity: 90%;
        }
        
        .badge-available {
            background-color: #28a745; 
            color: #ffffff; 
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.875rem;
        }
        .badge-unavailable {
            background-color: #dc3545; 
            color: #ffffff; 
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <main>
    <nav class="navbar navbar-expand-lg navbar-light custom-navbar">
    <div class="container">
        <a class="navbar-brand" href="./">
            <img src="./images/tiny_logo.png" width="150" height="60"/> 
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'home')? 'active' : '' ?>" aria-current="page" href="./?page=home">
                        <i class="fa fa-home"></i> Home
                    </a>
                </li>
                <?php if($_SESSION['type'] == 1): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'products')? 'active' : '' ?>" href="./?page=products">
                        <i class="fa fa-shopping-cart"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'stocks')? 'active' : '' ?>" href="./?page=stocks">
                        <i class="fa fa-cube"></i> Stocks
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'users')? 'active' : '' ?>" href="./?page=users">
                        <i class="fa fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'maintenance')? 'active' : '' ?>" href="./?page=maintenance">
                        <i class="fa fa-wrench"></i> Maintenance
                    </a>
                </li>
                <?php endif; ?>
                <?php if (in_array($_SESSION['type'], [1, 0, NULL])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'manage_shifts')? 'active' : '' ?>" href="./?page=manage_shifts">
                        <i class="fa fa-clock"></i> Shift
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'sales')? 'active' : '' ?>" href="./?page=sales">
                        <i class="fa fa-dollar-sign"></i> POS
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'sales_report')? 'active' : '' ?>" href="./?page=sales_report">
                        <i class="fa fa-chart-bar"></i> Sales
                    </a>
                </li>
            </ul>
            <div class="form-check form-switch ms-auto">
                <input class="form-check-input" type="checkbox" id="darkModeToggle">
                <label class="form-check-label" for="darkModeToggle"><i id="darkModeIcon" class="fa fa-sun"></i></label>
            </div>
        </div>
        <div>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle bg-transparent text-light border-0" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    Hello <?php echo $_SESSION['fullname'] ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="./?page=manage_account">Manage Account</a></li>
                    <li><a class="dropdown-item" href="./Actions.php?a=logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
    
    <div class="container py-3" id="page-container">
        <?php 
            if(isset($_SESSION['flashdata'])):
        ?>
        <div class="dynamic_alert alert alert-<?php echo $_SESSION['flashdata']['type'] ?> rounded-0 shadow">
        <div class="float-end"><a href="javascript:void(0)" class="text-dark text-decoration-none" onclick="$(this).closest('.dynamic_alert').hide('slow').remove()">x</a></div>
            <?php echo $_SESSION['flashdata']['msg'] ?>
        </div>
        <?php unset($_SESSION['flashdata']) ?>
        <?php endif; ?>
        <?php
            include $page.'.php';
        ?>
    </div>
    </main>
    <div class="modal fade" id="uni_modal" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm rounded-0 btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
            <button type="button" class="btn btn-sm rounded-0 btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="uni_modal_secondary" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-md modal-dialog-centered  rounded-0" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm rounded-0 btn-primary" id='submit' onclick="$('#uni_modal_secondary form').submit()">Save</button>
            <button type="button" class="btn btn-sm rounded-0 btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered  rounded-0" role="document">
        <div class="modal-content rounded-0 rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body">
            <div id="delete_content"></div>
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-primary btn-sm rounded-0" id='confirm' onclick="">Continue</button>
            <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
<script>
    function toggleDarkMode() {
        const container = document.querySelector('.container.py-3');
        const cards = document.querySelectorAll('.card.rounded-0.shadow');
        const icon = document.getElementById('darkModeIcon');
        const toggleButton = document.getElementById('darkModeToggle');

        container.classList.toggle('dark-mode');
        cards.forEach(card => card.classList.toggle('dark-mode'));

        // Adjust icon based on dark mode state
        if (container.classList.contains('dark-mode')) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            toggleButton.checked = true; // Check the toggle button
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            toggleButton.checked = false; // Uncheck the toggle button
        }

        // Save the user's preference in local storage
        localStorage.setItem('darkMode', container.classList.contains('dark-mode'));
    }

    // Set the initial mode based on user preference from local storage
    document.addEventListener('DOMContentLoaded', function () {
        const darkModePreference = localStorage.getItem('darkMode');
        const container = document.querySelector('.container.py-3');
        const icon = document.getElementById('darkModeIcon');
        const cards = document.querySelectorAll('.card.rounded-0.shadow');
        const toggleButton = document.getElementById('darkModeToggle');

        if (darkModePreference === 'true') {
            container.classList.add('dark-mode');
            cards.forEach(card => card.classList.add('dark-mode'));
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            toggleButton.checked = true; // Set the checkbox to checked
        } else {
            toggleButton.checked = false; // Set the checkbox to unchecked
        }

        // Attach the toggle event listener to the button
        toggleButton.addEventListener('change', toggleDarkMode); // Use 'change' event to capture toggle state
    });
</script>

</body>
</html>
