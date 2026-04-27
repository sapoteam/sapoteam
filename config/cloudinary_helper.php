<?php

function uploadToCloudinary($fileTmpPath, $folder = 'general') {
    $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
    $apiKey    = getenv('CLOUDINARY_API_KEY');
    $apiSecret = getenv('CLOUDINARY_API_SECRET');

    $timestamp = time();
    $params    = "folder={$folder}&timestamp={$timestamp}{$apiSecret}";
    $signature = sha1($params);

    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

    $postFields = [
        'file'      => new CURLFile($fileTmpPath),
        'api_key'   => $apiKey,
        'timestamp' => $timestamp,
        'signature' => $signature,
        'folder'    => $folder,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['secure_url'] ?? null;
}

function deleteFromCloudinary($publicId) {
    $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
    $apiKey    = getenv('CLOUDINARY_API_KEY');
    $apiSecret = getenv('CLOUDINARY_API_SECRET');

    $timestamp = time();
    $params    = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
    $signature = sha1($params);

    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy";

    $postFields = [
        'public_id' => $publicId,
        'api_key'   => $apiKey,
        'timestamp' => $timestamp,
        'signature' => $signature,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function getPublicIdFromUrl($url) {
    if (empty($url) || strpos($url, 'cloudinary.com') === false) return null;
    preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $url, $matches);
    return $matches[1] ?? null;
}