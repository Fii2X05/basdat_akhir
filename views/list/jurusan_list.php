<div class="card-custom mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">List Jurusan</h5>
        <a href="?page=jurusan_create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Jurusan
        </a>
    </div>
</div>

<div class="card-custom">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th width="80">Kode</th>
                <th>Nama Jurusan</th>
                <th width="250">Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($dataJurusan)): ?>
                <?php foreach ($dataJurusan as $jrs): ?>
                    <tr>
                        <td><?= $jrs['kode_jurusan'] ?></td>
                        <td><?= $jrs['nama_jurusan'] ?></td>

                        <td>
                            <a href="?page=jurusan_detail&id=<?= $jrs['id'] ?>" 
                               class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>

                            <a href="?page=jurusan_edit&id=<?= $jrs['id'] ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>

                            <a href="?page=jurusan_delete&id=<?= $jrs['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus jurusan ini?');">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">Tidak ada data.</td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>
</div>
