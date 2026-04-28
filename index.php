<?php
/**
 * ============================================================
 * SiTiTi — Front Controller / Router Utama
 * File: index.php
 *
 * Semua request masuk melalui file ini.
 * Routing menggunakan query string: ?page=nama_halaman
 * ============================================================
 */

// ── Bootstrap ──────────────────────────────────────────────
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Helper.php';

// ── Autoload Models ──────────────────────────────────────────
$modelFiles = [
    'User', 'Mahasiswa', 'Dosen', 'Pengajuan',
    'Pembimbing', 'StatusLog', 'Notifikasi', 'Periode', 'CatatanBimbingan'
];
foreach ($modelFiles as $model) {
    require_once __DIR__ . "/models/{$model}.php";
}

// ── Autoload Controllers ──────────────────────────────────────
$controllerFiles = [
    'AuthController', 'DashboardController', 'PengajuanController',
    'StaffController', 'KaprodiController', 'DewanController', 'AdminController', 'AccountController',
    'SuperadminController', 'ExportController', 'DosenController'
];
foreach ($controllerFiles as $ctrl) {
    require_once __DIR__ . "/controllers/{$ctrl}.php";
}

// ── Mulai Sesi ───────────────────────────────────────────────
Auth::startSession();

// ── Router ───────────────────────────────────────────────────
$page = get('page', 'dashboard');

// Halaman publik (tidak perlu login)
$publicPages = ['login', 'login.post'];

if (!in_array($page, $publicPages) && !Auth::check()) {
    redirect('?page=login');
}

// Cek hak akses dinamis (berdasarkan tabel menus dan role_menus)
if (!in_array($page, $publicPages)) {
    Auth::requireMenuAccess($page);
}

// ── Dispatch ke controller yang sesuai ───────────────────────
switch ($page) {

    // ── AUTH ──────────────────────────────────────────────────
    case 'login':
        (new AuthController())->showLogin();
        break;

    case 'login.post':
        (new AuthController())->login();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'switch-role':
        Auth::requireLogin();
        $newRole = get('role');
        if ($newRole && Auth::switchRole($newRole)) {
            setFlash('success', "Peran berhasil diganti ke: " . ucfirst($newRole));
        }
        redirect('?page=dashboard');
        break;

    // ── PROFIL ──────────────────────────────────────────────────
    case 'profil':
        (new AccountController())->profil();
        break;

    case 'profil.update':
        (new AccountController())->updateProfil();
        break;

    // ── DOSEN ────────────────────────────────────────────────
    case 'dosen.bimbingan':
        (new DosenController())->bimbingan();
        break;
    case 'dosen.bimbingan.show':
        (new DosenController())->showBimbingan();
        break;
    case 'dosen.bimbingan.catatan':
        (new DosenController())->storeCatatan();
        break;


    // ── DASHBOARD ─────────────────────────────────────────────
    case 'dashboard':
        (new DashboardController())->index();
        break;

    // ── PENGAJUAN (Mahasiswa) ─────────────────────────────────
    case 'pengajuan':
        Auth::requireLogin();
        if (Auth::role() === ROLE_MAHASISWA) {
            (new PengajuanController())->index();
        } else {
            // Role lain redirect ke halaman mereka
            redirect('?page=dashboard');
        }
        break;

    case 'pengajuan.dosen':
        (new PengajuanController())->dosenList();
        break;

    case 'pengajuan.create':
        (new PengajuanController())->create();
        break;

    case 'pengajuan.store':
        (new PengajuanController())->store();
        break;

    case 'pengajuan.show':
        (new PengajuanController())->show();
        break;

    case 'pengajuan.edit':
        (new PengajuanController())->edit();
        break;

    case 'pengajuan.update':
        (new PengajuanController())->update();
        break;

    case 'pengajuan.submit':
        (new PengajuanController())->submit();
        break;

    case 'pengajuan.delete':
        (new PengajuanController())->delete();
        break;

    // ── STAFF ─────────────────────────────────────────────────
    case 'staff':
        (new StaffController())->index();
        break;

    case 'staff.verifikasi':
        (new StaffController())->verifikasi();
        break;

    case 'staff.proses':
        (new StaffController())->prosesVerifikasi();
        break;

    case 'staff.siap_fakultas':
        (new StaffController())->siapFakultas();
        break;

    case 'staff.mark_selesai':
        (new StaffController())->markSelesai();
        break;

    case 'staff.undo_selesai':
        (new StaffController())->undoSelesai();
        break;

    case 'staff.mark_selesai_fakultas':
        (new StaffController())->markSelesaiFakultas();
        break;

    case 'staff.undo_selesai_fakultas':
        (new StaffController())->undoSelesaiFakultas();
        break;

    case 'staff.mark_sk_terbit':
        (new StaffController())->markSKTerbit();
        break;

    case 'staff.undo_sk_terbit':
        (new StaffController())->undoSKTerbit();
        break;

    // ── KAPRODI ───────────────────────────────────────────────
    case 'kaprodi':
        (new KaprodiController())->index();
        break;

    case 'kaprodi.history':
        (new KaprodiController())->history();
        break;

    case 'kaprodi.review':
        (new KaprodiController())->review();
        break;

    case 'kaprodi.proses':
        (new KaprodiController())->prosesReview();
        break;

    // ── DEWAN ─────────────────────────────────────────────────
    case 'dewan':
        (new DewanController())->index();
        break;

    case 'dewan.history':
        (new DewanController())->history();
        break;

    case 'dewan.pembimbing':
        (new DewanController())->pembimbing();
        break;

    case 'dewan.proses':
        (new DewanController())->prosesPembimbing();
        break;

    // ── ADMIN / DATA MASTER ───────────────────────────────────
    case 'admin.mahasiswa':
        (new AdminController())->mahasiswa();
        break;

    case 'admin.mahasiswa.store':
        (new AdminController())->mahasiswaStore();
        break;

    case 'admin.mahasiswa.update':
        (new AdminController())->mahasiswaUpdate();
        break;

    case 'admin.mahasiswa.delete':
        (new AdminController())->mahasiswaDelete();
        break;

    case 'admin.mahasiswa.import':
        (new AdminController())->importMahasiswa();
        break;

    case 'admin.dosen':
        (new AdminController())->dosen();
        break;

    case 'admin.dosen.store':
        (new AdminController())->dosenStore();
        break;

    case 'admin.dosen.update':
        (new AdminController())->dosenUpdate();
        break;

    case 'admin.dosen.delete':
        (new AdminController())->dosenDelete();
        break;

    case 'admin.staff':
        (new AdminController())->staff();
        break;

    case 'admin.staff.store':
        (new AdminController())->staffStore();
        break;

    case 'admin.staff.update':
        (new AdminController())->staffUpdate();
        break;

    case 'admin.staff.delete':
        (new AdminController())->staffDelete();
        break;

    case 'admin.dosen.import':
        (new AdminController())->importDosen();
        break;

    case 'admin.periode':
        (new AdminController())->periode();
        break;

    case 'admin.periode.store':
        (new AdminController())->periodeStore();
        break;

    case 'admin.periode.update':
        (new AdminController())->periodeUpdate();
        break;

    case 'admin.periode.set_active':
        (new AdminController())->periodeSetActive();
        break;

    case 'admin.periode.delete':
        (new AdminController())->periodeDelete();
        break;

    // ── SUPER ADMIN ───────────────────────────────────────────
    case 'superadmin.roles':
        (new SuperadminController())->roles();
        break;

    case 'superadmin.roles.store':
        (new SuperadminController())->roleStore();
        break;

    case 'superadmin.roles.delete':
        (new SuperadminController())->roleDelete();
        break;

    case 'superadmin.menus':
        (new SuperadminController())->menus();
        break;

    case 'superadmin.menus.store':
        (new SuperadminController())->menuStore();
        break;

    case 'superadmin.menus.delete':
        (new SuperadminController())->menuDelete();
        break;

    case 'superadmin.role_menus':
        (new SuperadminController())->roleMenus();
        break;

    case 'superadmin.role_menus.update':
        (new SuperadminController())->roleMenusUpdate();
        break;

    case 'superadmin.database':
        (new SuperadminController())->database();
        break;

    // ── EXPORT ────────────────────────────────────────────────
    case 'export.excel':
        (new ExportController())->excel();
        break;

    case 'superadmin.database.backup':
        (new SuperadminController())->backupDatabase();
        break;

    // ── NOTIFIKASI ────────────────────────────────────────────
    case 'notif':
        Auth::requireLogin();
        $notifModel = new Notifikasi();
        $notifList  = $notifModel->getByUserId(Auth::id(), 50);
        $pageTitle  = 'Notifikasi';
        require __DIR__ . '/views/notif/index.php';
        break;

    case 'notif.read':
        Auth::requireLogin();
        $nid = (int)get('id');
        $ref = (int)get('ref');
        (new Notifikasi())->markRead($nid, Auth::id());
        
        $target = '?page=notif';
        if ($ref) {
            $role = Auth::role();
            if ($role === ROLE_STAFF) {
                $target = '?page=staff.verifikasi&id=' . $ref;
            } elseif ($role === ROLE_KAPRODI) {
                $target = '?page=kaprodi.review&id=' . $ref;
            } elseif ($role === ROLE_DEWAN) {
                $target = '?page=dewan.pembimbing&id=' . $ref;
            } else {
                // Mahasiswa atau Superadmin
                $target = '?page=pengajuan.show&id=' . $ref;
            }
        }
        redirect($target);
        break;

    case 'notif.readall':
        Auth::requireLogin();
        (new Notifikasi())->markAllRead(Auth::id());
        setFlash('success', 'Semua notifikasi ditandai sudah dibaca.');
        redirect('?page=notif');
        break;

    // ── ERROR PAGES ───────────────────────────────────────────
    case '403':
        http_response_code(403);
        $pageTitle = 'Akses Ditolak';
        require __DIR__ . '/views/errors/403.php';
        break;

    // ── DEFAULT (halaman tidak ditemukan) ─────────────────────
    default:
        http_response_code(404);
        $pageTitle = 'Halaman Tidak Ditemukan';
        // Redirect ke dashboard jika sudah login
        setFlash('warning', "Halaman '{$page}' tidak ditemukan.");
        redirect('?page=dashboard');
}
