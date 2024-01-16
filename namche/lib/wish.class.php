<?php

namespace namche\lib;

class Wish
{

    private $db = null;
    public function __construct($db = null)
    {
        $this->db = $db;
    }



    public function insData($customer_no, $item_id)
    {
        $table = ' wish ';
        $insData = [
            'customer_no' => $customer_no, 
            'item_id' => $item_id
        ];

       
        
        return $this->db->insert($table, $insData);
    }

    


// cart ni info getto site hicyona jyouhou ....... 
public function getWishData($customer_no)
{
    //SELECT
    //c.crt_id,
    //i.item_id,
    //i.item_name,
    //i.price,
    // i.image ';
    //FROM
    //cart c 
    //LEFT JOIN
    //item i 
    // ON
    //c. item_id= i.item_id';
    //WHERE
    //c.customer_no = ? AND c.delete_flg = ?';
    $table = ' wish c LEFT JOIN item i ON c.item_id = i.item_id';
    $column = ' c.wish_id, i.item_id, i.item_name,i.price,i.image ';
    $where  = ' c.customer_no = ?  ';
    $arrVal = [$customer_no];

    return $this->db->select($table, $column, $where, $arrVal);

    }



                
    public function deleteWish($item_id)
    {
        $table = ' wish ';
        $condition = ' wish_id = ?';
        $values = [$item_id];

        $sql = "DELETE FROM $table WHERE $condition";

        // Execute the query to delete the item
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }


    public function getwishsum($customer_no)
    {
        //gokeikingaku
        //SELECT
        //sum(i.price) AS totalPrice";
        //FROM
        //cart c 
        //LEFT JOIN
        //item i
        //ON
        //c.item_id = i.item_id"
        //WHERE
        //c. customer_no = ? AND c.delete_flg =?;

        $table  = " wish c LEFT JOIN item i ON c.item_id = i.item_id ";
        $column = " SUM( i.price ) AS totalPrice ";
        $where = ' c.customer_no  = ? ';
        $arrWhereVal = [$customer_no];

        $res = $this->db->select($table, $column, $where, $arrWhereVal);
        $price = ($res !== false && count($res) !== 0) ? $res[0]['totalPrice'] : 0;

        //item 
        $table = ' wish c ';
        $column = ' SUM( num ) AS num ';
        $res = $this->db->select($table, $column, $where, $arrWhereVal);
        $num = ($res !== false && count($res) !== 0) ? $res[0]['num'] : 0;
        return [$num, $price];
    }
    

}
?>
