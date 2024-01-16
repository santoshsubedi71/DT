<?php


require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;
use namche\lib\Crud;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);


$ses = new Session($db);
$allItem = new Crud($db);
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR,
]);

if(!$ses->isAdminLogin()){
    header('Location: index.php');
    exit;
}

// this is insert section , here we make data insert for database

$getItemById = isset($_GET['getItemById']) ?  intval($_GET['getItemById']) : '';
if($getItemById>0){
    $currentItem = $allItem->getDataByID($getItemById);
    echo json_encode($currentItem);
    exit;
}


if (isset($_POST['u_item_id']) && isset($_POST['submit']))  {
    try {
        // Retrieve form data
        $itemName = $_POST["name"];
        $item_id = $_POST["u_item_id"];
        $price = $_POST['price'];
        $ctg_id = $_POST["u_ctg_id"];
        $subcategory_id = $_POST["Usubcategory_id"];
        $description = $_POST["contents"];
        $image = $_FILES["image"]["name"]; 
//
        $uploadDir = Bootstrap::IMAGE_DIR;;

        if($image){
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
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadPath)) { } else {
            
                echo "Image upload failed.";
            }
        }

        
        
            
        //アイテムクラスにデータを代入する
          
            $inserted = $allItem->update($item_id, $itemName, $description, $price, $image, $ctg_id, $subcategory_id);

            if ($inserted) {
               
                header('location:crud.php');
                echo '<script>alert("Data update successfully!") ;</script>';
                exit;
            
            } else {
                
                echo "データ入力失敗しました！";
            }
       
    } catch (\PDOException $e) {
        //  database connection errors
        echo "データベース接続失敗しました！: " . $e->getMessage();
    }
exit;
}

// here is code for add item product


if (isset($_POST['addproduct']))  {

     try {
        // Retrieve form data
        $itemName = $_POST["name"];
        $category = $_POST["item"];
        $subcategory_id = $_POST["subcategory_id"];
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
            
            // //アイテムクラスにデータを代入する
          
            $inserted = $allItem->insertItem($itemName, $description, $price, $image, $category, $subcategory_id);

            if ($inserted) {
               
                header('location:crud.php');
                echo '<script>alert("Data inserted successfully!") ;</script>';
                exit;
            
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
   
}

// this is function for show items in the main admin page

$showItems = $allItem->read();
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Number of items to display per page
$perPage = 10;

// Retrieve items for the current page
$showItems = $allItem->read($page, $perPage);

// Calculate total pages
$totalPages = ceil($allItem->getTotalItems() / $perPage);






// this script is for delete data from admin pannel

    $item_id = (isset($_GET['item_id'])===true && preg_match('/^\d+$/', $_GET
    ['item_id']) === 1) ? $_GET['item_id'] : '';


    if ($item_id !== '') {
        $res = $allItem->deleteItem($item_id);
        header('location: crud.php?delete_success=true');
        exit();
    }
    

    // here i want to make update 
$childCategory = [];
$childCategory = $db->getSubCategory();


$context = [
    'allItems' => $showItems,
    'category' =>  $db->getParentCategory(),
    'childCategory' => $childCategory,
    'totalPages' => $totalPages,
    
];


$template = $twig->loadTemplate('crud.html.twig');
$template->display($context);
