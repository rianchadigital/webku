<?php
/**
 * ==============================================================================
 * API PENDETEKSI & MANAJEMEN GAMBER HEADER SLIDER
 * Puskesmas Kepulauan Seribu Selatan
 * ==============================================================================
 * Endpoint: /api/slider.php
 * Method  : GET (List slider images) | POST (Upload / Set slider images)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$rootDir = dirname(__DIR__);

// Daftar folder pencarian slider (prioritas urut dari atas ke bawah)
$sliderFolders = [
    '/asset/slider/'        => $rootDir . '/asset/slider',
    '/public/asset/slider/' => $rootDir . '/public/asset/slider',
    '/assets/slider/'       => $rootDir . '/assets/slider',
    '/banners/'             => $rootDir . '/banners',
    '/public/banners/'      => $rootDir . '/public/banners',
];

$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $foundImages = [];
    $seenFilenames = [];

    foreach ($sliderFolders as $webPrefix => $physicalPath) {
        if (!is_dir($physicalPath)) {
            continue;
        }

        $files = scandir($physicalPath);
        if (!$files) continue;

        // Urutkan secara natural agar banner-1, banner-2 berurutan rapi
        natsort($files);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExtensions)) {
                continue;
            }

            // Hindari duplikat nama file yang sama persis
            if (isset($seenFilenames[$file])) {
                continue;
            }
            $seenFilenames[$file] = true;

            $fullPath = $physicalPath . '/' . $file;
            $fileSize = @filesize($fullPath) ?: 0;
            $modTime = @filemtime($fullPath) ?: 0;

            $foundImages[] = [
                'filename' => $file,
                'url'      => $webPrefix . $file,
                'size'     => $fileSize,
                'updated'  => date('Y-m-d H:i:s', $modTime),
                'folder'   => $webPrefix
            ];
        }
    }

    echo json_encode([
        'status'  => 'success',
        'message' => count($foundImages) . ' gambar slider terdeteksi di folder asset/slider.',
        'count'   => count($foundImages),
        'images'  => $foundImages
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// POST: Upload slider langsung dari admin jika diperlukan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada file gambar yang diunggah']);
        exit;
    }

    $file = $_FILES['file'];
    $slot = isset($_POST['slot']) ? intval($_POST['slot']) : 1; // 1, 2, 3, 4
    if ($slot < 1 || $slot > 4) $slot = 1;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Format file tidak didukung (.'.$ext.'). Gunakan jpg, png, atau webp']);
        exit;
    }

    // Target folder di asset/slider dan public/asset/slider
    $targetDir1 = $rootDir . '/asset/slider';
    $targetDir2 = $rootDir . '/public/asset/slider';
    @mkdir($targetDir1, 0755, true);
    @mkdir($targetDir2, 0755, true);

    $finalName = "banner-{$slot}.{$ext}";
    $dest1 = $targetDir1 . '/' . $finalName;
    $dest2 = $targetDir2 . '/' . $finalName;

    if (move_uploaded_file($file['tmp_name'], $dest1)) {
        @copy($dest1, $dest2);
        echo json_encode([
            'status'  => 'success',
            'message' => "Gambar slider slot {$slot} berhasil diunggah ke asset/slider/{$finalName}",
            'url'     => "/asset/slider/{$finalName}"
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file ke folder asset/slider di server']);
    }
    exit;
}
