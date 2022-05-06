<?php

return [
    'enable' => env('ELASTICSEARCH_ENABLE', false),
    'hosts' => explode(',', env('ELASTICSEARCH_HOSTS')),
];
