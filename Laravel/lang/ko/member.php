<?php

// Écran membre (« / » après connexion).
return [
    'nav' => [
        'current_bids' => '이번 입찰 내역',
        'unsettled' => '미결 입찰 내역',
        'inbox' => '받은 쪽지',
        'outbox' => '보낸 쪽지',
        'profile' => '개인정보 수정',
        'grade' => '회원 등급 변경',
    ],
    // ⓖ ⓜ ⓟ ⓐ à côté de l'identifiant.
    'grades' => [
        'G' => '일반',
        'M' => '정회원',
        'P' => '프리미엄',
        'A' => '관리자',
    ],
    'card' => [
        'lot' => 'Lot :n',
        'current_price' => '현재가',
        'starting_price' => '시작가',
        'price_hidden' => '마감 :h시간 전부터 가격 공개',
        'ended' => '마감',
        'closing' => '마감 :date KST',
        'bidders' => '입찰자 수',
        'sold' => '판매 완료',
        'awarded' => '낙찰가',
    ],
    'lots' => ':n개 상품',
    'empty' => '이번 경매에 등록된 상품이 아직 없습니다.',
    'send_message' => '쪽지 보내기 (정회원 신청, 등록 등)',
    'send_message_hint' => '쪽지를 보낼 프리미엄 회원을 선택하세요.',
    'no_premium' => '프리미엄 회원이 아직 없습니다.',
];
