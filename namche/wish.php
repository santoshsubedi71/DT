<?php

    namespace namche;

    require_once dirname(__FILE__) . '/Bootstrap.class.php';
   

    use namche\Bootstrap;
    use namche\lib\PDODatabase;
    use namche\lib\Session;
    use namche\lib\Wish;


    $db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);
    // $session = new Session($db);
    $ses = new Session($db);
    $wish = new Wish($db);
//table settting

    $loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
    $twig = new \Twig_Environment($loader,[
        'cache' => Bootstrap::CACHE_DIR
    ]);

    $ses->checkSession();
    $customer_no = $_SESSION['customer_no'];

    $item_id = (isset($_REQUEST['item_id']) === true && preg_match('/^\d+$/', $_REQUEST
    ['item_id']) === 1) ? $_REQUEST['item_id'] : '';

    $remove_item_id = (isset($_GET['remove_item_id']) === true && preg_match('/^\d+$/', $_GET
    ['remove_item_id']) === 1) ? $_GET['remove_item_id'] : '';

   
    if ($item_id !== '') {
        $res = $wish->insData($customer_no, $item_id);
    if ($res === false) {
        echo "商品購入に失敗しました。";
        exit();
        }
    }
   
    if ($remove_item_id !== '') {
        $res = $wish->deleteWish($remove_item_id);
    if ($res === false) {
        echo "商品購入に失敗しました。";
        exit();
        }
    }


    
    $dataArr = $wish->getWishData($customer_no);

    list($wishSum, $wishPrice)=$wish->getwishsum($customer_no);


   
    
    $context = [
        'dataArr' => $dataArr
    ];

    $context['sumPrice'] = $wishPrice;
    $context['wishSum'] = $wishSum;
   
 
    $template = $twig->loadTemplate('wish_list.html.twig');
    $template->display($context);
    