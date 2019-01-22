<?php
    /**
     *  app、小程序公共接口
     */
    return [
        // post
        'POST get-msg-code'=> 'get-msg-code', // 获取短信验证码
        'POST save-client' => 'save-client',  // 保存C端信息
        'POST get-money'   => 'get-money',    // 提现
        'POST consult'     => 'consult',      // 咨询留言
        
        // get
        'GET team-profile'   => 'team-profile',   // B端填写资料页面获取省份
        'GET get-team'       => 'get-team',       // 查询B端用户信息
        'GET get-client'     => 'get-client',     // 查询C端用户信息
        'GET get-place'      => 'get-place',      // 获取城市、县
        
        'GET index'          => 'index',          // 首页
        'GET pro-detail'     => 'pro-detail',     // 产品详情
        'GET per-center'     => 'per-center',     // 个人中心
        'GET my-client'      => 'my-client',      // 我的客户
        'GET my-member'      => 'my-member',      // 我的会员
        'GET dev-member'     => 'dev-member',     // 发展会员
        'GET img-txt-extend' => 'img-txt-extend', // 图文推广
        'GET img-txt-detail' => 'img-txt-detail', // 图文详情
        'GET member-group'   => 'member-group',   // 会员精英群
        'GET member-guide'   => 'member-guide',   // 会员指南
        'GET prize-explain'  => 'prize-explain',  // 奖励说明
        'GET trade-news'     => 'trade-news',     // 行业动态
        'GET my-money'       => 'my-money',       // 我的佣金
        'GET my-money-list'  => 'get-money-list', // 提现列表
        'GET code-start'     => 'code-start',     // 推广赚钱、扫码做单
        'GET get-loan-income-list'        => 'get-loan-income-list',        // 贷款收入记录 
        'GET get-credit-card-income-list' => 'get-credit-card-income-list', // 信用卡收入记录
        'GET get-big-data-income-list'    => 'get-big-data-income-list',    // 大数据收入记录
        'GET get-consult-list' => 'get-consult-list', // 获取咨询小蜜历史记录
    ];