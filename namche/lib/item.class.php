<?php

namespace namche\lib;

class Item
{
    public $cateArr = [];
    public $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function getCategoryList()
    {
        $table = ' category ';
        $col = ' ctg_id, category_name ';
        $res = $this->db->select($table, $col);
        return $res;
    }


    //-------------this is comment for pagination page-------//


    // public function getItemList($ctg_id, $child='' )
    // {
    //     //
    //     $table = 'item';
    //     $col = 'item_id, item_name, price,image, ctg_id, subcategory_id ';
    //     $where = ($ctg_id !== '') ? ' ctg_id = ? ' : '';
    //     $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];

    //     if($child){
    //         $where .= ($child != '') ? ' and subcategory_id = ? ' : '';
    //         $arrVal[] = ($child !== '') ? $child : '';
    //     }
       
    //     $res = $this->db->select($table, $col, $where, $arrVal);

    //     shuffle($res); // shuffle show data randomly

    //     return ($res !== false && count($res) !==0) ? $res : false;
    // }

    //------------------here ge getting item detail like item name item price and image from database ----------------//

    


    
   
    
    //this is  for my fetch all data pagination from database 
    public function getItemList($ctg_id, $child = '', $start = 0, $itemsPerPage = null)
    {
        $table = 'item';
        $col = 'item_id, item_name, price, image, ctg_id, subcategory_id';
        $where = ($ctg_id !== '') ? ' ctg_id = ? ' : '';
        $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];
    
        if ($child) {
            $where .= ($child != '') ? ' and subcategory_id = ? ' : '';
            $arrVal[] = ($child !== '') ? $child : '';
        }
    
        // Add pagination conditions if $start and $itemsPerPage are provided
        $limit = ($itemsPerPage !== null) ? "LIMIT $start, $itemsPerPage" : '';
    
        // Retrieve items with pagination
        $res = $this->db->select($table, $col, $where, $arrVal, $limit);
    
        shuffle($res); // shuffle show data randomly
    
        return ($res !== false && count($res) !== 0) ? $res : false;
    }
    

//-- here is item count in database 

public function getTotalItemCount($ctg_id, $child = '')
{
    $table = 'item';
    $col = 'COUNT(item_id) AS total_count';
    $where = ($ctg_id !== '') ? ' ctg_id = ? ' : '';
    $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];

    if ($child) {
        $where .= ($child != '') ? ' AND subcategory_id = ? ' : '';
        $arrVal[] = ($child !== '') ? $child : '';
    }

    $result = $this->db->select($table, $col, $where, $arrVal);

    return ($result !== false && isset($result[0]['total_count'])) ? $result[0]['total_count'] : 0;
}

    
public function getItemDetailData($item_id)
    {
        $table = ' item';
        $col = ' item_id, item_name, detail, price, image, ctg_id';
        $where = ($item_id !== '') ? ' item_id = ?' : '';
        $arrVal = ($item_id !== '') ? [$item_id] : [];
        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false && count($res) !== 0) ? $res : false;
    }

    // this is data insert for database 
    public function insertItemData($item_name, $detail, $price, $image, $ctg_id)
    {
        $table = 'item';
        $columns = ['item_name', 'detail', 'price', 'image', 'ctg_id'];
        $values = [$item_name, $detail, $price, $image, $ctg_id];
    
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (?, ?, ?, ?, ?)";
    
       
        $this->db->setQuery($sql, $values);
    
        
        return true; 
    }
    
}



