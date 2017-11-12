<?php
    include_once('config.php');
    
    if(isset($_POST["submit"])) {
        $product_id = $_POST["id"];
        $url = $api_url . 'products/' . $product_id;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_json = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response_json, true);

        if($response['status'] == '1') {
            header("Location: index.php?edit_status=success");
        } elseif($response_json['status'] == 0) {
            header("Location: index.php?edit_status=failed");
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
        <h1>Edit Product</h1>
        <?php
            $url = $api_url . 'products/'.$_GET["product_id"];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response_json = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response_json, true);
        ?>
        <form action="edit_product.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $response[0]["id"]; ?>" >
            <table>
                <tr>
                    <td><label>Product Name</label></td>
                    <td><input type="text" name="product_name" value="<?php echo $response[0]["product_name"]; ?>" ></td>
                </tr>
                <tr>
                    <td><label>Price</label></td>
                    <td><input type="text" name="price" value="<?php echo $response[0]["price"]; ?>" ></td>
                </tr>
                <tr>
                    <td><label>Quantity</label></td>
                    <td><input type="text" name="quantity" value="<?php echo $response[0]["quantity"]; ?>" ></td>
                </tr>
                <tr>
                    <td><label>Seller</label></td>
                    <td><input type="text" name="seller" value="<?php echo $response[0]["seller"]; ?>" ></td>
                </tr>
            </table>
            <div>
                <input type="submit" name="submit" value="Update Product">
                <input type="button" name="cancel" value="Cancel" onclick="window.location='index.php';">
            </div>
        </form>
    </body>
</html>