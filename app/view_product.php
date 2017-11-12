<?php
    include_once('config.php');
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
        <h1>Product Details</h1>
        <?php
            $url = $api_url . 'products/' . $_GET["product_id"];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response_json = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response_json, true);
        ?>
        <div>
            <div>Product Name - <?php echo $response[0]["product_name"]; ?></div>
            <div>Price - <?php echo $response[0]["price"]; ?></div>
            <div>Quantity - <?php echo $response[0]["quantity"]; ?></div>
            <div>Seller - <?php echo $response[0]["seller"]; ?></div>
        </div>
    </body>
</html>