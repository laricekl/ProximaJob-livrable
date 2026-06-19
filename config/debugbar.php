<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Debugbar
    |--------------------------------------------------------------------------
    |
    | Keep the Laravel Debugbar disabled by default so it never leaks into
    | public candidate or enterprise pages unless a developer explicitly
    | enables it in their local environment.
    |
    */
    'enabled' => env('DEBUGBAR_ENABLED', false),
];
