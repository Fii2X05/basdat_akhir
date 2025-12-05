<div class="card-custom mb-4">
    <h5 class="mb-0">Detail Dosen</h5>
</div>

<div class="card-custom">
    <table class="table table-bordered">
        <tr><th>NIP</th><td><?= $dsn['nip'] ?></td></tr>
        <tr><th>Nama</th><td><?= $dsn['nama'] ?></td></tr>
        <tr><th>Keahlian</th><td><?= $dsn['keahlian'] ?></td></tr>
    </table>

    <a href="?page=dosen_edit&id=<?= $dsn['id'] ?>" 
       class="btn btn-warning btn-sm">
        <i class="bi bi-pencil-square"></i> Edit
    </a>

    <a href="?page=dosen_list" class="btn btn-secondary btn-sm">Kembali</a>
</div>
