# 📁 SIAKAD Project Structure

## Final Clean Structure

```
basdat_akhir/
│
├── 📂 bdakhir/                      # Main Application
│   ├── 📂 assets/css/               # Stylesheets
│   ├── 📂 config/
│   │   └── database.php             # Database configuration
│   ├── 📂 uploads/                  # Student photos
│   ├── 📂 views/                    # All view files
│   │   ├── dashboard.php            # Dashboard
│   │   ├── mahasiswa.php            # Students
│   │   ├── dosen.php                # Lecturers
│   │   ├── jurusan.php              # Departments
│   │   ├── kelas.php                # Classes
│   │   ├── matakuliah.php           # Courses
│   │   ├── nilai.php                # Grades
│   │   ├── transkrip.php            # Transcripts
│   │   └── laporan.php              # Reports
│   └── index.php                    # Main application file
│
├── 🗄️ database_schema.sql           # Database setup
├── 🔧 setup_interactive.bat         # Database setup script
├── 🔧 enable_postgresql.bat         # Enable PostgreSQL drivers
├── 🚀 start_app.bat                 # Start PHP server
├── 📖 README.md                     # Main documentation
└── 📖 START_HERE.md                 # Quick start guide
```

## File Count

- **Application Files:** 12 files
- **Setup Scripts:** 3 files
- **Documentation:** 2 files
- **Total:** 17 files

## What Each File Does

### Application Files (bdakhir/)
- **index.php** - Main controller, handles all routing and CRUD operations
- **database.php** - Database connection configuration
- **9 view files** - HTML/PHP templates for each feature
- **uploads/** - Storage for student photos

### Setup Files
- **database_schema.sql** - Creates tables and adds sample data
- **setup_interactive.bat** - Interactive database setup wizard
- **enable_postgresql.bat** - Enables PostgreSQL drivers in PHP
- **start_app.bat** - Starts PHP built-in server

### Documentation
- **README.md** - Complete project documentation
- **START_HERE.md** - Quick start guide for new users

## Quick Commands

**First Time Setup:**
```cmd
setup_interactive.bat
```

**Start Application:**
```cmd
start_app.bat
```
Or use XAMPP and open: http://localhost/basdat_akhir/bdakhir/

**Read Documentation:**
- Start with `START_HERE.md`
- Then read `README.md` for details

## Features Available

1. ✅ Dashboard
2. ✅ Mahasiswa (Students)
3. ✅ Dosen (Lecturers)
4. ✅ Jurusan (Departments)
5. ✅ Kelas (Classes)
6. ✅ Mata Kuliah (Courses)
7. ✅ Nilai (Grades)
8. ✅ Transkrip Nilai (Academic Transcripts)
9. ✅ Laporan Akademik (Academic Reports)

## Database Tables

1. jurusan (Departments)
2. kelas (Classes)
3. mahasiswa (Students)
4. dosen (Lecturers)
5. mata_kuliah (Courses)
6. nilai (Grades)

## Technology Stack

- PHP 8.2+
- PostgreSQL 18
- Bootstrap 5.3
- Apache (XAMPP) or PHP Built-in Server

---

**Clean, simple, and ready to use!** 🎉
