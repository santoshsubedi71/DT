<?php

namespace namche\registration;
use namche\lib\PDODatabase;
use namche\Bootstrap;

class errorClass
{
    private $dataArr = [];
    private $errArr = [];
    private $db;


    
    public function __construct()
    {
        $this->db = new PDOdatabase(Bootstrap::DB_HOST, Bootstrap::DB_NAME, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_TYPE);
    }

    public function errorCheck($dataArr)
    {
        $this->dataArr = $dataArr;
        //
        $this->createErrorMessage();

        $this->username();
        // print_r($this->errArr);
        $this->mailCheck();
        $this->mailExists();
        $this->password();
        $this->telCheck();
        $this->zipcheck();
        $this->address();

        return $this->errArr;

    }

    public function errorCheckLogin($dataArr)
    {
        $this->dataArr = $dataArr;
        //
        $this->createErrorMessage();

        $this->mailCheck();
        $this->loginMailExists();
        $this->isAccountDeactivated();
        $this->loginPassword();  

        return $this->errArr;

    }

    private function createErrorMessage(){
        foreach ($this->dataArr as $key => $val){
            $this->errArr[$key]='';
        }
    }
    private function username(){
        if($this->dataArr['username'] ===''){
            $this->errArr['username']= 'お名前（氏）を入力してください';
        }
    }
    private function mailCheck(){
        $pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';

        if(preg_match( $pattern, $this->dataArr['email'])===0){
            $this->errArr['email']='Please input email in correct format';

        }
    }
    private function mailExists(){
        if( $this->db->email_check($this->dataArr['email'])){
            $this->errArr['error_message']='Error: Email already exists in the database.';

        }
    }
    private function loginMailExists(){
        if($this->dataArr['email'] && !$this->errArr['email'] && !$this->db->email_check($this->dataArr['email'])){
            $this->errArr['error_message']='Error: Email does not exists in the database.';

        }
    }
    private function isAccountDeactivated(){
        if($this->dataArr['email'] && !$this->errArr['email'] && $this->db->deactive_check($this->dataArr['email'])){
            $this->errArr['error_message']='Error: account has been deactivated in the database.';

        }
    }
 

        private function password(){
            if
         (preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{6,15}$/', $this->dataArr['password'])===0){
              $this->errArr['password']='少なくとも1つの大文字,小文字,数字,特殊記号入力して下さい';
            }
        }

        private function loginPassword(){
            if
         ( empty($this->dataArr['password']) ){
              $this->errArr['password']='少なくとも1つの大文字,小文字,数字,特殊記号入力して下さい';
            }
        }
    


        private function telCheck(){
            if(preg_match('/^\d{3}$/', $this->dataArr['tel1'])===0 ||
              preg_match('/^\d{4}$/', $this->dataArr['tel2'])===0 ||
              preg_match('/^\d{4}$/', $this->dataArr['tel3'])===0 ||
              strlen($this->dataArr['tel1'].$this->dataArr['tel2'].
              $this->dataArr['tel3']) >=12){
                $this->errArr['tel1']= '電話番号は、半角数字11桁以内入力してください';
              }
        }


    private function zipcheck(){
        if(preg_match('/^[0-9]{3}$/',$this->dataArr['zip1'])===0){
            $this->errArr['zip1'] = '郵便番号の上は半角数字3桁入力してください';
        }
        if(preg_match('/^[0-9]{4}$/',$this->dataArr['zip2'])===0){
            $this->errArr['zip2']= '郵便番号の下は半角数字4桁入力してください';
        }
    }
    private function address(){
        if($this->dataArr['address']===''){
            $this->errArr['address']= '住所を入力してください';
        }
    }

  

 

    public function getErrorFlg(){
        $err_check=  false;
        foreach($this->errArr as $key => $value){
            if ($value != ''){
                $err_check =true;
            }
        }
        return $err_check;
    }
}
?>