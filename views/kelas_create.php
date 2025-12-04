<div class="card-custom mb-4">
    <h5 class="mb-0">Tambah Kelas</h5>
</div>

<div class="card-custom">
    <form action="?page=kelas_store" method="POST">

        <div class="mb-3">
            <label class="form-label">Kode Kelas</label>
            <input type="text" name="kode_kelas" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Kelas</label>
            <input type="text" name="nama_kelas" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Wali Kelas</label>
            <input type="text" name="wali_kelas" class="form-control" required>
        </div>

        <button class="btn btn-primary">
            <i class="bi bi-check2-circle"></i> Simpan
        </button>

        <a href="?page=kelas_list" class="btn btn-secondary">Kembali</a>
    </form>
</div>
