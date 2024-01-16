<?php

namespace namche\lib;

class PDOdatabase
{
       
    private $dbh = null;
    private $db_host = '';
    private $db_name = '';
    private $db_user = '';
    private $db_pass = ''; 
    private $db_type = '';
    private $order = '';
  

    private $limit = '';
    private $offset = '';
    private $groupby = '';


 

    public function __construct($db_host, $db_name, $db_user, $db_pass, $db_type)
    {
        $this->dbh = $this->connectDB($db_host,$db_name,$db_user, $db_pass, $db_type);
        $this->db_host = $db_host;
        $this->db_name = $db_name;
        $this->db_user=  $db_user;
        $this->db_pass = $db_pass;

        $this->order = '';
        
        $this->limit = '';
        $this->offset = '';
        $this->groupby = '';
     
    }   

       
    private function connectDB($db_host, $db_name, $db_user, $db_pass, $db_type)
    {
        try {
            switch ($db_type) {
                    case 'mysql':
                        $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name;
                        $dbh = new \PDO($dsn, $db_user, $db_pass);
                        $dbh->query('SET NAMES utf8');
                        break;
    
                    case 'pgsql':
                        $dsn = 'pgsql:dbname=' . $db_name . ' host=' . $db_host . ' port=3306';
                        $dbh = new \PDO($dsn, $db_user, $db_pass);
                        break; 
            }
                
                
        } catch (\PDOException $e) {
                var_dump($e->getMessage());
                exit();
        }
        return $dbh;  
            
    }
        
            public function setQuery($query = '', $arrVal = [])
            {
                $stmt = $this->dbh->prepare($query);
                $stmt->execute($arrVal);
            }
    
        
            // public function select($table, $column ='', $where = '', $arrVal = [])
            // {
            //     $sql = $this->getSql('select', $table, $where, $column);
            //     $this->sqlLogInfo($sql, $arrVal);
            //     $stmt = $this->dbh->prepare($sql);
    
            //     $res = $stmt->execute($arrVal);
            //     if ($res === false){
            //     $this->catchError($stmt->errorInfo());
            //     }
            //     //
            //     $data = [];
            //     while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            //         array_push($data, $result);
            //     }
                
            //     return $data;
            // }

            public function select($table, $column = '', $where = '', $arrVal = [], $limit = '')
            {
                $sql = $this->getSql('select', $table, $where, $column);
                
                if ($limit !== '') {
                    $sql .= " $limit";
                }

                $this->sqlLogInfo($sql, $arrVal);
                $stmt = $this->dbh->prepare($sql);

                $res = $stmt->execute($arrVal);

                if ($res === false) {
                    $this->catchError($stmt->errorInfo());
                }

                $data = [];
                while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    array_push($data, $result);
                }

                return $data;
            }


            // fetch zip code from database
            public function selectPostalCode($zip){
                
                $sql = "SELECT pref, city, town FROM postcode WHERE zip = :zip LIMIT 1";
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(":zip", $zip);
                $stmt->execute();
                
                $data = $stmt->fetch(\PDO::FETCH_ASSOC);

            
                
                return $data;
               print_r($data);
            }
            // count table here

            public function count($table, $where = '', $arrVal = [])
            {
                $sql = $this->getSql('count', $table, $where);
    
                $this->sqlLogInfo($sql, $arrVal);
                $stmt = $this->dbh->prepare($sql);
    
                $res = $stmt->execute($arrVal);
                
                if ($res === false){
                    $this->catchError($stmt->errorInfo());
                }
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    
                return intval($result['NUM']);
    
            }
            public function setOrder($order = '')
            {
                if($order !== '' ){
                    $this->order= ' ORDER BY '. $order;
                }
            }
            public function setLimitOff($limit = '', $offset = '' )
            {
                if ($limit !== ""){
                    $this->limit = " LIMIT " . $limit;
                }
                if ($offset !== ""){
                    $this->offset = " OFFSET ". $offset;
                }
            }
            public function setGroupBy($groupby)
            {
                if($groupby !== ""){
                    $this->groupby = ' GROUP BY '. $groupby;
                }
            }
            private function getSql($type, $table, $where = '', $column = '')
            {
                switch ($type) {
                    case 'select':
                        $columnKey = ($column !== '') ? $column : "*";
                        break;
    
                    case 'count':
                    $columnKey = 'COUNT(*) AS NUM';
                    break;
    
                    default:
                    break;
                }
            $whereSQL = ($where !== '') ? ' WHERE' . $where : '';
            $other = $this->groupby . "" . $this->order . " "  . $this->limit . " " . $this->offset;
           
            $sql = " SELECT " . $columnKey . " FROM " . $table . $whereSQL . $other;
    
            
            return $sql;
            }
            // insert data in table
            public function insert($table, $insData =[])
            {
            
                $insDataKey = [];
                $insDataVal = [];
                $preCnt = [];
    
                $columns = '';
                $preSt = '';
    
                foreach ($insData as $col => $val){
                $insDataKey[] = $col;
                $insDataVal[] = $val;
                $preCnt[] = '?';
                }
            $columns = implode(",", $insDataKey);
            $preSt = implode(",", $preCnt);
    
            $sql = " INSERT INTO "
                . $table
                ." ("
                .$columns
                .") VALUES ("
                . $preSt
                .") ";
    
            $this->sqlLogInfo($sql, $insDataVal);
    
            $stmt = $this->dbh->prepare($sql);
            $res = $stmt->execute($insDataVal);
    
            if ($res === false) {
                $this->catchError($stmt->errorInfo());
            }
            return $res;
        }
        public function update($table, $insData = [], $where ='', $arrWhereVal = [])
        {
            $arrPreSt = [];
            foreach ($insData as $col => $val) {
                 $arrPreSt[] = $col . " =? ";
        
            }
            $preSt = implode(',', $arrPreSt);
            //
            $sql = " UPDATE "
            . $table 
            . " SET "
            . $preSt 
            . "WHERE "
            . $where;
        
            $updateData = array_merge(array_values($insData), $arrWhereVal);
            $this->sqlLogInfo($sql, $updateData);
    
            $stmt = $this->dbh->prepare($sql);
            $res = $stmt->execute($updateData);
            if ($res === false) {
                $this->catchError($stmt->errorInfo());
    
            }
            return $res;
    
        }
        // this is prepare for  query 
        public function prepare($query)
        {
            return $this->dbh->prepare($query);
        }
        
    
        public function getLastId()
        {
            return $this->dbh->lastInsertId();
        }
        private function catchError($errArr =[])
        {
            $errMsg = (!empty($errArr[2])) ? $errArr[2]:"";
            die("SQL エラ発生しました。" . $errArr[2]);
    
        }
        
    
        // make log file 
        private function makeLogFile()
        {
            $logDir = dirname(__DIR__) . "/logs";
            if (!file_exists($logDir)) {
                mkdir($logDir, 0777); 
            
            }
            $logPath = $logDir . '/namche.log';
            if (!file_exists($logPath)) {
                touch($logPath);
            }
            return $logPath;
        }
    
            private function sqlLogInfo($str, $arrVal = [])
            {
                $logPath = $this->makeLogFile();
                $logData = sprintf("[SQL_LOG:%s]: %s [%s]\n", date('Y-m-d H:i:s'), $str, implode(",", $arrVal));
                error_log($logData, 3, $logPath);
            }
        
 
        // this is login function for admin

        public function admin($username, $password){

          
            $sql = "SELECT password FROM admin WHERE username = :username and password = :password";

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, \PDO::PARAM_STR);
            $stmt->execute();

            $column = $stmt->fetchColumn();

            // Verify the password
            if ($column) {
                return true; // Passwords match, login successful
            } else {
                return false; // Passwords do not match, login failed
            }
        }

        public function quote($int){
            return mysqli_real_escape_string($this->dbh, $int);
        }
        public function str_quote($str){
    
            return "'". mysqli_real_escape_string($this->dbh,$str). "'";
        }

        
    //   function for email register already in database or not

                        public function email_check($email)
                        {
                            $table = 'signup';
                            $columns = ['email'];
                            $sql = "SELECT " . implode(', ', $columns) . " FROM $table WHERE email = :email";
                        
                            $stmt = $this->dbh->prepare($sql);
                            $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
                            $stmt->execute();
                            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        
                            return count($result) > 0;
                        }

                        public function deactive_check($email)
                        {
                            $table = 'signup';
                            $columns = ['delete_flg'];
                            $sql = "SELECT " . implode(', ', $columns) . " FROM $table WHERE email = :email and delete_flg=1";
                        
                            $stmt = $this->dbh->prepare($sql);
                            $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
                            $stmt->execute();
                            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        
                            return count($result) > 0;
                        }
                        
                    
         public function insertUser($username, $email, $password, $tel1, $tel2, $tel3, $zip1, $zip2, $address)
                        {
                            // Hash the password before storing it
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        
                            $table = 'signup';
                            $columns = ['username', 'email', 'password', 'tel1', 'tel2', 'tel3', 'zip1', 'zip2', 'address'];
                            $values = [$username, $email, $hashedPassword, $tel1, $tel2, $tel3, $zip1, $zip2, $address];
                        
                          
                            $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            
                            // echo $sql;
                            try {
                                
                                $stmt = $this->dbh->prepare($sql);
                                $stmt->execute($values);
                        
                                return true;
                            } catch (\Exception $e) {
                                echo "Error inserting data: " . $e->getMessage();
                                return false;
                            }
                        }
                        


                        public function loginInfo($email, $password) 
                        {
                            
                            $query = "SELECT * FROM signup WHERE email = ?";
                            $stmt = $this->dbh->prepare($query);
                            $stmt->execute([$email]);
                           
                            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                            if (password_verify($password, $user['password'])) {

                                return $user;
                            }
                            
                            return false;

                    }


                    // category start
                    
                    public function getParentCategory() 
                    {
                        
                        $query = "SELECT * FROM category WHERE parent_id is null order by category_name asc";
                        $stmt = $this->dbh->prepare($query);
                        $stmt->execute([]);
                        $cat = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        return $cat;

                    }
                    public function getSubCategory() 
                    {
                        
                        $query = "SELECT * FROM category WHERE parent_id is not null order by category_name asc";
                        $stmt = $this->dbh->prepare($query);
                        $stmt->execute([]);
                        $cat = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        return $cat;

                    }
                    public function getCategory($parent_id = null) 
                    {
                        
                        $query = "SELECT * FROM category WHERE parent_id = ? order by category_name asc";
                        $stmt = $this->dbh->prepare($query);
                        $stmt->execute([$parent_id]);
                        $cat = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        // print_r(  $cat);
                        return $cat;

                    }
                    public function getSingleCategory($ctg_id = null) 
                    {
                        
                        $query = "SELECT * FROM category WHERE ctg_id = ? order by category_name asc";
                        $stmt = $this->dbh->prepare($query);
                        $stmt->execute([$ctg_id]);
                        $cat = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        // print_r(  $cat);
                        return $cat;

                    }

                    
                    public function getAllCategory() 
                    {
                        
                        $query = "SELECT * FROM category  order by category_name,parent_id asc";
                        $stmt = $this->dbh->prepare($query);
                        $stmt->execute([]);
                        $cat = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        return $cat;
                    }

                    // end category

                    public function getAdminOrders() 
                    {
                        
                        $query = "SELECT * FROM orders order by created_at desc";
                        $stmt = $this->dbh->prepare($query);
                        $stmt->execute();
                       
                        $info = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        
                        return $info;

                }

                

                public function getAdminOrdersByDate($fdate, $tdate) 
                {
                    
                    $query = "SELECT * FROM orders
                    where created_at >= ? and created_at <= ?
                     order by created_at desc";
                    $stmt = $this->dbh->prepare($query);
                    $stmt->execute([
                        $fdate,
                        $tdate
                    ]);
                   
                    $info = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                    // print_r($info);
                    
                    return $info;

            }

                

            public function getAdminOrdersStatusAmountByDate($fdate, $tdate) 
            {
                
                $query = "SELECT sum(total) as total, status FROM orders
                where created_at >= ? and created_at <= ?
                group by status
                 order by created_at desc";
                $stmt = $this->dbh->prepare($query);
                $stmt->execute([
                    $fdate,
                    $tdate
                ]);
               
                $info = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                // print_r($info);
                
                return $info;

        }


        // here we fetch profile and edit function form database 

    

        public function getLoginUserProfile(){
            $email = $_SESSION['email'];

            $query = "SELECT * FROM signup WHERE email = ?";
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([$email]);
           
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $user;
        }

        public function updateUserPassword($userId, $hashedNewPassword) {
            $query = "UPDATE signup SET password = ? WHERE id = ?";
            $stmt = $this->dbh->prepare($query);
            $stmt->execute([$hashedNewPassword, $userId]);
        
            return $stmt->rowCount() > 0;
        }
        

        public function insertCSV(){
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Check if a file is uploaded
                if (isset($_FILES["csv_file"]) && $_FILES["csv_file"]["error"] == UPLOAD_ERR_OK) {
                    // Define the path where the file will be saved
                    $uploadDir = './uploads/';
                    $uploadFile = $uploadDir . basename($_FILES["csv_file"]["name"]);
            
                    // Move the uploaded file to the specified directory
                    if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $uploadFile)) {
                        echo "File is valid, and was successfully uploaded.\n";
            
                        // Process the CSV file
                        if (($handle = fopen($uploadFile, "r")) !== FALSE) {
                            // Read the header row
                            $header = fgetcsv($handle);
            
                            // Loop through the rows
                            while (($data = fgetcsv($handle)) !== FALSE) {
                               
                                $this->insertUser(
                                    $data[0],
                                    $data[1],
                                    '123456',
                                    $data[2],
                                    $data[3],
                                    $data[4],
                                    $data[5],
                                    $data[6],                                    
                                    $data[7]                                   
                                );
                                // echo "<br>";
                            }
            
                            fclose($handle);
                        } else {
                            echo "Error opening the CSV file.";
                        }
                    } else {
                        echo "Error uploading the file.";
                    }
                } else {
                    echo "No file uploaded.";
                }
            }
        }
       
        
           
    public function updateOrderStatus($orderId, $status)
    {

        $sql = "UPDATE orders SET status = :status WHERE id = :orderId"; // Corrected the SQL query
   
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([
            'status' => $status,
            'orderId' => $orderId            
        ]);

    } 
           
    public function UserToggleDeactive($email, $status)
    {

        $sql = "UPDATE signup SET delete_flg = :status, delete_date= :delete_date WHERE email = :email"; // Corrected the SQL query
   
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([
            'status' => (boolean) $status,
            'email' => $email  ,
            'delete_date' =>  $status == 1 ? date('Y-m-d h:i:s') : null

        ]);

    } 
        
    public function updateProfile($username, $tel1, $tel2, $tel3,$zip1,$zip2, $address)
    {
        $old_email = $_SESSION['email'];

        

        $sql = "UPDATE signup SET username = :username,  tel1 = :tel1, tel2 =:tel2, tel3 = :tel3,  zip1 = :zip1, zip2 = :zip2, address = :address 
                WHERE email = :old_email"; // Corrected the SQL query
        // $sql = "UPDATE item SET item_name = :item_name, detail = :detail, price = :price
        //         WHERE item_id = :item_id"; // Corrected the SQL query
    // echo $sql;
    // exit;
    // print_r([
    //     'username' => $username,
    //     'email' => $email,
    //     'tel1' => $tel1,
    //     'tel2' =>$tel2,
    //     'tel3'=> $tel3,
    //     'zip1'=> $zip1,
    //     'zip2'=>$zip2,
    //     'address' => $address
    // ]);
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'tel1' => $tel1,
            'tel2' =>$tel2,
            'tel3'=> $tel3,
            'zip1'=> $zip1,
            'zip2'=>$zip2,
            'address' => $address,
            'old_email' => $old_email
        ]);

    }  
     
                    
       
}



