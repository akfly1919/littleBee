<?php
namespace api\models;

/**
 * 接口对接类
 * @author Administrator
 *
 */
class BusinessInterface
{
    public $clientID; // 客户ID
    
    
    public function __construct($clientID)
    {
        $this->clientID = $clientID;  
    } 
    
    
}