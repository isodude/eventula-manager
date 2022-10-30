<?php

return [

    // The default gateway to use
    'default' => 'paypal',

    // Add in each gateway here
    'gateways' => [
        'paypal_express' => [
            'driver'  => 'PayPal_Express',
            'options' => [
                'solutionType'      => '',
                'landingPage'       => '',
                'headerImageUrl'    => '',
                'displayName'       => 'Paypal',
                'note'              => 'You will be redirected offsite for this payment.',
            ],
            'credentials' => [
                'username' => '',
                'password' => '',
                'signature' => ''
            ]
        ],
        'stripe' => [
            'driver'  => 'stripe',
            'options' => [
                'displayName'   => 'Debit/Credit Card via stripe',
                'note'          => 'You may be redirected offsite for this payment.',
            ],
            'credentials' => [
                'public' => env('STRIPE_PUBLIC_KEY'),
                'secret' => env('STRIPE_SECRET_KEY')
            ]
        ],
        'quickpay' => [
            'driver'  => 'quickpay',
            'options' => [
                'displayName'   => 'Quickpay',
                'note'          => 'You may be redirected offsite for this payment.',
            ],
            'credentials' => [
                'merchant' => env('QUICKPAY_MERCHANT_ID'),
                'agreement' => env('QUICKPAY_AGREEMENT_ID'),
                'privatekey' => env('QUICKPAY_PRIVATE_KEY'),
                'apikey' => env('QUICKPAY_API_KEY'),
            ]
        ],
        'free' => [
            'driver'  => 'free_driver',
            'options' => [
                'displayName'   => 'Free',
                'note'          => '',
            ]
            ],
        'onsite' => [
            'driver'  => 'onsite_driver',
            'options' => [
                'displayName'   => 'On site',
                'note'          => 'pay cash on site',
            ]
        ]
    ]

];