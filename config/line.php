<?php
return [
    'line_authorize_uri' => env('LINE_AUTHORIZE_URL'),
    'client_id'          => env('LINE_CHANNEL_ID'),
    'client_secret'      => env('LINE_CHANNEL_SECRET'),
    'redirect'           => env('LINE_REDIRECT_URI'),
];
