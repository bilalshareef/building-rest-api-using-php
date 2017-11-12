<?php
    include_once('config.php');
?>
<!doctype html>
<html>
    <head>
        <title>REST API</title>
        <style type="text/css">
            .products_table {
                border-collapse: collapse;
            }
            .products_table th, .products_table td {
                border: 1px solid black;
                padding: 5px;
            }
        </style>
    </head>
    <body>
        <div>
            <a href="add_product.php">Add Product</a>
        </div>
        <?php
            if(isset($_GET["add_status"])) {
                if($_GET["add_status"] == "success") {
                    echo '<div>Product Added Successfully.</div>';
                } elseif($_GET["add_status"] == "failed") {
                    echo '<div>Product Addition Failed.</div>';
                }
            } if(isset($_GET["delete_status"])) {
                if($_GET["delete_status"] == "success") {
                    echo '<div>Product Deleted Successfully.</div>';
                } elseif($_GET["delete_status"] == "failed") {
                    echo '<div>Product Deletion Failed.</div>';
                }
            } if(isset($_GET["edit_status"])) {
                if($_GET["edit_status"] == "success") {
                    echo '<div>Product Updated Successfully.</div>';
                } elseif($_GET["edit_status"] == "failed") {
                    echo '<div>Product Updation Failed.</div>';
                }
            }
        ?>
        <table class="products_table">
            <tr>
                <th>S.No.</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Seller</th>
                <th>Action</th>
            </tr>
            <?php
                $url = $api_url . 'products';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response_json = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response_json, true);
                if(count($response) == 0) {
                    echo '<tr><td colspan="6">No products found.</td></tr>';
                } else {
                    $i = 1;
                    foreach($response as $product) {
                        echo '<tr>';
                        echo '<td>' . $i . '</td>';
                        echo '<td>' . $product["product_name"] . '</td>';
                        echo '<td>' . $product["price"] . '</td>';
                        echo '<td>' . $product["quantity"] . '</td>';
                        echo '<td>' . $product["seller"] . '</td>';
                        echo '<td><a href="view_product.php?product_id=' . $product["id"] . '">View</a><a href="edit_product.php?product_id=' . $product["id"] . '">Edit</a><a href="delete_product.php?product_id=' . $product["id"] . '">Delete</a></td>';
                        echo '</tr>';
                        $i++;
                    }
                }
            ?>
        </table>
    </body>
</html>