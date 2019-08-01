<?php
/**
 * addressbooks is an array of dotmailer address book IDs, this is which list to add a contact to.
 *
 * Each addressbook entry can have a set of validation rules, in laravel format. Field names must match those
 * on the same addressbook in dotmailer, or the user will not be added to the address book.
 */
return [


    'new_user' => [
        'id' => 11111,
        'account' => 'Account_Name',
    ],

    'sales_lead' => [
        'id' => 11111,
        'account' => 'Account_Name',
        'rules' => [
            'FIRSTNAME' => 'required',
            'LASTNAME' => 'required',
            'MOBILE_NUMBER' => 'required',
        ]
    ],

    'weekly_subscriber' => [
        'id' => 11111,
        'account' => 'Account_Name',
        'rules' => [
            'FIRSTNAME' => 'required',
            'LASTNAME' => 'required',
            'EMAIL' => 'required',
        ]
    ]
];

