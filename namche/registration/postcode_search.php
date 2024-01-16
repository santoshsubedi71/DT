<?php

namespace namche\registration;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';


use namche\Bootstrap;
use namche\lib\PDODatabase;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);

if (isset($_GET['zip1']) && isset($_GET['zip2'])){

    $zip1 = trim($_GET['zip1']);
    $zip2 = trim($_GET['zip2']);

    $res = $db->selectPostalCode($zip1.$zip2);
    //

    echo ($res) ? $res['pref']. $res['city'].$res['town']: false;

}else{
        echo false;
}






?>