<?php
/**
 * ==============================================================================
 * API AGENDA KEGIATAN KESEHATAN - MYSQL HOSTINGER
 * Puskesmas Kepulauan Seribu Selatan
 * ==============================================================================
 * Endpoint: /api/agenda.php
 * Methods : GET, POST, PUT, DELETE
 */

require_once __DIR__ . '/db.php';

$pdo = getDb();
$method = $_SERVER['REQUEST_METHOD'];

function ensureAgendaTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS `agenda_kegiatan` (
        `id` VARCHAR(50) NOT NULL,
        `judul` VARCHAR(255) NOT NULL,
        `tanggal` VARCHAR(50) NOT NULL,
        `waktu` VARCHAR(100) NOT NULL,
        `lokasi` VARCHAR(255) NOT NULL,
        `pelaksana` VARCHAR(150) NOT NULL,
        `target_peserta` VARCHAR(150) DEFAULT NULL,
        `status` VARCHAR(50) NOT NULL DEFAULT 'Mendatang',
        `deskripsi` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
}

try {
    ensureAgendaTable($pdo);
} catch (Exception $e) {}

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM `agenda_kegiatan` ORDER BY `created_at` DESC");
        $rows = $stmt->fetchAll();

        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id' => $row['id'],
                'title' => $row['judul'],
                'date' => $row['tanggal'],
                'time' => $row['waktu'],
                'location' => $row['lokasi'],
                'organizer' => $row['pelaksana'],
                'targetAudience' => $row['target_peserta'],
                'status' => $row['status'],
                'description' => $row['deskripsi']
            ];
        }

        jsonResponse('success', 'Data agenda berhasil dimuat dari database', $items);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal memuat agenda: ' . $e->getMessage(), null, 500);
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['title']) || empty($input['date'])) {
        jsonResponse('error', 'Judul dan tanggal agenda wajib diisi.', null, 400);
    }

    $id = !empty($input['id']) ? $input['id'] : ('ev-' . time());
    $title = trim($input['title']);
    $date = trim($input['date']);
    $time = !empty($input['time']) ? trim($input['time']) : '08:00 - Selesai';
    $location = !empty($input['location']) ? trim($input['location']) : 'Puskesmas Kepulauan Seribu Selatan';
    $organizer = !empty($input['organizer']) ? trim($input['organizer']) : 'Tim Puskesmas';
    $target = !empty($input['targetAudience']) ? trim($input['targetAudience']) : '';
    $status = !empty($input['status']) ? trim($input['status']) : 'Mendatang';
    $desc = !empty($input['description']) ? trim($input['description']) : '';

    try {
        $stmt = $pdo->prepare("INSERT INTO `agenda_kegiatan` 
            (`id`, `judul`, `tanggal`, `waktu`, `lokasi`, `pelaksana`, `target_peserta`, `status`, `deskripsi`)
            VALUES (:id, :judul, :tanggal, :waktu, :lokasi, :pelaksana, :target_peserta, :status, :deskripsi)
            ON DUPLICATE KEY UPDATE 
            `judul` = VALUES(`judul`),
            `tanggal` = VALUES(`tanggal`),
            `waktu` = VALUES(`waktu`),
            `lokasi` = VALUES(`lokasi`),
            `pelaksana` = VALUES(`pelaksana`),
            `target_peserta` = VALUES(`target_peserta`),
            `status` = VALUES(`status`),
            `deskripsi` = VALUES(`deskripsi`)");

        $stmt->execute([
            ':id' => $id,
            ':judul' => $title,
            ':tanggal' => $date,
            ':waktu' => $time,
            ':lokasi' => $location,
            ':pelaksana' => $organizer,
            ':target_peserta' => $target,
            ':status' => $status,
            ':deskripsi' => $desc
        ]);

        jsonResponse('success', 'Agenda berhasil disimpan di database MySQL!', ['id' => $id]);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal menyimpan agenda: ' . $e->getMessage(), null, 500);
    }
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
    }

    if (!$id) {
        jsonResponse('error', 'ID agenda wajib diberikan', null, 400);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `agenda_kegiatan` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        jsonResponse('success', 'Agenda berhasil dihapus dari database MySQL');
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal menghapus agenda: ' . $e->getMessage(), null, 500);
    }
}
