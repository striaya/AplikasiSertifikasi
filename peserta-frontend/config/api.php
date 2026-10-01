<?php

return [
    // Alamat dasar API Go (Gin). Ubah lewat API_BASE_URL di file .env
    'base_url' => env('API_BASE_URL', 'http://localhost:8080/api'),
    'timeout'  => 10,
];
