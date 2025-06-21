<?php
require_once("DBConnection.php");

if(isset($_GET['id'])){
    $qry = $conn->query("SELECT p.*, c.name as cname FROM `product_list` p INNER JOIN `category_list` c ON p.category_id = c.category_id WHERE p.product_id = '{$_GET['id']}'");
    $product = $qry->fetch_assoc(); // Using fetch_assoc() for associative array

    // Check if product details were fetched successfully
    if ($product) {
        extract($product); // Extract variables for easier access

        // Correct the image path slashes for HTML display
        $image_path = str_replace('\\', '/', $image_path);
?>
<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header .product-details {
        flex: 1;
        padding-right: 20px; 
    }
    .modal-header .product-image {
        width: 80px; 
        height: 80px; 
        border-radius: 50%;
        overflow: hidden;
    }
    .modal-header .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
<div class="container-fluid">
    <div class="col-12">
        <div class="modal-header">
            <div class="product-details">
                <div class="w-100 mb-1">
                    <div class="fs-6"><b>Product Code:</b></div>
                    <div class="fs-5 ps-4"><?php echo isset($name) ? $name : '' ?></div>
                </div>
                <div class="w-100 mb-1">
                    <div class="fs-6"><b>Category:</b></div>
                    <div class="fs-5 ps-4"><?php echo isset($cname) ? $cname : '' ?></div>
                </div>
                <div class="w-100 mb-1">
                    <div class="fs-6"><b>Product:</b></div>
                    <div class="fs-5 ps-4"><?php echo isset($name) ? $name : '' ?></div>
                </div>
                <div class="w-100 mb-1">
                    <div class="fs-6"><b>Description:</b></div>
                    <div class="fs-6 ps-4"><?php echo isset($description) ? $description : '' ?></div>
                </div>
                <div class="w-100 mb-1">
                    <div class="fs-6"><b>Price:</b></div>
                    <div class="fs-5 ps-4"><?php echo isset($price) ? number_format($price,2) : '' ?></div>
                </div>
                <div class="w-100 mb-1">
                    <div class="fs-6"><b>Status:</b></div>
                    <div class="fs-5 ps-4">
                        <?php 
                            if(isset($status) && $status == 1){
                                echo "<small><span class='badge rounded-pill bg-success'>Active</span></small>";
                            }else{
                                echo "<small><span class='badge rounded-pill bg-danger'>Inactive</span></small>";
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="product-image">
                <img src="<?php echo $image_path; ?>" alt="Product Showcase Image">
            </div>
        </div>
        <div class="w-100 d-flex justify-content-end">
            <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<?php
    } else {
        echo "Product not found."; 
    }
}
?>
