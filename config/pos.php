<?php

return [
    'admin_username' => env('POS_ADMIN_USERNAME'),
    'admin_name' => env('POS_ADMIN_NAME', 'POS Administrator'),
    'admin_password' => env('POS_ADMIN_PASSWORD'),
    // Show the review credentials in local development by default; production
    // deployments must opt in explicitly because the login page is public.
    'demo_access_enabled' => env('POS_DEMO_ACCESS_ENABLED', env('APP_ENV', 'production') === 'local'),
    // Sample data is on in local development and explicitly enabled on Render.
    'demo_data_enabled' => env('POS_DEMO_DATA_ENABLED', env('APP_ENV', 'production') === 'local'),
    'reporting_timezone' => env('POS_REPORTING_TIMEZONE', 'Asia/Manila'),
    'low_stock_threshold' => (int) env('POS_LOW_STOCK_THRESHOLD', 5),
];
