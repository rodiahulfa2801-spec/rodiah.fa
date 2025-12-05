<?php
// functions.php

// selalu mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// utilitas kecil
function e($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// extract youtube id from various formats; return id or false
function extract_youtube_id($url) {
    if (!$url) return false;
    // common patterns
    $patterns = [
        '/youtu\.be\/([^\?&\/]+)/',
        '/youtube\.com\/watch\?v=([^\?&\/]+)/',
        '/youtube\.com\/embed\/([^\?&\/]+)/',
        '/youtube\.com\/v\/([^\?&\/]+)/'
    ];
    foreach ($patterns as $p) {
        if (preg_match($p, $url, $m)) return $m[1];
    }
    // maybe user pasted just id
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) return $url;
    return false;
}
