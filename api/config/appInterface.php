<?php
    /**
     * app接口
     */
    return [
        // post
        'POST app-login'              => 'app-login',              // 登录
        'POST register'               => 'register',               // 注册
        'POST reset-password'         => 'reset-password',         // 重置密码
        'POST app-save-team-profile'  => 'app-save-team-profile',  // B端保存用户信息
        'POST app-bind-member'        => 'app-bind-member',        // app下载app之前绑定会员
        
        // get
        'GET create-down-code'        => 'create-down-code',       // 下载二维码
        'GET app-from-share'          => 'app-from-share',         // 来自分享，C端用户看到的我们平台的产品详情页
    ];