<?php
/**
 * Campaigns, used for triggered campaign emails
 *
 * I've put examples in below to show the structure, rules are used as validators and can both define a parameter as required and the data type.
 **/
return [
    'new_user_welcome' => [
        'id' => 11111,
        'account' => 'Account name',
        'rules' => [
            'email' => 'required',
            'password' => 'required'
        ]
    ],

    'product_subscribed' => [
        'id' => 111111,
        'account' => 'Account name',
        'rules' => [
            'firstname' => 'required',
            'lastname' => 'required',
            'product_id' => 'required|integer',
        ]
    ],
];