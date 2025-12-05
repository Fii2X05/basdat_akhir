<div class="card-custom mb-4">
    <h5 class="mb-0">Tambah Dosen</h5>
</div>

<div class="card-custom">
    <form action="?page=dosen_store" method="POST">

        <div class="mb-3">
            <label class="form-label">NIP</label>
            <input type="text" name="nip" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Dosen</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Keahlian</label>
            <input type="text" name="keahlian" class="form-control" required>
        </div>

        <button class="btn btn-primary"><i class="bi bi-check2-circle"></i> Simpan</button>
        <a href="?page=dosen_list" class="btn btn-secondary">Kembali</a>
    </form>
</div>
