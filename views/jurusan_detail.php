<div class="card-custom mb-4">
    <h5 class="mb-0">Detail Jurusan</h5>
</div>

<div class="card-custom">
    <table class="table table-bordered">
        <tr><th>Kode Jurusan</th><td><?= $jrs['kode_jurusan'] ?></td></tr>
        <tr><th>Nama Jurusan</th><td><?= $jrs['nama_jurusan'] ?></td></tr>
    </table>

    <a href="?page=jurusan_edit&id=<?= $jrs['id'] ?>" 
       class="btn btn-warning btn-sm">
        <i class="bi bi-pencil-square"></i> Edit
    </a>

    <a href="?page=jurusan_list" class="btn btn-secondary btn-sm">Kembali</a>
</div>
