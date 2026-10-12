<?php
/**
 * ==============================================================================
 * API PROFIL PUSKESMAS & DETEKSI FOTO KEPALA PUSKESMAS (dr. Dendy)
 * Puskesmas Kepulauan Seribu Selatan
 * ==============================================================================
 * Endpoint: /api/profile.php
 * Methods : GET, POST, PUT
 */

require_once __DIR__ . '/db.php';

$pdo = getDb();
$method = $_SERVER['REQUEST_METHOD'];

// Fungsi pemindai foto resmi dr. Dendy dari folder asset GitHub
function findDendyPhoto() {
    $root = dirname(__DIR__);
    $searchDirs = [
        ['dir' => $root . '/asset/gambar', 'url' => '/asset/gambar/'],
        ['dir' => $root . '/public/asset/gambar', 'url' => '/asset/gambar/'],
        ['dir' => $root . '/asset', 'url' => '/asset/'],
        ['dir' => $root . '/public/asset', 'url' => '/asset/'],
        ['dir' => $root . '/assets/images', 'url' => '/assets/images/'],
        ['dir' => $root . '/public/assets/images', 'url' => '/assets/images/']
    ];

    $priorityPatterns = [
        'dendy.jpg', 'dendy.jpeg', 'dendy.png', 'dendy.webp',
        'dr-dendy.jpg', 'dr-dendy.jpeg', 'dr-dendy.png', 'dr-dendy.webp',
        'dr_dendy.jpg', 'dr_dendy.png', 'foto-dendy.jpg', 'foto-dendy.png',
        'dendy_purnama.jpg', 'dendy_purnama.png',
        'dendy.svg', 'dr-dendy.svg',
        'kepala-puskesmas.jpg', 'kepala-puskesmas.png', 'kepala-puskesmas.svg'
    ];

    foreach ($searchDirs as $entry) {
        $folder = $entry['dir'];
        $prefix = $entry['url'];
        if (is_dir($folder)) {
            // Cek file spesifik
            foreach ($priorityPatterns as $file) {
                if (file_exists($folder . '/' . $file)) {
                    return $prefix . $file;
                }
            }

            // Pindai file apapun yang mengandung kata dendy
            $scanned = scandir($folder);
            foreach ($scanned as $f) {
                if ($f === '.' || $f === '..') continue;
                $low = strtolower($f);
                if (strpos($low, 'dendy') !== false && preg_match('/\.(jpg|jpeg|png|webp|svg)$/i', $f)) {
                    return $prefix . $f;
                }
            }
        }
    }

    return '/assets/kepala-puskesmas.svg';
}

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'get_profile';

    if ($action === 'detect_head_photo') {
        $detected = findDendyPhoto();
        jsonResponse('success', 'Foto kepala puskesmas berhasil dideteksi', [
            'photo_url' => $detected,
            'source' => 'github_asset'
        ]);
    }

    try {
        $stmt = $pdo->query("SELECT * FROM `profil_puskesmas` WHERE `id` = 1 LIMIT 1");
        $row = $stmt->fetch();

        $detectedPhoto = findDendyPhoto();

        if ($row) {
            $data = [
                'name' => $row['nama_puskesmas'],
                'subTitle' => $row['sub_judul'],
                'motto' => $row['motto'],
                'headOfPuskesmas' => $row['kepala_nama'],
                'headOfPuskesmasTitle' => $row['kepala_jabatan'],
                'headOfPuskesmasNIP' => $row['kepala_nip'],
                'headOfPuskesmasPhoto' => !empty($row['kepala_foto']) ? $row['kepala_foto'] : $detectedPhoto,
                'detectedPhotoFromAsset' => $detectedPhoto,
                'headSpeech' => $row['sambutan_kepala'],
                'vision' => $row['visi'],
                'missions' => !empty($row['misi_json']) ? json_decode($row['misi_json'], true) : [],
                'coreValues' => !empty($row['tata_nilai_json']) ? json_decode($row['tata_nilai_json'], true) : [],
                'address' => $row['alamat'],
                'phone' => $row['telepon'],
                'whatsapp' => $row['whatsapp'],
                'email' => $row['email'],
                'hotlineIGD' => $row['hotline_igd'],
                'hotlineAmbulance' => $row['hotline_ambulans'],
                'workingAreaDescription' => $row['deskripsi_wilayah']
            ];

            jsonResponse('success', 'Data profil puskesmas berhasil dimuat', $data);
        } else {
            // Fallback profil standar
            jsonResponse('success', 'Profil belum tersimpan di MySQL, menggunakan profil bawaan', [
                'headOfPuskesmas' => 'dr. Ignatius Dendy Purnama',
                'headOfPuskesmasPhoto' => $detectedPhoto,
                'detectedPhotoFromAsset' => $detectedPhoto
            ]);
        }
    } catch (PDOException $e) {
        $detectedPhoto = findDendyPhoto();
        jsonResponse('success', 'Koneksi database dalam proses persiapan', [
            'headOfPuskesmas' => 'dr. Ignatius Dendy Purnama',
            'headOfPuskesmasPhoto' => $detectedPhoto,
            'detectedPhotoFromAsset' => $detectedPhoto
        ]);
    }
}

if ($method === 'POST' || $method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        jsonResponse('error', 'Data input profil tidak valid', null, 400);
    }

    try {
        $fields = [];
        $params = [':id' => 1];

        if (isset($input['headOfPuskesmas'])) {
            $fields[] = "`kepala_nama` = :kepala_nama";
            $params[':kepala_nama'] = trim($input['headOfPuskesmas']);
        }
        if (isset($input['headOfPuskesmasTitle'])) {
            $fields[] = "`kepala_jabatan` = :kepala_jabatan";
            $params[':kepala_jabatan'] = trim($input['headOfPuskesmasTitle']);
        }
        if (isset($input['headOfPuskesmasNIP'])) {
            $fields[] = "`kepala_nip` = :kepala_nip";
            $params[':kepala_nip'] = trim($input['headOfPuskesmasNIP']);
        }
        if (isset($input['headOfPuskesmasPhoto'])) {
            $fields[] = "`kepala_foto` = :kepala_foto";
            $params[':kepala_foto'] = trim($input['headOfPuskesmasPhoto']);
        }
        if (isset($input['headSpeech'])) {
            $fields[] = "`sambutan_kepala` = :sambutan_kepala";
            $params[':sambutan_kepala'] = trim($input['headSpeech']);
        }
        if (isset($input['motto'])) {
            $fields[] = "`motto` = :motto";
            $params[':motto'] = trim($input['motto']);
        }
        if (isset($input['vision'])) {
            $fields[] = "`visi` = :visi";
            $params[':visi'] = trim($input['vision']);
        }
        if (isset($input['phone'])) {
            $fields[] = "`telepon` = :telepon";
            $params[':telepon'] = trim($input['phone']);
        }
        if (isset($input['whatsapp'])) {
            $fields[] = "`whatsapp` = :whatsapp";
            $params[':whatsapp'] = trim($input['whatsapp']);
        }
        if (isset($input['email'])) {
            $fields[] = "`email` = :email";
            $params[':email'] = trim($input['email']);
        }

        if (!empty($fields)) {
            $sql = "UPDATE `profil_puskesmas` SET " . implode(", ", $fields) . " WHERE `id` = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }

        jsonResponse('success', 'Profil Puskesmas berhasil disimpan di database MySQL!');
    } catch (PDOException $e) {
        jsonResponse('error', 'Gagal memperbarui profil: ' . $e->getMessage(), null, 500);
    }
}
