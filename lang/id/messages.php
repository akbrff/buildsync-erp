<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification & Flash Messages (Indonesian)
    |--------------------------------------------------------------------------
    */
    // Users
    'user_created' => 'Pengguna baru berhasil ditambahkan.',
    'user_updated' => 'Data pengguna berhasil diperbarui.',
    'user_role_updated' => 'Role pengguna diperbarui.',
    'user_activated' => 'Akun pengguna berhasil diaktifkan kembali.',
    'user_deactivated' => 'Akun pengguna berhasil dinonaktifkan.',
    'user_password_reset' => 'Kata sandi untuk :name berhasil direset.',
    'user_deleted' => 'Akun pengguna berhasil dihapus dari sistem.',

    // Protections
    'cannot_demote_primary_admin' => 'Peran akun Administrator utama tidak dapat diubah.',
    'cannot_demote_self' => 'Tidak dapat menurunkan role akun sendiri.',
    'cannot_deactivate_primary_admin' => 'Akun Administrator utama tidak dapat dinonaktifkan.',
    'cannot_deactivate_self' => 'Tidak dapat menonaktifkan akun sendiri.',
    'cannot_delete_primary_admin' => 'Akun Administrator utama tidak dapat dihapus.',
    'cannot_delete_self' => 'Tidak dapat menghapus akun sendiri.',
    'last_active_admin_required' => 'Minimal harus ada satu akun Administrator yang aktif.',

    // Projects
    'project_created' => 'Data proyek berhasil ditambahkan.',
    'project_updated' => 'Data proyek berhasil diperbarui.',
    'project_deleted' => 'Data proyek berhasil dihapus.',
    'project_cannot_delete_has_schedules' => 'Proyek tidak dapat dihapus karena masih memiliki Jadwal Pekerjaan. Hapus atau selesaikan data jadwal terlebih dahulu.',
    'project_cannot_delete_has_budgets' => 'Proyek tidak dapat dihapus karena masih memiliki catatan anggaran. Pertahankan riwayat biaya proyek.',

    // Schedules
    'schedule_created' => 'Jadwal pekerjaan berhasil ditambahkan.',
    'schedule_updated' => 'Jadwal pekerjaan berhasil diperbarui.',
    'schedule_deleted' => 'Jadwal pekerjaan berhasil dihapus.',
    'schedule_cannot_delete_has_progress' => 'Jadwal tidak dapat dihapus karena memiliki riwayat progres. Riwayat tersebut harus dipertahankan sebagai catatan proyek.',
    'progress_recorded' => 'Progres berhasil dicatat.',

    // Materials
    'material_created' => 'Material baru berhasil ditambahkan ke inventaris.',
    'material_updated' => 'Data material [:code] berhasil diperbarui.',
    'material_deleted' => 'Material [:code] berhasil dihapus dari inventaris.',
    'material_has_transactions' => 'Material memiliki riwayat transaksi dan tidak dapat dihapus.',
    'insufficient_stock' => 'Gagal mengeluarkan material! Stok fisik hanya tersisa :stock :unit, permintaan: :requested :unit.',
    'stock_adjusted' => 'Mutasi stok dan histori inventaris berhasil dicatat.',

    // Procurement
    'supplier_created' => 'Supplier berhasil ditambahkan.',
    'supplier_updated' => 'Data supplier berhasil diperbarui.',
    'po_created' => 'PO dibuat. Stok baru bertambah setelah penerimaan barang.',
    'po_received' => 'Penerimaan barang dan mutasi stok tercatat.',
    'po_paid' => 'Pembayaran supplier tercatat di arus kas.',

    // Workforce
    'worker_created' => 'Tenaga kerja baru [:code] :name berhasil ditambahkan.',
    'worker_updated' => 'Data tenaga kerja [:code] :name berhasil diperbarui.',
    'worker_deleted' => 'Tenaga kerja [:code] :name berhasil dihapus dari sistem.',
    'assignment_created' => 'Penugasan pekerja tersimpan.',
    'attendance_recorded' => 'Kehadiran dan biaya aktual tenaga kerja dicatat.',
    'wages_paid' => 'Pembayaran upah masuk ke arus kas.',

    // Finance & Budgets
    'cash_flow_created' => 'Arus kas berhasil dicatat.',
    'expense_created' => 'Pengeluaran biaya berhasil dicatat.',
    'expense_updated' => 'Data pengeluaran biaya berhasil diperbarui.',
    'expense_deleted' => 'Catatan pengeluaran biaya berhasil dihapus.',
];
