<div class="card-custom mb-4">
    <h5 class="mb-0">Edit Mahasiswa</h5>
</div>

<div class="card-custom">
    <form action="?page=mahasiswa_update" method="POST">
        <input type="hidden" name="id" value="<?= $mhs['id']; ?>">

        <div class="mb-3">
            <label class="form-label">NIM</label>
            <input type="text" name="nim" value="<?= $mhs['nim'] ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Mahasiswa</label>
            <input type="text" name="nama" value="<?= $mhs['nama'] ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Jurusan</label>
            <input type="text" name="jurusan" value="<?= $mhs['jurusan'] ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kelas</label>
            <input type="text" name="kelas" value="<?= $mhs['kelas'] ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Semester</label>
            <input type="number" name="semester" value="<?= $mhs['semester'] ?>" class="form-control" required>
        </div>

        <button class="btn btn-primary"><i class="bi bi-check2-circle"></i> Update</button>
        <a href="?page=mahasiswa_list" class="btn btn-secondary">Kembali</a>
    </form>
</div>
