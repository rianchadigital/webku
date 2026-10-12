<?php
/**
 * ==============================================================================
 * API TAUTAN BERMANFAAT (USEFUL LINKS) - MYSQL HOSTINGER
 * Puskesmas Kepulauan Seribu Selatan
 * ==============================================================================
 * Endpoint: /api/tautan.php
 * Methods : GET, POST, PUT, DELETE
 */

require_once __DIR__ . '/db.php';

$pdo = getDb();
$method = $_SERVER['REQUEST_METHOD'];

// Pastikan tabel tautan_bermanfaat ada
function ensureTautanTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS `tautan_bermanfaat` (
        `id` VARCHAR(50) NOT NULL,
        `name` VARCHAR(150) NOT NULL,
        `short_name` VARCHAR(50) DEFAULT NULL,
        `subtitle` VARCHAR(255) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `url` VARCHAR(255) NOT NULL,
        `badge` VARCHAR(50) DEFAULT 'Resmi',
        `badge_color` VARCHAR(100) DEFAULT NULL,
        `category` VARCHAR(100) DEFAULT 'Kepegawaian & Kinerja',
        `icon_name` VARCHAR(50) DEFAULT 'globe',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($sql);

    // Jika tabel baru dan kosong, masukkan data awal
    $count = (int)$pdo->query("SELECT COUNT(*) FROM `tautan_bermanfaat`")->fetchColumn();
    if ($count === 0) {
        $initial = [
            [
                'id' => 'link-etpp',
                'name' => 'e-TPP DKI Jakarta',
                'short_name' => 'e-TPP',
                'subtitle' => 'Tambahan Penghasilan Pegawai',
                'description' => 'Sistem pelaporan kinerja, kehadiran harian & realisasi pembayaran tunjangan ASN Pemprov DKI Jakarta.',
                'url' => 'https://etpp.jakarta.go.id/login',
                'badge' => 'Pemprov DKI Jakarta',
                'badge_color' => 'bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                'category' => 'Kepegawaian & Kinerja',
                'icon_name' => 'file'
            ],
            [
                'id' => 'link-simpeg',
                'name' => 'SIMPEG DKI Jakarta',
                'short_name' => 'SIMPEG',
                'subtitle' => 'Sistem Manajemen Kepegawaian',
                'description' => 'Portal terpadu layanan administrasi berkas ASN, profil jabatan, SK, dan mutasi Badan Kepegawaian Daerah.',
                'url' => 'https://pegawai.jakarta.go.id/login',
                'badge' => 'BKD DKI Jakarta',
                'badge_color' => 'bg-blue-50 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                'category' => 'Administrasi ASN',
                'icon_name' => 'user'
            ],
            [
                'id' => 'link-darlink',
                'name' => 'DARLINK Seribu Selatan',
                'short_name' => 'DARLINK',
                'subtitle' => 'Data Arsip & Rekam Layanan Interaktif',
                'description' => 'Sistem operasional terpadu internal faskes untuk monitoring arsip, rekam data, dan layanan kepulauan.',
                'url' => 'https://darlink.puskesmasseribuselatan.com',
                'badge' => 'Internal Puskesmas',
                'badge_color' => 'bg-teal-50 dark:bg-teal-950/80 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800',
                'category' => 'Sistem Manajemen Faskes',
                'icon_name' => 'database'
            ],
            [
                'id' => 'link-sisdmk',
                'name' => 'SISDMK Kemenkes RI',
                'short_name' => 'SISDMK',
                'subtitle' => 'Sistem Informasi SDM Kesehatan',
                'description' => 'Basis data nasional tenaga medis, dokter, bidan, perawat, verifikasi STR, dan perencanaan nakes Kemenkes.',
                'url' => 'https://sisdmk.kemkes.go.id/login',
                'badge' => 'Kementerian Kesehatan RI',
                'badge_color' => 'bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                'category' => 'SDM Kesehatan Nasional',
                'icon_name' => 'shield'
            ]
        ];

        $ins = $pdo->prepare("INSERT INTO `tautan_bermanfaat` 
            (`id`, `name`, `short_name`, `subtitle`, `description`, `url`, `badge`, `badge_color`, `category`, `icon_name`)
            VALUES (:id, :name, :short_name, :subtitle, :description, :url, :badge, :badge_color, :category, :icon_name)");

        foreach ($initial as $item) {
            $ins->execute([
                ':id' => $item['id'],
                ':name' => $item['name'],
                ':short_name' => $item['short_name'],
                ':subtitle' => $item['subtitle'],
                ':description' => $item['description'],
                ':url' => $item['url'],
                ':badge' => $item['badge'],
                ':badge_color' => $item['badge_color'],
                ':category' => $item['category'],
                ':icon_name' => $item['icon_name']
            ]);
        }
    }
}

try {
    ensureTautanTable($pdo);
} catch (Exception $e) {
    // Abaikan jika tidak ada izin DDL di hosting terbatas
}

// ------------------------------------------------------------------------------
// GET: Ambil semua data tautan bermanfaat
// ------------------------------------------------------------------------------
if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM `tautan_bermanfaat` ORDER BY `created_at` ASC");
        $rows = $stmt->fetchAll();

        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'shortName' => $row['short_name'] ?? $row['name'],
                'subtitle' => $row['subtitle'] ?? '',
                'description' => $row['description'] ?? '',
                'url' => $row['url'],
                'badge' => $row['badge'] ?? 'Resmi',
                'badgeColor' => $row['badge_color'] ?? '',
                'category' => $row['category'] ?? 'Kepegawaian & Kinerja',
                'iconName' => $row['icon_name'] ?? 'globe'
            ];
        }

        jsonResponse('success', 'Data tautan bermanfaat berhasil dimuat dari database MySQL', $items);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal memuat data tautan: ' . $e->getMessage(), null, 500);
    }
}

// ------------------------------------------------------------------------------
// POST: Tambah tautan baru
// ------------------------------------------------------------------------------
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['name']) || empty($input['url'])) {
        jsonResponse('error', 'Nama dan URL tautan bermanfaat wajib diisi.', null, 400);
    }

    $id = !empty($input['id']) ? $input['id'] : ('link-' . time());
    $name = trim($input['name']);
    $shortName = !empty($input['shortName']) ? trim($input['shortName']) : (!empty($input['short_name']) ? trim($input['short_name']) : substr($name, 0, 10));
    $subtitle = !empty($input['subtitle']) ? trim($input['subtitle']) : '';
    $description = !empty($input['description']) ? trim($input['description']) : '';
    $url = trim($input['url']);
    $badge = !empty($input['badge']) ? trim($input['badge']) : 'Resmi';
    $badgeColor = !empty($input['badgeColor']) ? trim($input['badgeColor']) : (!empty($input['badge_color']) ? trim($input['badge_color']) : '');
    $category = !empty($input['category']) ? trim($input['category']) : 'Kepegawaian & Kinerja';
    $iconName = !empty($input['iconName']) ? trim($input['iconName']) : (!empty($input['icon_name']) ? trim($input['icon_name']) : 'globe');

    try {
        $stmt = $pdo->prepare("INSERT INTO `tautan_bermanfaat` 
            (`id`, `name`, `short_name`, `subtitle`, `description`, `url`, `badge`, `badge_color`, `category`, `icon_name`)
            VALUES (:id, :name, :short_name, :subtitle, :description, :url, :badge, :badge_color, :category, :icon_name)
            ON DUPLICATE KEY UPDATE 
            `name` = VALUES(`name`),
            `short_name` = VALUES(`short_name`),
            `subtitle` = VALUES(`subtitle`),
            `description` = VALUES(`description`),
            `url` = VALUES(`url`),
            `badge` = VALUES(`badge`),
            `badge_color` = VALUES(`badge_color`),
            `category` = VALUES(`category`),
            `icon_name` = VALUES(`icon_name`)");

        $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':short_name' => $shortName,
            ':subtitle' => $subtitle,
            ':description' => $description,
            ':url' => $url,
            ':badge' => $badge,
            ':badge_color' => $badgeColor,
            ':category' => $category,
            ':icon_name' => $iconName
        ]);

        jsonResponse('success', 'Tautan bermanfaat berhasil disimpan di database MySQL!', [
            'id' => $id,
            'name' => $name,
            'url' => $url
        ]);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal menyimpan ke database MySQL: ' . $e->getMessage(), null, 500);
    }
}

// ------------------------------------------------------------------------------
// PUT: Update tautan yang sudah ada
// ------------------------------------------------------------------------------
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['id'])) {
        jsonResponse('error', 'ID tautan wajib disertakan untuk pembaruan.', null, 400);
    }

    $id = $input['id'];
    $fields = [];
    $params = [':id' => $id];

    if (isset($input['name'])) {
        $fields[] = "`name` = :name";
        $params[':name'] = trim($input['name']);
    }
    if (isset($input['shortName']) || isset($input['short_name'])) {
        $fields[] = "`short_name` = :short_name";
        $params[':short_name'] = trim($input['shortName'] ?? $input['short_name']);
    }
    if (isset($input['subtitle'])) {
        $fields[] = "`subtitle` = :subtitle";
        $params[':subtitle'] = trim($input['subtitle']);
    }
    if (isset($input['description'])) {
        $fields[] = "`description` = :description";
        $params[':description'] = trim($input['description']);
    }
    if (isset($input['url'])) {
        $fields[] = "`url` = :url";
        $params[':url'] = trim($input['url']);
    }
    if (isset($input['badge'])) {
        $fields[] = "`badge` = :badge";
        $params[':badge'] = trim($input['badge']);
    }
    if (isset($input['badgeColor']) || isset($input['badge_color'])) {
        $fields[] = "`badge_color` = :badge_color";
        $params[':badge_color'] = trim($input['badgeColor'] ?? $input['badge_color']);
    }
    if (isset($input['category'])) {
        $fields[] = "`category` = :category";
        $params[':category'] = trim($input['category']);
    }
    if (isset($input['iconName']) || isset($input['icon_name'])) {
        $fields[] = "`icon_name` = :icon_name";
        $params[':icon_name'] = trim($input['iconName'] ?? $input['icon_name']);
    }

    if (empty($fields)) {
        jsonResponse('error', 'Tidak ada data yang diperbarui.', null, 400);
    }

    try {
        $sql = "UPDATE `tautan_bermanfaat` SET " . implode(", ", $fields) . " WHERE `id` = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        jsonResponse('success', 'Data tautan bermanfaat berhasil diperbarui di database MySQL.', ['id' => $id]);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal memperbarui database: ' . $e->getMessage(), null, 500);
    }
}

// ------------------------------------------------------------------------------
// DELETE: Hapus tautan
// ------------------------------------------------------------------------------
if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    if (!$id) {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
    }

    if (!$id) {
        jsonResponse('error', 'ID tautan wajib diberikan untuk dihapus.', null, 400);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `tautan_bermanfaat` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        jsonResponse('success', 'Tautan berhasil dihapus dari database MySQL.', ['id' => $id]);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal menghapus tautan dari database: ' . $e->getMessage(), null, 500);
    }
}
