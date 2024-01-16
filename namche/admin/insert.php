<?php

require_once dirname(__FILE__) . '..\\../Bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;
use namche\lib\Crud;

$db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);

$ses = new Session($db);
$crud = new Crud($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    

    try {
        // Retrieve form data
        $itemName = $_POST["name"];
        $category = $_POST["item"];
        $price = $_POST['price'];
        $description = $_POST["contents"];
        $image = $_FILES["image"]["name"]; 
//
        $uploadDir = Bootstrap::IMAGE_DIR;;

      
        $uploadPath = $uploadDir . $image;

        // Check image size
        if ($_FILES["image"]["size"] >524288) { 
            echo "Image size exceeds the allowed limit.";
            exit;
        }

        // Check image type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; // Add more allowed types if needed
        $imageType = $_FILES["image"]["type"];
        if (!in_array($imageType, $allowedTypes)) {
            echo "Invalid image type. Please upload a JPEG, PNG, or GIF image.";
            exit;
        }

        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadPath)) {
            
            //アイテムクラスにデータを代入する
            $itemManager = new Crud($db);
            $inserted = $itemManager->insertItem($itemName, $description, $price, $image, $category);

            if ($inserted) {
                echo '<script>alert("Data inserted successfully!");</script>';
                header("Location: crud.php");
            } else {
                
                echo "データ入力失敗しました！";
            }
        } else {
            
            echo "Image upload failed.";
        }
    } catch (\PDOException $e) {
        //  database connection errors
        echo "データベース接続失敗しました！: " . $e->getMessage();
    }
} else {
    //  render the Twig template
    echo $twig->render('insert.html.twig');
}
