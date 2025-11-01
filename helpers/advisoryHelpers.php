<?php

function addBaseUrl($data, $baseUrl = "http://localhost/disaster-backend/") {
    if (isset($data['img_path'])) {
        $data['image_url'] = $baseUrl . $data['img_path'];
    }
    return $data;
}

function deleteDisasterImage($imgPath) {
    $fullPath = __DIR__ . '/../' . $imgPath;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
}
