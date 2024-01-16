<?php

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use namche\Bootstrap;
use namche\lib\PDODatabase;
use namche\lib\Session;
use namche\lib\Crud;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);

$ses = new Session($db);
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR,
]);

$users = new Crud($db);

$userdata = '';
function userdata($db) {
    $data = array();
    $table = 'signup';
    $columns = ['id', 'username', 'email', 'tel1', 'tel2', 'tel3', 'address','delete_flg'];
    $sql = "SELECT " . implode(', ', $columns) . " FROM $table";

    // Execute the query and fetch the result
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $data[] = $row;
    }
    return $data;
}

// for delete users data

$item_id = (isset($_GET['id']) === true && preg_match('/^\d+$/', $_GET['id']) === 1) ? $_GET['id'] : '';
$toggleDeactivate = (isset($_GET['action']) && $_GET['action'] == 'toggleDeactivate') ? 1 : '';

$pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';
$email = (isset($_GET['email']) === true && preg_match( $pattern, $_GET['email']) === 1) ? $_GET['email'] : '';
$status = (isset($_GET['status']) === true && preg_match('/^\d+$/', $_GET['status']) === 1) ? $_GET['status'] : ''; 


if($toggleDeactivate && $email){
    $db->UserToggleDeactive(
        $email,
        $status
    );
    header('location: users_list.php?deactivate_success=true');
    exit();
}



if ($item_id !== '') {
    $users->deleteUsers($item_id);  // Update variable to $item_id
  
    header('location: users_list.php?delete_success=true');
    exit();
}


$data = userdata($db); 


// here i want to import data form csv

$db->insertCSV();








$context = [
    'usersdata'=>$data
];

$template = $twig->loadTemplate('users_list.html.twig');

$template->display($context);

?>
