<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Refund Types Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for different refund types available
    | in the system. Each refund type can be enabled/disabled for refunds.
    |
    */

    'types' => [
        'wallet' => [
            'label' => 'Wallet',
            'can_be_refunded' => true,
            'enum_value' => \App\Enums\RefundTypeEnum::WALLET->value,
            'description' => 'Refund amount will be credited back to user\'s wallet',
        ],
        'cash' => [
            'label' => 'Cash',
            'can_be_refunded' => true,
            'enum_value' => \App\Enums\RefundTypeEnum::CASH->value,
            'description' => 'Refund will be processed as cash payment',
        ],
        'online' => [
            'label' => 'Online Payment',
            'can_be_refunded' => false,
            'enum_value' => \App\Enums\RefundTypeEnum::ONLINE->value,
            'description' => 'Online payment refunds are not available',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Refund Settings
    |--------------------------------------------------------------------------
    |
    | General settings for refund processing
    |
    */

    'settings' => [
        'allow_multiple_refunds' => false, // Original order can only be refunded once
        'auto_approve' => true, // Automatically process refunds without admin approval
        'refund_to_same_payment_method' => true, // Refund using same payment method as original order
    ],
];