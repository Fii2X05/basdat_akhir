<div class="card-custom mb-4">
    <h5 class="mb-0">Edit Kelas</h5>
</div>

<div class="card-custom">
    <form action="?page=kelas_update" method="POST">
        <input type="hidden" name="id" value="<?= $kls['id'] ?>">

        <div class="mb-3">
            <label class="form-label">Kode Kelas</label>
            <input type="text" 
                   name="kode_kelas" 
                   class="form-control" 
                   value="<?= $kls['kode_kelas'] ?>" 
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Kelas</label>
            <input type="text" 
                   name="nama_kelas" 
                   class="form-control" 
                   value="<?= $kls['nama_kelas'] ?>" 
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Wali Kelas</label>
            <input type="text" 
                   name="wali_kelas" 
                   class="form-control" 
                   value="<?= $kls['wali_kelas'] ?>" 
                   required>
        </div>

        <button class="btn btn-primary">
            <i class="bi bi-check2-circle"></i> Update
        </button>

        <a href="?page=kelas_list" class="btn btn-secondary">Kembali</a>
    </form>
</div>
