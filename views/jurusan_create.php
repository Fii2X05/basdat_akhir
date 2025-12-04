<div class="card-custom mb-4">
    <h5 class="mb-0">Tambah Jurusan</h5>
</div>

<div class="card-custom">
    <form action="?page=jurusan_store" method="POST">

        <div class="mb-3">
            <label class="form-label">Kode Jurusan</label>
            <input type="text" name="kode_jurusan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Jurusan</label>
            <input type="text" name="nama_jurusan" class="form-control" required>
        </div>

        <button class="btn btn-primary">
            <i class="bi bi-check2-circle"></i> Simpan
        </button>
        <a href="?page=jurusan_list" class="btn btn-secondary">Kembali</a>
    </form>
</div>
