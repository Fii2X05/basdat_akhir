<div class="card-custom mb-4">
    <h5 class="mb-0">Detail Kelas</h5>
</div>

<div class="card-custom">
    <table class="table table-bordered">
        <tr><th>Kode Kelas</th><td><?= $kls['kode_kelas'] ?></td></tr>
        <tr><th>Nama Kelas</th><td><?= $kls['nama_kelas'] ?></td></tr>
        <tr><th>Wali Kelas</th><td><?= $kls['wali_kelas'] ?></td></tr>
    </table>

    <a href="?page=kelas_edit&id=<?= $kls['id'] ?>" 
       class="btn btn-warning btn-sm">
        <i class="bi bi-pencil-square"></i> Edit
    </a>

    <a href="?page=kelas_list" class="btn btn-secondary btn-sm">Kembali</a>
</div>
