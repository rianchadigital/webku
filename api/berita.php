<?php
/**
 * ==============================================================================
 * API BERITA & ARTIKEL KESEHATAN - MYSQL HOSTINGER
 * Puskesmas Kepulauan Seribu Selatan
 * ==============================================================================
 * Endpoint: /api/berita.php
 * Methods : GET, POST, PUT, DELETE
 */

require_once __DIR__ . '/db.php';

$pdo = getDb();
$method = $_SERVER['REQUEST_METHOD'];

function ensureBeritaTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS `berita_kegiatan` (
        `id` VARCHAR(50) NOT NULL,
        `judul` VARCHAR(255) NOT NULL,
        `kategori` VARCHAR(100) NOT NULL DEFAULT 'Kesehatan Masyarakat',
        `ringkasan` TEXT DEFAULT NULL,
        `konten` LONGTEXT NOT NULL,
        `penulis` VARCHAR(150) NOT NULL DEFAULT 'Tim Redaksi Puskesmas',
        `tanggal` VARCHAR(50) NOT NULL,
        `foto_url` VARCHAR(255) DEFAULT NULL,
        `dibaca` INT(11) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
}

try {
    ensureBeritaTable($pdo);
} catch (Exception $e) {}

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM `berita_kegiatan` ORDER BY `created_at` DESC");
        $rows = $stmt->fetchAll();

        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id' => $row['id'],
                'title' => $row['judul'],
                'category' => $row['kategori'],
                'summary' => $row['ringkasan'],
                'content' => $row['konten'],
                'author' => $row['penulis'],
                'date' => $row['tanggal'],
                'imageUrl' => $row['foto_url'],
                'readCount' => (int)$row['dibaca']
            ];
        }

        jsonResponse('success', 'Data berita berhasil dimuat dari database', $items);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal memuat berita: ' . $e->getMessage(), null, 500);
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['title']) || empty($input['content'])) {
        jsonResponse('error', 'Judul dan konten berita wajib diisi.', null, 400);
    }

    $id = !empty($input['id']) ? $input['id'] : ('news-' . time());
    $title = trim($input['title']);
    $category = !empty($input['category']) ? trim($input['category']) : 'Kesehatan Masyarakat';
    $summary = !empty($input['summary']) ? trim($input['summary']) : substr($title, 0, 140);
    $content = trim($input['content']);
    $author = !empty($input['author']) ? trim($input['author']) : 'Tim Redaksi Puskesmas';
    $date = !empty($input['date']) ? trim($input['date']) : date('d M Y');
    $imageUrl = !empty($input['imageUrl']) ? trim($input['imageUrl']) : (!empty($input['image_url']) ? trim($input['image_url']) : '');

    try {
        $stmt = $pdo->prepare("INSERT INTO `berita_kegiatan` 
            (`id`, `judul`, `kategori`, `ringkasan`, `konten`, `penulis`, `tanggal`, `foto_url`)
            VALUES (:id, :judul, :kategori, :ringkasan, :konten, :penulis, :tanggal, :foto_url)
            ON DUPLICATE KEY UPDATE 
            `judul` = VALUES(`judul`),
            `kategori` = VALUES(`kategori`),
            `ringkasan` = VALUES(`ringkasan`),
            `konten` = VALUES(`konten`),
            `penulis` = VALUES(`penulis`),
            `tanggal` = VALUES(`tanggal`),
            `foto_url` = VALUES(`foto_url`)");

        $stmt->execute([
            ':id' => $id,
            ':judul' => $title,
            ':kategori' => $category,
            ':ringkasan' => $summary,
            ':konten' => $content,
            ':penulis' => $author,
            ':tanggal' => $date,
            ':foto_url' => $imageUrl
        ]);

        jsonResponse('success', 'Berita berhasil disimpan di database MySQL!', ['id' => $id]);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal menyimpan berita: ' . $e->getMessage(), null, 500);
    }
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
    }

    if (!$id) {
        jsonResponse('error', 'ID berita wajib diberikan', null, 400);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `berita_kegiatan` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        jsonResponse('success', 'Berita berhasil dihapus dari database MySQL');
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal menghapus berita: ' . $e->getMessage(), null, 500);
    }
}
