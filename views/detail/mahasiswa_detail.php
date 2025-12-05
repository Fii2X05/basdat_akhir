<div class="card-custom mb-4">
    <h5 class="mb-0">Detail Mahasiswa</h5>
</div>

<div class="card-custom">
    <table class="table table-bordered">
        <tr><th>NIM</th><td><?= $mhs['nim'] ?></td></tr>
        <tr><th>Nama</th><td><?= $mhs['nama'] ?></td></tr>
        <tr><th>Jurusan</th><td><?= $mhs['jurusan'] ?></td></tr>
        <tr><th>Kelas</th><td><?= $mhs['kelas'] ?></td></tr>
        <tr><th>Semester</th><td><?= $mhs['semester'] ?></td></tr>
    </table>

    <a href="?page=mahasiswa_edit&id=<?= $mhs['id'] ?>" 
       class="btn btn-warning btn-sm">
       <i class="bi bi-pencil-square"></i> Edit
    </a>

    <a href="?page=mahasiswa_list" class="btn btn-secondary btn-sm">
        Kembali
    </a>
</div>
