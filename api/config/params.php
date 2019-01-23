<?php

// domain
$domain = 'zxgybj.com';

return [
    // 小蜜蜂小程序
    'littleBeeParams' => [
        'tokenExpire' => 3600 * 24,
        'appid'       => 'wx0dfc02479bdaa4fe',
        'secret'      => '836b9cc3975a160ec569ecd25d2a89be',
        'key'      => 'zxgybjjrfwwbyxgs888888888888zxgy',
        'mchid'      => '1522792231',
        
        
        'loginUrl'          => "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code&connect_redirect=1",
        // 生成小程序码接口
        'createMiniCode'    => 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=%s',
        // getAccessToken 接口
        'getAccessTokenUrl' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',
        //统一下单
        'createOrderUrl'    => 'https://api.mch.weixin.qq.com/pay/unifiedorder',
        //分账地址
        'profitSharingUrl'    => 'https://api.mch.weixin.qq.com/secapi/pay/profitsharing',
    ],
    
    // accessToken 文件地址
    'accessTokenFile'=> '/data/www/littleBee/api/web/accessToken.txt',
    
    // 下载链接
    'downloadAppUrl' => 'https://'.$domain.'/smallBee/cThird/down.html',
    
    // share url
    'shareParams' => [
        // 'shareUrl'     => 'https://'.$domain.'/api/from-share?shareID=%s',
        'shareUrl'     => 'https://'.$domain.'/smallBee/cThird/index.html?id=%s&teamID=%s&proType=%s',
        'devMemberUrl' => 'https://'.$domain.'/api/team-profile?sjID=%s',
    ],
    
    // 不需要token的action
    'optional' => [
        // app
        'register',
        'app-login',
        'reset-password',
        'create-down-code',
        'app-from-share',
        'app-bind-member',
        'app-save-team-profile',
        'my-test',
        
        // common
        'index',
        'get-msg-code',
        'get-client',
        'img-txt-extend',
        'prize-explain',
        'member-guide',
        'member-group',
        'trade-news',
        'get-team',
        'get-client',
        'save-client',
        'save-team-profile',
        'buy-member-callback',
        
        // miniProgram
        'login',
        'from-share',
        'mini-code-login',
        'decrypt-user-phone'
    ],
    
    // 不需要判断手机号是否存在的action
    'phoneOptional' => [
        'get-msg-code',
        'app-login',
        'reset-password',
        'save-client',
        'save-team-profile',
        'mini-code-login'
    ],
];
