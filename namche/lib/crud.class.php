<?php

namespace namche\lib;

class Crud
{
    public $cateArr = [];
    public $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

 

    // this is data insert for database 
    public function insertItem($item_name, $detail, $price, $image, $ctg_id, $subcategory_id)
    {
        $table = 'item';
        $columns = ['item_name', 'detail', 'price', 'image', 'ctg_id', 'subcategory_id'];
        $values = [$item_name, $detail, $price, $image, $ctg_id, $subcategory_id];
    
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (?, ?, ?, ?, ?, ?)";
    
       
        $this->db->setQuery($sql, $values);
    
        
        return true; 
    }

        // this is for  data read

//  public function read()
//         {
//             $data = array();
//             $table = 'item';
//             $columns = ['item_id', 'item_name', 'detail', 'price', 'image', 'ctg_id'];
//             $sql = "SELECT " . implode(', ', $columns) . " FROM $table";

//             // Execute the query and fetch the result
//             $stmt = $this->db->prepare($sql); 
//             $stmt->execute();
//             $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
//             foreach ($result as $row){
//                 $data[] = $row;
//             }
//             return $data;
//         }

public function read($page = 1, $perPage = 10)
{
    $data = array();
    $table = 'item';
    $columns = ['item_id', 'item_name', 'detail', 'price', 'image', 'ctg_id'];
    
    // Calculate the offset based on the current page and items per page
    $offset = ($page - 1) * $perPage;

    // Build the SQL query with LIMIT and OFFSET for pagination
    $sql = "SELECT " . implode(', ', $columns) . " FROM $table LIMIT :perPage OFFSET :offset";

    // Prepare and bind parameters
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':perPage', $perPage, \PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);

    // Execute the query and fetch the result
    $stmt->execute();
    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $data[] = $row;
    }

    return $data;
}


        public function getTotalItems()
        {
            $query = "SELECT COUNT(*) as total FROM item";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['total'];
        }
        

    public function getDataByID($item_id){
        $sql = "SELECT * FROM item WHERE item_id = :item_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $item_id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result;

    }

    public function update($item_id, $item_name, $detail, $price, $image, $ctg_id, $subcategory_id)
    {

        
        if($image){
            $sql = "UPDATE item SET image = :image
            WHERE item_id = :item_id"; // Corrected the SQL query

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'item_id' => $item_id,
                'image' => $image,
          
            ]);
        }
        $sql = "UPDATE item SET item_name = :item_name, detail = :detail, price = :price, ctg_id = :ctg_id, subcategory_id = :subcategory_id 
                WHERE item_id = :item_id"; // Corrected the SQL query
        // $sql = "UPDATE item SET item_name = :item_name, detail = :detail, price = :price
        //         WHERE item_id = :item_id"; // Corrected the SQL query
    // echo $sql;
    // exit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'item_id' => $item_id,
            'item_name' => $item_name,
            'detail' => $detail,
            'price' => $price,
            'ctg_id' => $ctg_id,
            'subcategory_id' => $subcategory_id
        ]);



    
        return true;
    }

    

            
     public function deleteItem($item_id)
        {
            $table = 'item';
            $condition = 'item_id = ?';
            $values = [$item_id];

            $sql = "DELETE FROM $table WHERE $condition";

            // Execute the query to delete the item
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        }


    

            
        public function deleteSession($customer_no)
           {
               $table = 'session';
               $condition = 'customer_no = ?';
               $values = [$customer_no];
   
               $sql = "DELETE FROM $table WHERE $condition";
   
               echo $sql;
               exit;
               // Execute the query to delete the item
               $stmt = $this->db->prepare($sql);
               return $stmt->execute($values);
           }

        //    delete users

        public function deleteUsers($id)
        {
            $table = 'signup';
            $condition = 'id = ?';
            $values = [$id];

            $sql = "DELETE FROM $table WHERE $condition";

            // Execute the query to delete the item
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        }


}



