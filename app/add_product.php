<?php
    include_once('config.php');
    
    if(isset($_POST["submit"])) {
        $url = $api_url . 'products';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_json = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response_json, true);

        if($response['status'] == '1') {
            header("Location: index.php?add_status=success");
        } elseif($response_json['status'] == 0) {
            header("Location: index.php?add_status=failed");
        }
    }
?>
<!doctype html>
<html>
    <head>
        <title>REST API</title>
    </head>
    <body>
        <div>
            <a href="index.php">All Products</a>
        </div>
        <h1>Add Product</h1>
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                    <td><label>Product Name</label></td>
                    <td><input type="text" name="product_name"></td>
                </tr>
                <tr>
                    <td><label>Price</label></td>
                    <td><input type="text" name="price"></td>
                </tr>
                <tr>
                    <td><label>Quantity</label></td>
                    <td><input type="text" name="quantity"></td>
                </tr>
                <tr>
                    <td><label>Seller</label></td>
                    <td><input type="text" name="seller"></td>
                </tr>
            </table>
            <div>
                <input type="submit" name="submit" value="Add Product">
                <input type="button" name="cancel" value="Cancel" onclick="window.location='index.php';">
            </div>
        </form>
    </body>
</html>