<style>
.dashboard-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}
</style>

<div class="card-custom mb-4">
    <h5 class="mb-3">Selamat Datang di Sistem Informasi Akademik Kampus</h5>
    <p>Gunakan menu di sebelah kiri untuk mengelola data akademik kampus.</p>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="dashboard-card card-mahasiswa text-center">
            <i class="bi bi-people fs-1 text-primary"></i> 
            <h5 class="mt-2">Data Mahasiswa</h5>
            <a href="?page=mahasiswa" class="btn btn-primary btn-sm mt-2">Lihat</a>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="dashboard-card card-dosen text-center">
            <i class="bi bi-person-badge fs-1 text-success"></i>
            <h5 class="mt-2">Data Dosen</h5>
            <a href="?page=dosen" class="btn btn-success btn-sm mt-2">Lihat</a>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="dashboard-card card-jurusan text-center">
            <i class="bi bi-diagram-3 fs-1 text-warning"></i>
            <h5 class="mt-2">Data Jurusan</h5>
            <a href="?page=jurusan" class="btn btn-warning btn-sm mt-2 text-white">Lihat</a>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4 mb-3">
        <div class="dashboard-card card-kelas text-center">
            <i class="bi bi-building fs-1 text-danger"></i>
            <h5 class="mt-2">Data Kelas</h5>
            <a href="?page=kelas" class="btn btn-danger btn-sm mt-2">Lihat</a>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="dashboard-card card-matakuliah text-center">
            <i class="bi bi-journal-text fs-1 text-primary"></i>
            <h5 class="mt-2">Data Matakuliah</h5>
            <a href="?page=matakuliah" class="btn btn-primary btn-sm mt-2">Lihat</a>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="dashboard-card card-nilai text-center">
            <i class="bi bi-bar-chart fs-1 text-dark"></i>
            <h5 class="mt-2">Data Nilai</h5>
            <a href="?page=nilai" class="btn btn-secondary btn-sm mt-2">Lihat</a> 
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6 mb-3">
        <div class="dashboard-card card-transkrip text-center">
            <i class="bi bi-file-earmark-text fs-1 text-info"></i>
            <h5 class="mt-2">Transkrip Nilai</h5>
            <a href="?page=transkrip" class="btn btn-info btn-sm mt-2 text-white">Lihat</a>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="dashboard-card card-laporan text-center">
            <i class="bi bi-graph-up fs-1 text-success"></i>
            <h5 class="mt-2">Laporan Akademik</h5>
            <a href="?page=laporan" class="btn btn-success btn-sm mt-2">Lihat</a>
        </div>
    </div>
</div>