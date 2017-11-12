<?php
    include_once('config.php');
    
    // Connect to database
    $connection = mysqli_connect($db_host, $db_username, $db_password, $db_name);

    $request_method = $_SERVER["REQUEST_METHOD"];
    switch($request_method) {
        case 'GET':
            // Retrive Products
            if(!empty($_GET["product_id"])) {
                $product_id = intval($_GET["product_id"]);
                get_products($product_id);
            } else {
                get_products();
            }
            break;
        case 'POST':
            // Insert Product
            insert_product();
            break;
        case 'PUT':
            // Update Product
            $product_id = intval($_GET["product_id"]);
            update_product($product_id);
            break;
        case 'DELETE':
            // Delete Product
            $product_id = intval($_GET["product_id"]);
            delete_product($product_id);
            break;
        default:
            // Invalid Request Method
            header("HTTP/1.0 405 Method Not Allowed");
            break;
    }

    function insert_product() {
        global $connection;
        $product_name = $_POST["product_name"];
        $price = $_POST["price"];
        $quantity = $_POST["quantity"];
        $seller = $_POST["seller"];
        $query = "INSERT INTO products SET product_name='{$product_name}', price={$price}, quantity={$quantity}, seller='{$seller}'";
        if(mysqli_query($connection, $query)) {
            $response = array(
                'status' => 1,
                'status_message' => 'Product Added Successfully.'
            );
        } else {
            $response=array(
                'status' => 0,
                'status_message' => 'Product Addition Failed.'
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function get_products($product_id=0) {
        global $connection;
        $query = "SELECT * FROM products";
        if($product_id != 0) {
            $query .= " WHERE id=" . $product_id . " LIMIT 1";
        }
        $response = array();
        $result = mysqli_query($connection, $query);
        while($row = mysqli_fetch_array($result)) {
            $response[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function delete_product($product_id) {
        global $connection;
        $query = "DELETE FROM products WHERE id=" . $product_id;
        if(mysqli_query($connection, $query)) {
            $response=array(
                'status' => 1,
                'status_message' => 'Product Deleted Successfully.'
            );
        } else {
            $response=array(
                'status' => 0,
                'status_message' => 'Product Deletion Failed.'
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    function update_product($product_id) {
        global $connection;
        parse_str(file_get_contents("php://input"), $post_vars);
        $product_name = $post_vars["product_name"];
        $price = $post_vars["price"];
        $quantity = $post_vars["quantity"];
        $seller = $post_vars["seller"];
        $query = "UPDATE products SET product_name='{$product_name}', price={$price}, quantity={$quantity}, seller='{$seller}' WHERE id=".$product_id;
        if(mysqli_query($connection, $query)) {
            $response=array(
                'status' => 1,
                'status_message' => 'Product Updated Successfully.'
            );
        } else {
            $response=array(
                'status' => 0,
                'status_message' => 'Product Updation Failed.'
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Close database connection
    mysqli_close($connection);
?>