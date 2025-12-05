<div class="card-custom mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">List Mahasiswa</h5>
        <a href="?page=mahasiswa_create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Mahasiswa
        </a>
    </div>
</div>

<div class="card-custom">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th width="60">NIM</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Kelas</th>
                <th>Semester</th>
                <th width="200">Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php if (!empty($dataMahasiswa)): ?>
            <?php foreach ($dataMahasiswa as $mhs): ?>
                <tr>
                    <td><?= $mhs['nim'] ?></td>
                    <td><?= $mhs['nama'] ?></td>
                    <td><?= $mhs['jurusan'] ?></td>
                    <td><?= $mhs['kelas'] ?></td>
                    <td><?= $mhs['semester'] ?></td>

                    <td>
                        <a href="?page=mahasiswa_detail&id=<?= $mhs['id']; ?>" 
                           class="btn btn-info btn-sm">
                           <i class="bi bi-eye"></i> Detail
                        </a>

                        <a href="?page=mahasiswa_edit&id=<?= $mhs['id']; ?>" 
                           class="btn btn-warning btn-sm">
                           <i class="bi bi-pencil-square"></i> Edit
                        </a>

                        <a href="?page=mahasiswa_delete&id=<?= $mhs['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus?');">
                           <i class="bi bi-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted">Tidak ada data.</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
</div>
