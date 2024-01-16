<?php

    namespace namche;

    require_once dirname(__FILE__) . '/Bootstrap.class.php';
   

    use namche\Bootstrap;
    use namche\lib\PDODatabase;
    use namche\lib\Session;
    use namche\lib\Item;
    use namche\lib\Cart;

    $db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);
    $session = new Session($db);
    $itm = new Item($db);
    $cart = new Cart($db);

//table settting

    $loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
    $twig = new \Twig_Environment($loader,[
        'cache' => Bootstrap::CACHE_DIR
    ]);

    //session id 設定する

    $session->checkSession();

    //イテムid取得する
    $item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET
    ['item_id']) === 1) ? $_GET['item_id'] : '';

    //if item can not get
    if ($item_id === '') {
    
        header ('Location: ' . Bootstrap::ENTRY_URL . 'list.php');

    }

    $sumNum = '';

    if(!isset($customer_no) || empty($customer_no)){
            $customer_no = $_SESSION['customer_no'];
        }
        list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($customer_no);



    //category list　一覧へに取得する
    $cateArr = $itm->getCategoryList();

    //商品情報取得する
    $itemData = $itm->getItemDetailData($item_id);
    

    $context = [
        'username' =>  isset($_SESSION) &&  isset($_SESSION['username']) ? $_SESSION['username'] : '',
    'sumNum' =>  $sumNum,
    ];
    $context['isUserLogin'] = $session->isUserLogin();
    $context['cateArr'] = $cateArr;
    $context['itemData'] = $itemData[0];
    $template = $twig->loadTemplate('detail.html.twig');
    $template->display($context);
