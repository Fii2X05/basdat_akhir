<div class="card-custom mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">List Kelas</h5>

        <a href="?page=kelas_create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Kelas
        </a>
    </div>
</div>

<div class="card-custom">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th width="80">Kode</th>
                <th>Nama Kelas</th>
                <th>Wali Kelas</th>
                <th width="220">Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($dataKelas)): ?>
                <?php foreach ($dataKelas as $kls): ?>
                    <tr>
                        <td><?= $kls['kode_kelas'] ?></td>
                        <td><?= $kls['nama_kelas'] ?></td>
                        <td><?= $kls['wali_kelas'] ?></td>

                        <td>
                            <a href="?page=kelas_detail&id=<?= $kls['id'] ?>" 
                               class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>

                            <a href="?page=kelas_edit&id=<?= $kls['id'] ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>

                            <a href="?page=kelas_delete&id=<?= $kls['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin ingin hapus kelas ini?');">
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
