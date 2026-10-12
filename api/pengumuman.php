<?php
/**
 * ==============================================================================
 * API PENGUMUMAN & REKRUTMEN RESMI - MYSQL HOSTINGER
 * Puskesmas Kepulauan Seribu Selatan
 * ==============================================================================
 * Endpoint: /api/pengumuman.php
 * Methods : GET, POST, PUT, DELETE
 */

require_once __DIR__ . '/db.php';

$pdo = getDb();
$method = $_SERVER['REQUEST_METHOD'];

// Pastikan tabel pengumuman ada
function ensurePengumumanTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS `pengumuman` (
        `id` VARCHAR(50) NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `category` VARCHAR(100) NOT NULL DEFAULT 'Pengumuman Resmi',
        `urgency` VARCHAR(50) NOT NULL DEFAULT 'Biasa',
        `date` VARCHAR(50) NOT NULL,
        `valid_until` VARCHAR(50) DEFAULT NULL,
        `official_number` VARCHAR(100) NOT NULL DEFAULT 'PKM.KSS/OFFICIAL/2026',
        `author` VARCHAR(150) NOT NULL DEFAULT 'Puskesmas Kepulauan Seribu Selatan',
        `summary` TEXT DEFAULT NULL,
        `content` LONGTEXT NOT NULL,
        `positions_json` JSON DEFAULT NULL,
        `requirements_json` JSON DEFAULT NULL,
        `documents_json` JSON DEFAULT NULL,
        `contact_person_json` JSON DEFAULT NULL,
        `file_attachment_json` JSON DEFAULT NULL,
        `is_urgent_banner` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);

    // Jika tabel kosong, masukkan data awal
    $count = (int)$pdo->query("SELECT COUNT(*) FROM `pengumuman`")->fetchColumn();
    if ($count === 0) {
        $positions = json_encode([
            [
                'name' => 'Perawat Pelaksana / Terampil',
                'quota' => '3 Orang',
                'qualification' => 'D-III / S-1 Ners Keperawatan, STR Aktif, Bersedia dinas rawat inap 24 jam'
            ],
            [
                'name' => 'Bidan Pelaksana',
                'quota' => '2 Orang',
                'qualification' => 'D-III / D-IV Kebidanan, STR Aktif, Kompeten dalam penanganan kegawatdaruratan maternal neonatal'
            ],
            [
                'name' => 'Pranata Laboratorium Kesehatan (ATLM)',
                'quota' => '1 Orang',
                'qualification' => 'D-III Analis Kesehatan / Laboratorium Medis, STR Aktif, Mampu operasional hematologi analizer & mikroskopis'
            ],
            [
                'name' => 'Tenaga Sanitasi Lingkungan (Kesling)',
                'quota' => '1 Orang',
                'qualification' => 'D-III / S-1 Kesehatan Lingkungan / Sanitasi, Memahami pengelolaan limbah B3 medis & pengawasan air bersih'
            ]
        ], JSON_UNESCAPED_UNICODE);

        $requirements = json_encode([
            'Warga Negara Indonesia (WNI) berusia minimal 20 tahun dan maksimal 35 tahun pada saat pendaftaran.',
            'Memiliki kualifikasi pendidikan sesuai dengan formasi yang dilamar dari institusi terakreditasi minimal B.',
            'Memiliki Surat Tanda Registrasi (STR) yang masih berlaku (minimal 6 bulan masa aktif) yang diterbitkan Kemenkes RI / KTKI.',
            'Sehat jasmani dan rohani, tidak memiliki riwayat mabuk laut berat, serta bersedia ditempatkan di pulau pemukiman.',
            'Memiliki sertifikat pelatihan keahlian (BTCLS bagi perawat, APN/MU bagi bidan) merupakan nilai tambah.',
            'Berkelakuan baik dan tidak pernah diberhentikan dengan tidak hormat dari instansi pemerintah/swasta.'
        ], JSON_UNESCAPED_UNICODE);

        $documents = json_encode([
            'Surat Lamaran Pekerjaan ditujukan kepada Kepala Puskesmas Kepulauan Seribu Selatan.',
            'Curriculum Vitae (CV) lengkap dengan riwayat pengalaman kerja / magang.',
            'Salinan Ijazah dan Transkrip Nilai yang telah dilegalisir.',
            'Salinan STR yang masih berlaku.',
            'Salinan KTP elektronik dan Kartu Keluarga (KK).',
            'Surat Keterangan Sehat dari fasilitas pelayanan kesehatan pemerintah.',
            'Pasfoto terbaru ukuran 4x6 latar belakang warna merah (format JPG/PDF).'
        ], JSON_UNESCAPED_UNICODE);

        $contact = json_encode([
            'name' => 'Panitia Seleksi Pegawai Puskesmas Kepulauan Seribu Selatan',
            'phone' => '0813-1818-7299',
            'email' => 'rekrutmen.puskesmas1000@jakarta.go.id'
        ], JSON_UNESCAPED_UNICODE);

        $attachment = json_encode([
            'name' => 'Pengumuman_Rekrutmen_Nakes_PKM_KSS_2026.pdf',
            'size' => '2.4 MB'
        ], JSON_UNESCAPED_UNICODE);

        $ins = $pdo->prepare("INSERT INTO `pengumuman` 
            (`id`, `title`, `category`, `urgency`, `date`, `valid_until`, `official_number`, `author`, `summary`, `content`, `positions_json`, `requirements_json`, `documents_json`, `contact_person_json`, `file_attachment_json`, `is_urgent_banner`)
            VALUES 
            (:id, :title, :category, :urgency, :date, :valid_until, :official_number, :author, :summary, :content, :positions_json, :requirements_json, :documents_json, :contact_person_json, :file_attachment_json, :is_urgent_banner)");

        $ins->execute([
            ':id' => 'ann-1',
            ':title' => 'Penerimaan Tenaga Kesehatan Non-PNS / PJLP Puskesmas Kepulauan Seribu Selatan Formasi Tahun 2026',
            ':category' => 'Rekrutmen',
            ':urgency' => 'Tinggi',
            ':date' => '05 Oktober 2026',
            ':valid_until' => '28 Oktober 2026',
            ':official_number' => '800/042/PKM.KSS/X/2026',
            ':author' => 'Sub Bagian Tata Usaha & Tim Seleksi Rekrutmen',
            ':summary' => 'Puskesmas Kepulauan Seribu Selatan membuka kesempatan berkarir bagi putra-putri terbaik bangsa untuk bergabung sebagai tenaga kesehatan dan penunjang medis kepulauan.',
            ':content' => 'Dalam rangka penguatan Integrasi Layanan Primer (ILP) dan pemenuhan standar ketenagaan di Puskesmas Induk Pulau Tidung serta Jejaring Pustu Pulau Lancang, Pulau Pari, dan Pulau Untung Jawa, Puskesmas Kepulauan Seribu Selatan membuka seleksi penerimaan tenaga kontrak dengan dedikasi tinggi mengabdi di wilayah kepulauan bahari.',
            ':positions_json' => $positions,
            ':requirements_json' => $requirements,
            ':documents_json' => $documents,
            ':contact_person_json' => $contact,
            ':file_attachment_json' => $attachment,
            ':is_urgent_banner' => 0
        ]);
    }
}

try {
    ensurePengumumanTable($pdo);
} catch (Exception $e) {
    // Abaikan jika database berizin terbatas
}

// ------------------------------------------------------------------------------
// GET: Ambil daftar pengumuman
// ------------------------------------------------------------------------------
if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM `pengumuman` ORDER BY `is_urgent_banner` DESC, `created_at` DESC");
        $rows = $stmt->fetchAll();

        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'category' => $row['category'],
                'urgency' => $row['urgency'],
                'date' => $row['date'],
                'validUntil' => $row['valid_until'] ?? null,
                'officialNumber' => $row['official_number'] ?? 'PKM.KSS/OFFICIAL/2026',
                'author' => $row['author'] ?? 'Puskesmas Kepulauan Seribu Selatan',
                'summary' => $row['summary'] ?? '',
                'content' => $row['content'],
                'isUrgentBanner' => (bool)$row['is_urgent_banner'],
                'positions' => !empty($row['positions_json']) ? json_decode($row['positions_json'], true) : undefined,
                'requirements' => !empty($row['requirements_json']) ? json_decode($row['requirements_json'], true) : undefined,
                'documents' => !empty($row['documents_json']) ? json_decode($row['documents_json'], true) : undefined,
                'contactPerson' => !empty($row['contact_person_json']) ? json_decode($row['contact_person_json'], true) : undefined,
                'fileAttachment' => !empty($row['file_attachment_json']) ? json_decode($row['file_attachment_json'], true) : undefined
            ];
        }

        jsonResponse('success', 'Data pengumuman berhasil dimuat dari database MySQL', $items);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal memuat pengumuman: ' . $e->getMessage(), null, 500);
    }
}

// ------------------------------------------------------------------------------
// POST: Tambah pengumuman baru
// ------------------------------------------------------------------------------
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['title']) || empty($input['content'])) {
        jsonResponse('error', 'Judul dan isi pengumuman wajib diisi.', null, 400);
    }

    $id = !empty($input['id']) ? $input['id'] : ('ann-' . time());
    $title = trim($input['title']);
    $category = !empty($input['category']) ? trim($input['category']) : 'Pengumuman Resmi';
    $urgency = !empty($input['urgency']) ? trim($input['urgency']) : 'Biasa';
    $date = !empty($input['date']) ? trim($input['date']) : date('d F Y');
    $validUntil = !empty($input['validUntil']) ? trim($input['validUntil']) : (!empty($input['valid_until']) ? trim($input['valid_until']) : null);
    $officialNumber = !empty($input['officialNumber']) ? trim($input['officialNumber']) : (!empty($input['official_number']) ? trim($input['official_number']) : 'PKM.KSS/OFFICIAL/2026');
    $author = !empty($input['author']) ? trim($input['author']) : 'Puskesmas Kepulauan Seribu Selatan';
    $summary = !empty($input['summary']) ? trim($input['summary']) : substr($title, 0, 140);
    $content = trim($input['content']);
    $isUrgent = !empty($input['isUrgentBanner']) ? 1 : 0;

    $positionsJson = !empty($input['positions']) ? json_encode($input['positions'], JSON_UNESCAPED_UNICODE) : null;
    $requirementsJson = !empty($input['requirements']) ? json_encode($input['requirements'], JSON_UNESCAPED_UNICODE) : null;
    $documentsJson = !empty($input['documents']) ? json_encode($input['documents'], JSON_UNESCAPED_UNICODE) : null;
    $contactJson = !empty($input['contactPerson']) ? json_encode($input['contactPerson'], JSON_UNESCAPED_UNICODE) : null;
    $attachmentJson = !empty($input['fileAttachment']) ? json_encode($input['fileAttachment'], JSON_UNESCAPED_UNICODE) : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO `pengumuman` 
            (`id`, `title`, `category`, `urgency`, `date`, `valid_until`, `official_number`, `author`, `summary`, `content`, `positions_json`, `requirements_json`, `documents_json`, `contact_person_json`, `file_attachment_json`, `is_urgent_banner`)
            VALUES 
            (:id, :title, :category, :urgency, :date, :valid_until, :official_number, :author, :summary, :content, :positions_json, :requirements_json, :documents_json, :contact_person_json, :file_attachment_json, :is_urgent_banner)
            ON DUPLICATE KEY UPDATE 
            `title` = VALUES(`title`),
            `category` = VALUES(`category`),
            `urgency` = VALUES(`urgency`),
            `date` = VALUES(`date`),
            `valid_until` = VALUES(`valid_until`),
            `official_number` = VALUES(`official_number`),
            `author` = VALUES(`author`),
            `summary` = VALUES(`summary`),
            `content` = VALUES(`content`),
            `positions_json` = VALUES(`positions_json`),
            `requirements_json` = VALUES(`requirements_json`),
            `documents_json` = VALUES(`documents_json`),
            `contact_person_json` = VALUES(`contact_person_json`),
            `file_attachment_json` = VALUES(`file_attachment_json`),
            `is_urgent_banner` = VALUES(`is_urgent_banner`)");

        $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':category' => $category,
            ':urgency' => $urgency,
            ':date' => $date,
            ':valid_until' => $validUntil,
            ':official_number' => $officialNumber,
            ':author' => $author,
            ':summary' => $summary,
            ':content' => $content,
            ':positions_json' => $positionsJson,
            ':requirements_json' => $requirementsJson,
            ':documents_json' => $documentsJson,
            ':contact_person_json' => $contactJson,
            ':file_attachment_json' => $attachmentJson,
            ':is_urgent_banner' => $isUrgent
        ]);

        jsonResponse('success', 'Pengumuman berhasil disimpan di database MySQL!', [
            'id' => $id,
            'title' => $title
        ]);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal menyimpan pengumuman ke database MySQL: ' . $e->getMessage(), null, 500);
    }
}

// ------------------------------------------------------------------------------
// PUT: Update pengumuman
// ------------------------------------------------------------------------------
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || empty($input['id'])) {
        jsonResponse('error', 'ID pengumuman wajib disertakan untuk pembaruan.', null, 400);
    }

    $id = $input['id'];
    $fields = [];
    $params = [':id' => $id];

    if (isset($input['title'])) {
        $fields[] = "`title` = :title";
        $params[':title'] = trim($input['title']);
    }
    if (isset($input['category'])) {
        $fields[] = "`category` = :category";
        $params[':category'] = trim($input['category']);
    }
    if (isset($input['urgency'])) {
        $fields[] = "`urgency` = :urgency";
        $params[':urgency'] = trim($input['urgency']);
    }
    if (isset($input['date'])) {
        $fields[] = "`date` = :date";
        $params[':date'] = trim($input['date']);
    }
    if (array_key_exists('validUntil', $input) || array_key_exists('valid_until', $input)) {
        $fields[] = "`valid_until` = :valid_until";
        $params[':valid_until'] = $input['validUntil'] ?? $input['valid_until'] ?? null;
    }
    if (isset($input['officialNumber']) || isset($input['official_number'])) {
        $fields[] = "`official_number` = :official_number";
        $params[':official_number'] = trim($input['officialNumber'] ?? $input['official_number']);
    }
    if (isset($input['author'])) {
        $fields[] = "`author` = :author";
        $params[':author'] = trim($input['author']);
    }
    if (isset($input['summary'])) {
        $fields[] = "`summary` = :summary";
        $params[':summary'] = trim($input['summary']);
    }
    if (isset($input['content'])) {
        $fields[] = "`content` = :content";
        $params[':content'] = trim($input['content']);
    }
    if (array_key_exists('isUrgentBanner', $input)) {
        $fields[] = "`is_urgent_banner` = :is_urgent_banner";
        $params[':is_urgent_banner'] = $input['isUrgentBanner'] ? 1 : 0;
    }
    if (array_key_exists('positions', $input)) {
        $fields[] = "`positions_json` = :positions_json";
        $params[':positions_json'] = !empty($input['positions']) ? json_encode($input['positions'], JSON_UNESCAPED_UNICODE) : null;
    }
    if (array_key_exists('requirements', $input)) {
        $fields[] = "`requirements_json` = :requirements_json";
        $params[':requirements_json'] = !empty($input['requirements']) ? json_encode($input['requirements'], JSON_UNESCAPED_UNICODE) : null;
    }
    if (array_key_exists('documents', $input)) {
        $fields[] = "`documents_json` = :documents_json";
        $params[':documents_json'] = !empty($input['documents']) ? json_encode($input['documents'], JSON_UNESCAPED_UNICODE) : null;
    }
    if (array_key_exists('contactPerson', $input)) {
        $fields[] = "`contact_person_json` = :contact_person_json";
        $params[':contact_person_json'] = !empty($input['contactPerson']) ? json_encode($input['contactPerson'], JSON_UNESCAPED_UNICODE) : null;
    }
    if (array_key_exists('fileAttachment', $input)) {
        $fields[] = "`file_attachment_json` = :file_attachment_json";
        $params[':file_attachment_json'] = !empty($input['fileAttachment']) ? json_encode($input['fileAttachment'], JSON_UNESCAPED_UNICODE) : null;
    }

    if (empty($fields)) {
        jsonResponse('error', 'Tidak ada data yang diubah.', null, 400);
    }

    try {
        $sql = "UPDATE `pengumuman` SET " . implode(", ", $fields) . " WHERE `id` = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        jsonResponse('success', 'Pengumuman berhasil diperbarui di database MySQL.', ['id' => $id]);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal memperbarui pengumuman di database: ' . $e->getMessage(), null, 500);
    }
}

// ------------------------------------------------------------------------------
// DELETE: Hapus pengumuman
// ------------------------------------------------------------------------------
if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    if (!$id) {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;
    }

    if (!$id) {
        jsonResponse('error', 'ID pengumuman wajib disertakan untuk dihapus.', null, 400);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `pengumuman` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);

        jsonResponse('success', 'Pengumuman berhasil dihapus dari database MySQL.', ['id' => $id]);
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal menghapus pengumuman dari database: ' . $e->getMessage(), null, 500);
    }
}
