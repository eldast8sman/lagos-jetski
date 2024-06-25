<?php

return [
    "api_credentials" => [
        "base_url" => env('G5_BASE_URL', 'http://posapi2.vertigopos.com/api'),
        "login_url" => 'http://posapi2.vertigopos.com/LoginUser',
        "workstation_id" => env('G5_WORKSTATION_ID', 1),
        "branch_id" => env('G5_BRANCH_ID', 1),
        "password" => env('G5_PASSWORD', '8887910'),
        "employee_code" => env('G5_EMPLOYEE_CODE', '999')
    ]
];