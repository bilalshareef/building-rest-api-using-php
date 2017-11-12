<?php
    include_once('config.php');
    
    $product_id = $_GET["product_id"];
    $url = $api_url . 'products/' . $product_id;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response_json = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response_json, true);

    if($response['status'] == '1') {
        header("Location: index.php?delete_status=success");
    } elseif($response_json['status'] == 0) {
        header("Location: index.php?delete_status=failed");
    }
?>