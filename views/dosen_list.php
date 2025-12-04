<div class="card-custom mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">List Dosen</h5>
        <a href="?page=dosen_create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Dosen
        </a>
    </div>
</div>

<div class="card-custom">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th width="70">NIP</th>
                <th>Nama</th>
                <th>Keahlian</th>
                <th width="230">Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($dataDosen)): ?>
                <?php foreach ($dataDosen as $dsn): ?>
                    <tr>
                        <td><?= $dsn['nip'] ?></td>
                        <td><?= $dsn['nama'] ?></td>
                        <td><?= $dsn['keahlian'] ?></td>

                        <td>
                            <a href="?page=dosen_detail&id=<?= $dsn['id'] ?>" 
                               class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>

                            <a href="?page=dosen_edit&id=<?= $dsn['id'] ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>

                            <a href="?page=dosen_delete&id=<?= $dsn['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus dosen ini?');">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">Tidak ada data.</td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>
</div>
