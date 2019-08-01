<?php
/**
 * Dotmailer Configuration
 */
$config = [

    /**
     * Which API version to use
     */
    'api_version' => 'v2',

    /**
     * Account References
     */
    'auth' => [
        'Account_name' => [
            'username' => env('USERNAME_EXISTING_MEMBERS'),
            'password' => env('PASSWORD_EXISTING_MEMBERS')
        ]
    ],
];

$config['addressbooks'] = include('dotmailer/addressbooks.php');
$config['campaigns'] = include('dotmailer/campaigns.php');

return $config;