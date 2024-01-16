<?php

namespace namche\lib;



class Session
{
    public $session_key = '';
    public $db = NULL;

    public $signup_no = null;

    public function __construct($db)
    {
        //session start
        session_start(); // // crating here aru ka douka 
        //get session id
        $this->session_key = session_id(); //
        //database 
        $this->db = $db;
    }
    public function checkSession()
    {
        //session check
        $customer_no = $this->selectSession();
        //if threre is any history onn this website
        if($customer_no !== false) {
            $_SESSION['customer_no'] = $customer_no;
          

        }else{
            //there is no sesion id first time visit this web 
            $res = $this->insertSession(); //seesion 
            if ($res === true) {
                $_SESSION['customer_no'] = $this->db->getLastId();
                $_SESSION['username'] = '';

            } else {
                $_SESSION['customer_no'] = '';
                $_SESSION['username'] = '';
            }
        }
    }


    
    private function selectSession()// user iru ka douka kakkuni sitemasu
    {
        $table = ' session ';
        $col = ' customer_no '; //login sitneia user ha sonomama
        $where = ' session_key = ? ';
        $arrVal = [$this->session_key];

        $res = $this->db->select($table, $col, $where, $arrVal);
        return (count($res) !== 0) ? $res[0]['customer_no'] : false;

        
    }

    public function getClient(){
        return $this->selectSession();
    }
    private function insertSession()// teburu ni tuika
    {
        $table = ' session ';
        $insData = ['session_key ' => $this->session_key, 'signup_no' => $this->signup_no];
        $res = $this->db->insert($table, $insData);
        return $res;
    }

    private function fillUserInfo($data){
        $_SESSION['username'] = $data['username'];
        $_SESSION['email'] = $data['email'];
    }

    
    public function loginUser($email, $password) 
    {        
        $loginInfo = $this->db->loginInfo($email, $password);
        if($loginInfo){
            $this->signup_no = $loginInfo['id'];
            $this->insertSession();
            $this->selectSession();
            $this->fillUserInfo($loginInfo);
            return true;
        }
        return false;
    }

    private function makeAdminSession(){
        session_start();
        $_SESSION['admin_login'] = true;
    }

    public function loginAdmin($user, $password) 
    {        
        $loginInfo = $this->db->admin($user, $password);
        if($loginInfo){
            $this->makeAdminSession();
            return true;
        }
        return false;
    }

    
    public function isAdminLogin(){
        if(isset($_SESSION) && isset($_SESSION['admin_login']) && !empty($_SESSION['admin_login']) ){
            return true;
        }
        return false;
    }

    public function isUserLogin(){
        if(isset($_SESSION) && isset($_SESSION['username']) && isset($_SESSION['email'])
            && !empty($_SESSION['username'])
            && !empty($_SESSION['email'])
        ){
            return true;
        }
        return false;
    }

    public function logout(){
        $_SESSION['username'] = '';
        $_SESSION['email'] = '';
        unset($_SESSION);
        session_destroy();
        session_unset();
    }
}


