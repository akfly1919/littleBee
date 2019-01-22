<?php
namespace api\models;

class CustomError {
    
    protected $message;  // 错误信息
    protected $errorCode;// 错误码
    
    // 构造器使 message 变为必须被指定的属性
    public function __construct($message, $errorCode = 0)
    {
        $this->message   = $message;
        $this->errorCode = $errorCode;
    }
    
    public function josnError()
    {
        return json_encode(['code' => $this->errorCode, 'msg' => $this->message]);
    } 
}