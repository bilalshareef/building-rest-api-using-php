Source code of simple REST API used in technical article on how to build REST API using PHP. To read the article, see below or [click here](https://bilalshareef.github.io/building-rest-api-using-php/).

# Building REST API Using PHP
In today's world, different applications on different devices are connected with one another and the main reason behind it is APIs. Before looking into REST API, let's see what is an API first. You might already be knowing what an API is. Since this post deals about REST API, let's see a basic intro about API.

API stands for Application Programming Interface and the idea behind API is to connect different applications irrespective of their platforms to share information. Generally, API takes requests from different applications, processes it and gives back the response.

You can use APIs to do anyone of the following.

**a. Build an API to let third party applications to connect with your application.**
![](images/image-1.jpg?raw=true)

**b. Consume a third party API to connect and use their information.**
![](images/image-2.jpg?raw=true)

**c. Build an API to connect your own applications like your website and mobile app.**
![](images/image-3.jpg?raw=true)

## What is REST API?

REST stands for Representational State Transfer and it means that the request and the response should contain a representation of the information i.e., should be in a certain format. So basically, the requests must use proper HTTP methods and the response must be in a proper format like JSON or XML instead of plain text.

REST API is nothing but a normal API with a set of principles. We need to follow a set of rules while creating and consuming REST API. The rules include the following.

1. Use appropriate HTTP methods while performing API calls. The following are the four primary HTTP methods which should be used to send and receive API requests.

    <ol type="a">
        <li>GET - To read single or multiple records.</li>
        <li>POST - To create a new record.</li>
        <li>PUT - To Update a record.</li>
        <li>DELETE - To delete a record.</li>
    </ol>

2. Use proper URL hierarchy instead of using URL query string for API URLs.

    <ol type="a">
        <li>Good - http://example.com/api/products/1</li>
        <li>Bad - http://example.com/api/products.php?id=1</li>
    </ol>

3. Avoid using verbs as resource names in the API URL and use nouns and proper HTTP methods instead.

    <ol type="a">
        <li>Good - http://example.com/api/products</li>
        <li>Bad - http://example.com/api/products/add</li>
    </ol>

4. Use plurals for the resource names in the API URL.

    <ol type="a">
        <li>Good - http://example.com/api/products</li>
        <li>Bad - http://example.com/api/product</li>
    </ol>

5. Use HTTP response codes to indicate status of the requests.
6. Response data should be in either JSON or XML format.

## HTTP Client Libraries(cURL)

Most of you will already know what HTTP is. HTTP stands for Hyper Text Transfer Protocol and it is the protocol that allows us to send information back and forth on the web. Whenever we make a HTTP request we use one of the HTTP methods (GET, POST, PUT, DELETE, etc).

So in order to use REST APIs, we need a client which has the capability to use all the HTTP methods. Unfortunately, HTML is limited in this case. HTML can only send GET and POST requests which is not enough to use REST APIs.

So we need a HTTP client library and that's where cURL comes into place. cURL is the most widely used and most popular HTTP client library among PHP developers. We will use cURL while consuming REST APIs. So we will see it in a bit.

## Building a REST API using PHP

Let's build a simple REST API in PHP with what we have seen so far. The final source code of the REST API which we will build is available on [Github](https://github.com/bilalshareef/building-rest-api-using-php).

Consider, you have an online product catalogue and you want your website and mobile application share the same information about the products. So let's build an API which allows to add, update, read and delete products.

Let's consider that `example.com` is your domain name and `example.com/api/` is the location of the API which we are going to build. We need to add a PHP file(`products.php`) to that `/api/` folder. The following table illustrates the URLs and HTTP methods which should be used to perform appropriate actions with our API.


| HTTP Method | URL             | Action                                      |
| ----------- | --------------- | ------------------------------------------- |
| GET         | /api/products   | Retrieves all products                      |
| GET         | /api/products/5 | Retrieves a single product of primary key 5 |
| POST        | /api/products   | Adds a new product                          |
| PUT         | /api/products/3 | Updates a single product of primary key 3   |
| DELETE      | /api/products/7 | Deletes a single product of primary key 7   |

The PHP file(`products.php`) is where we will put all our API code. Also note that we need to rewrite the URL in order to follow the REST rules. So add a `.htaccess` file to `/api/` folder and put the following in it. So `/api/products.php?product_id=5` becomes `/api/products/5`.

```
RewriteEngine On # Turn on the rewriting engine
RewriteRule ^products/?$ products.php [NC,L]
RewriteRule ^products/([0-9]+)/?$ products.php?product_id=$1 [NC,L]
```

Since URL rewriting itself is a big topic, we will not be able to discuss it here. If you are interested in URL rewriting rules, then I would recommend to check out the article in the following URL.

https://www.addedbytes.com/articles/for-beginners/url-rewriting-for-beginners/

Let's start by putting the following code to identify the HTTP request method.

```php
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
```

In the above code, we first connect to the database where we will store all the products information. Then we use PHP super global variable `$_SERVER` to get the HTTP request method used by the API call. We use a switch case block to perform appropriate action.

To retrieve the products, we use the following `get_products()` function. If a single product is to be retrieved, then we pass the product id to this function. If product id is not passed, then this function retrieves all the products.

```php
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
```

To insert a new product, we use the following `insert_product()` function. Since HTTP POST method will be used to make API calls to insert products, we get the details of the new product from the `$_POST` variable itself.

```php
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
```

To update a product, we use the following `update_product()` function. Since PHP does not have `$_PUT` variable similar to `$_GET` and `$_POST` to fetch the values passed, we use the input stream to get those values to update a product. We will see how to pass values through input stream while we consume the API.


```php
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
```

To delete a product, we use the following `delete_product()` function. We get the product id of the product to be deleted from the `$_GET` variable.

```php
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
```

If you look at all the above functions, then you will notice that we have used JSON to format the output data. So let's put all the code we have discussed together and the final `products.php` file will have the following code.

```php
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
```

## Consuming a REST API using PHP

So far we have built the API and now let's see how to consume it. As I told earlier, we will be using cURL here to consume the API. There are built in functions for cURL in PHP and the following are the functions we will be using.

<ol type="a">
    <li>Establish a connection - curl_init()</li>
    <li>Add request data - curl_setopt()</li>
    <li>Send the request - curl_exec()</li>
    <li>Close the connection - curl_close()</li>
</ol>

The following code is used to get all the products. We pass the API URL to `curl_init()` function to establish connection with the server and store the connection handle in `$ch` variable. Here, we set two options using `curl_setopt()` function. `CURLOPT_HTTPGET` is used to denote that the HTTP request method is GET and `CURLOPT_RETURNTRANSFER` is used to denote that the response must return the value instead of outputting it out directly.

Then the request is sent using the `curl_exec()` function and store the response in `$response_json` variable. Finally, we close the connection using `curl_close()`. Since the response will be a JSON string, we need to decode the string to convert it to a PHP array.

```php
$url = 'http://example.com/api/products';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response_json = curl_exec($ch);
curl_close($ch);
$response = json_decode($response_json, true);
```

The following code is used to get a single product and it is very similar to the code to get all the products. We are passing the value 5 to retrieve the product with the primary key 5.

```php
$url = 'http://example.com/api/products/5';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response_json = curl_exec($ch);
curl_close($ch);
$response = json_decode($response_json, true);
```

The following code is used to add a new product. This time we have added two new cURL options. `CURLOPT_POST` is used to denote that the HTTP request method is POST and `CURLOPT_POSTFIELDS` is used to attach the POST data.

```php
$data = array(
    'product_name' => 'Television',
    'price' => 1000,
    'quantity' => 10,
    'seller' => 'XYZ Traders'
);
$url = 'http://example.com/api/products';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response_json = curl_exec($ch);
curl_close($ch);
$response = json_decode($response_json, true);
```

The following code is used to update a product. I have used `CURLOPT_CUSTOMREQUEST` to denote that the HTTP request method is PUT. Since there is no specific constant to attach PUT data using `curl_setopt()` function, we are using `CURLOPT_POSTFIELDS` which we used in POST request. But this time, we will not pass the data as an array. Instead, we will pass it as a query string using `http_build_query()` function. This API call will update the product with primary key 3.

```php
$data = array(
    'product_name' => 'Laptop',
    'price' => 1200,
    'quantity' => 15,
    'seller' => 'ABC Trading Inc.'
);
$url = 'http://example.com/api/products/3';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response_json = curl_exec($ch);
curl_close($ch);
$response = json_decode($response_json, true);
```

The following code is used to delete a product. As you can see, I have used `CURLOPT_CUSTOMREQUEST` to denote the DELETE HTTP request method and this API call will delete the product with primary key 7.

```php
$url = 'http://example.com/api/products/7';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response_json = curl_exec($ch);
curl_close($ch);
$response = json_decode($response_json, true);
```

## Summary

Of course, there is lot more to REST APIs than what we have seen here. But the idea behind this article is to give you a basic but strong foundation on REST APIs. I hope that you now have a clear understanding of what REST API is and how to build it yourself.

Happy Coding.

## Author

- [Mohammed Bilal Shareef](https://bilalshareef.github.io/)
