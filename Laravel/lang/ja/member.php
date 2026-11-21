<?php

// Écran membre (« / » après connexion).
return [
    'nav' => [
        'current_bids' => '今回の入札一覧',
        'unsettled' => '未決済の入札',
        'inbox' => '受信メッセージ',
        'outbox' => '送信メッセージ',
        'profile' => '個人情報の変更',
        'grade' => '会員グレード変更',
    ],
    // ⓖ ⓜ ⓟ ⓐ à côté de l'identifiant.
    'grades' => [
        'G' => '一般会員',
        'M' => '正会員',
        'P' => 'プレミアム会員',
        'A' => '管理者',
    ],
    'card' => [
        'lot' => 'ロット :n',
        'current_price' => '現在価格',
        'starting_price' => '開始価格',
        'price_hidden' => '価格は終了 :h 時間前に公開',
        'ended' => '終了',
        'closing' => '終了 :date KST',
        'bidders' => '入札者数',
        'sold' => '落札済み',
        'secret_sr' => 'カードには価格を表示しません',
        'shh' => [
            // Petits mots de la bulle du cactus : un par carte, dans l'ordre, la liste se répète.
            'しーっ！',
            'ないしょ！',
            'ナゾ！',
            '当ててみて！',
            '見ちゃダメ！',
            'ヒミツ！',
        ],
        'awarded' => '落札価格',
    ],
    'lots' => ':n ロット',
    'empty' => 'このセッションの出品はまだありません。',
    'send_message' => 'メッセージを送る（正会員申請、登録など）',
    'send_message_hint' => 'メッセージを送るプレミアム会員を選んでください。',
    'no_premium' => 'プレミアム会員はまだいません。',
];
