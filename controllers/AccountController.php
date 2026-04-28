<?php
/**
 * Account Controller - Manajemen Profil User
 * File: controllers/AccountController.php
 */

class AccountController {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function profil(): void {
        Auth::requireLogin();
        
        $user = $this->userModel->findById(Auth::id());
        if (!$user) redirect('?page=logout');
        
        require BASE_PATH . '/views/account/profil.php';
    }

    public function updateProfil(): void {
        Auth::requireLogin();
        
        if (!isPost()) redirect('?page=profil');
        verifyCsrf();

        $id       = Auth::id();
        $nama     = trim(post('nama'));
        $email    = trim(post('email'));
        $password = post('password');
        $passwordConfirm = post('password_confirm');

        $errors = [];

        if (empty($nama)) $errors[] = 'Nama wajib diisi.';
        if (empty($email)) $errors[] = 'Email wajib diisi.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
        elseif (!str_ends_with($email, '@uniku.ac.id')) $errors[] = 'Email wajib menggunakan domain @uniku.ac.id.';
        
        // Cek email duplikat
        if ($this->userModel->emailExists($email, $id)) {
            $errors[] = 'Email sudah digunakan oleh akun lain.';
        }

        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = 'Password minimal 6 karakter.';
            } elseif ($password !== $passwordConfirm) {
                $errors[] = 'Konfirmasi password tidak cocok.';
            }
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect('?page=profil');
        }

        $updateData = [
            'nama'  => $nama,
            'email' => $email
        ];

        // Handle Foto Upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = uploadAndCompressImage($_FILES['foto'], UPLOAD_PATH_AVATAR);
            if ($upload['success']) {
                $updateData['foto'] = $upload['filename'];
                
                // Hapus foto lama jika ada
                if (!empty($user['foto']) && file_exists(UPLOAD_PATH_AVATAR . '/' . $user['foto'])) {
                    unlink(UPLOAD_PATH_AVATAR . '/' . $user['foto']);
                }
            } else {
                setFlash('danger', $upload['error']);
                redirect('?page=profil');
            }
        }

        if (!empty($password)) {
            $updateData['password'] = $password;
        }

        if ($this->userModel->update($id, $updateData)) {
            // Update session data
            $_SESSION['user']['nama']  = $nama;
            $_SESSION['user']['email'] = $email;
            if (isset($updateData['foto'])) {
                $_SESSION['user']['foto'] = $updateData['foto'];
            }
            
            setFlash('success', 'Profil berhasil diperbarui.');
        } else {
            setFlash('danger', 'Gagal memperbarui profil.');
        }

        redirect('?page=profil');
    }
}
