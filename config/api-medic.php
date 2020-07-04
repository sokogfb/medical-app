<?php
/**
 * --------------------------------------
 * Define all the endpoints and
 * api keys for api
 * --------------------------------------
 */
return [
    /**
     * ---------------------
     * set the api keys
     * ---------------------
     */
    'keys' => [
        'sandbox' => [
            'username' => config(env('SANDBOX_API_MEDIC_USERNAME'), 'ososiportal@gmail.com'),
            'password' => config(env('SANDBOX_API_MEDIC_PASSWORD'), 't9JSj8q6T5MdRr32W'),
        ],
        'live' => [
            'username' => config(env('LIVE_API_MEDIC_USERNAME'), 'w9M8W_GMAIL_COM_AUT'),
            'password' => config(env('LIVE_API_MEDIC_PASSWORD'), 'r3B9Kys5Y4TiHc82W'),
        ],
    ],

    /**
     * ---------------------------------
     * Set the default url endpoints
     * ---------------------------------
     */
    'url' => [
        'endpoint' => 'https://healthservice.priaid.ch/',
        'auth_endpoint' => 'https://authservice.priaid.ch/login',
        'symptoms' => 'symptoms'
    ],

    /**
     * ---------------------------------------------------------------------------------------------------
     * The timeout is the time given for the response to be given if no response is given
     * in 120 seconds the request is dropped.
     * You are free to set your timeout
     * ---------------------------------------------------------------------------------------------------
     */
    'timeout' => env('TIMEOUT', 120), // Response timeout 120sec

    /**
     * ---------------------------------------------------------------------------------------------------
     * The connection timeout is the time given for the request to acquire full connection to the
     * end point url. So if not connection is made in 60 seconds the request is dropped.
     * Your free to set your own connection timeout.
     * ---------------------------------------------------------------------------------------------------
     */
    'connect_timeout' => env('CONNECTION_TIMEOUT', 60), // Connection timeout 60sec
];
