<?php

// Écran membre (« / » après connexion).
return [
    'nav' => [
        'current_bids' => '本期竞拍',
        'unsettled' => '未结算竞拍',
        'inbox' => '收到的消息',
        'outbox' => '发送的消息',
        'profile' => '修改个人信息',
        'grade' => '会员等级变更',
    ],
    // ⓖ ⓜ ⓟ ⓐ à côté de l'identifiant.
    'grades' => [
        'G' => '普通会员',
        'M' => '正式会员',
        'P' => '高级会员',
        'A' => '管理员',
    ],
    'card' => [
        'lot' => '拍品 :n',
        'current_price' => '当前价',
        'starting_price' => '起拍价',
        'price_hidden' => '截止前 :h 小时公布价格',
        'ended' => '已结束',
        'closing' => '截止 :date KST',
        'bidders' => '出价人数',
        'sold' => '已售出',
        'awarded' => '成交价',
    ],
    'lots' => ':n 件拍品',
    'empty' => '本期暂无拍品。',
    'send_message' => '发送消息（申请正式会员、注册等）',
    'send_message_hint' => '选择一位高级会员发送消息。',
    'no_premium' => '暂无高级会员。',
];
