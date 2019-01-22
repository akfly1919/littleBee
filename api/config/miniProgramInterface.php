<?php

    /**
     * 小程序接口
     */
    return [
        // post
        'POST save-team-profile' => 'save-team-profile', // 保存B端用户信息 
        'POST mini-code-login'   => 'mini-code-login',   // 小程序短信验证码登录
        'POST create-mini-code-member' => 'create-mini-code-member', // 生成小程序码 会员绑定
        'POST create-mini-code-client' => 'create-mini-code-client', // 生成小程序码 客户绑定
        'POST decrypt-user-phone'      => 'decrypt-user-phone',      // 解密用户手机号
        
        // get
        'GET login'              => 'login',             // 登录
        'GET from-share'         => 'from-share',        // 来自分享，C端用户看到的我们平台的产品详情页
    ];