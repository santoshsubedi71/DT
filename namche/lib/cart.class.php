<?php

namespace namche\lib;

class Cart
{

    private $db = null;
    public function __construct($db = null)
    {
        $this->db = $db;
    }

    //cart ni ireru baai

    public function insCartData($customer_no, $item_id, $qty=1)
    {
        $table = ' cart ';


        $currentItem = $this->getCartItemData($customer_no, $item_id);

        if($currentItem && isset($currentItem[0])){
           $this->updateCartData($currentItem[0]['crt_id'], ((
            ((int) $currentItem[0]['quantity']))+$qty
           ));

           return header('Location: ./cart.php');
        }


        $insData = [
            'customer_no' => $customer_no, 
            'item_id' => $item_id,
            'quantity' => $qty
        ];
        
        
        $this->db->insert($table, $insData);
        return header('Location: ./cart.php');
    }


// cart ni info getto site hicyona jyouhou .......
    public function getCartData($customer_no)
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
        $table = ' cart c LEFT JOIN item i ON c.item_id = i.item_id';
        $column = ' c.crt_id, c.quantity, i.item_id, i.item_name,i.price,i.image ';
        $where  = ' c.customer_no = ? AND c.delete_flg = ? ';
        $arrVal = [$customer_no, 0];

        return $this->db->select($table, $column, $where, $arrVal);

    }

    
    public function getCartItemData($customer_no, $item_id)
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
        $table = ' cart c LEFT JOIN item i ON c.item_id = i.item_id';
        $column = ' c.crt_id, c.quantity, i.item_id, i.item_name,i.price,i.image ';
        $where  = ' c.customer_no = ? AND c.delete_flg = ? AND c.item_id = ? ';
        $arrVal = [$customer_no, 0, $item_id];

        return $this->db->select($table, $column, $where, $arrVal);

    }
        //
    public function delCartData($crt_id)

    {
        $table = ' cart ';
        $insData = ['delete_flg' => 1];
        $where = ' crt_id = ? ';
        $arrWhereVal = [$crt_id];

        return $this->db->update($table, $insData, $where, $arrWhereVal);
    }

    
    public function delAllCartData()

    {
        $table = ' cart ';        
        $condition = ' customer_no = ?';
        $values = [$_SESSION['customer_no']];

        $sql = "DELETE FROM $table WHERE $condition";

        // Execute the query to delete the item
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
        //
    public function getItemAndSumPrice($customer_no)
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

        $table  = " cart c LEFT JOIN item i ON c.item_id = i.item_id ";
        $column = " SUM( i.price*c.quantity ) AS totalPrice ";
        $where = ' c.customer_no  = ? AND c.delete_flg = ?';
        $arrWhereVal = [$customer_no, 0];

        $res = $this->db->select($table, $column, $where, $arrWhereVal);
        $price = ($res !== false && count($res) !== 0) ? $res[0]['totalPrice'] : 0;

        //item 
        $table = ' cart c ';
        $column = ' SUM( num ) AS num ';
        $res = $this->db->select($table, $column, $where, $arrWhereVal);
        $num = ($res !== false && count($res) !== 0) ? $res[0]['num'] : 0;
        return [$num, $price];
    }

    // here id aded new funtion
    public function getItemByItemId($customer_no, $item_id) {
        $table = 'cart c LEFT JOIN item i ON c.item_id = i.item_id';
        $column = 'c.crt_id, i.item_id, i.item_name, i.price, i.image, c.quantity';
        $where = 'c.customer_no = ? AND c.item_id = ? AND c.delete_flg = ?';
        $arrVal = [$customer_no, $item_id, 0];
    
        return $this->db->select($table, $column, $where, $arrVal);
    }
    
    public function updateCartData($crt_id, $quantity) {
        $table = 'cart';
        $insData = ['quantity' => $quantity];
        $where = 'crt_id = ?';
        $arrWhereVal = [$crt_id];
    
        return $this->db->update($table, $insData, $where, $arrWhereVal);
    }




    
    
}
