<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Token Prefix
     |--------------------------------------------------------------------------
     |
     | This option provides a token prefix which use to identify the token
     | The prefix represents the begging of the token, For example: kt8zbi17xH8_N0phusHqgoQQ...
     | Feel free to change it to whatever you like.
     | However, The default value is: (kt)
     |
     */
    'prefix' => 'kt',


    /*
     |--------------------------------------------------------------------------
     | Token TTL
     |--------------------------------------------------------------------------
     |
     | This option sets the ttl (Time To Live) to the token
     | The token will expire after the amount of minutes that you provide here
     | Default is one hour (60)
     |
     */
    'ttl' => 60,


    /*
     |--------------------------------------------------------------------------
     | Token Password
     |--------------------------------------------------------------------------
     |
     | You can set the password for the tokens here.
     | The password, used to secure and verify the incoming tokens.
     | Only the owner of the password can decrypt the token
     |
     */
    'password' => env('KTOKEN_PASSWORD', ''),


    /*
      |--------------------------------------------------------------------------
      | Token Storage Prefix
      |--------------------------------------------------------------------------
      |
      | Storage Prefix is used to identify the key inside the storage.
      | So, we can remove or revoke the existing tokens
      | Feel free to change the prefix to whatever you like.
      | However, the default value is: (kt.tokens)
      |
      */
    'storage_prefix' => 'kt.tokens',


    /*
      |--------------------------------------------------------------------------
      | Auth Model
      |--------------------------------------------------------------------------
      |
      | Auth model will determine the authintecatable model and
      | To search for the user that assigned to its token
      | Then, assigned the user to the incoming request
      |
      */
    'auth_model' => \App\User::class
];