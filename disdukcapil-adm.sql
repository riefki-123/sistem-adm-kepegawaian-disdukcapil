CREATE TABLE pns (
    NIP VARCHAR(20) NOT NULL PRIMARY KEY,
    nama VARCHAR(30) NOT NULL,
    no_telepon VARCHAR(12) NOT NULL,
    tgl_lahir DATE,
    alamat TEXT NOT NULL,
    status_asn VARCHAR(10) DEFAULT 'Aktif' CHECK (status_asn IN ('Aktif', 'Cuti', 'Pensiun')),
    tgl_masuk DATE
);

CREATE TABLE operator (
    id_operator INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nama_lengkap_operator VARCHAR(30) NOT NULL,
    peran ENUM('Admin', 'BKD') NOT NULL,
    no_telepon VARCHAR(12) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
);

CREATE TABLE jenis_pengajuan (
    kode_jenis VARCHAR(10) NOT NULL PRIMARY KEY,
    nama_jenis VARCHAR(50) NOT NULL,
    kategori_pengajuan ENUM('Kenaikan Pangkat', 'Kenaikan Gaji') NOT NULL,
    persyaratan TEXT NOT NULL,
);

CREATE TABLE pengajuan (
    id_pengajuan INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    NIP VARCHAR(20) NOT NULL,
    kode_jenis VARCHAR(10) NOT NULL,
    id_operator INT NOT NULL,
    tgl_pengajuan DATE NOT NULL,
    status_pengajuan ENUM('Diajukan', 'Disetujui', 'Ditolak') DEFAULT 'Diajukan',
    tanggal_persetujuan DATE,
    keterangan TEXT,
    FOREIGN KEY (NIP) REFERENCES pns(NIP),
    FOREIGN KEY (kode_jenis) REFERENCES jenis_pengajuan(kode_jenis),
    FOREIGN KEY (id_operator) REFERENCES operator(id_operator)
);

CREATE TABLE dokumen_pendukung(
    id_dokumen INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_pengajuan INT NOT NULL,
    nama_dokumen VARCHAR(50) NOT NULL,
    Path_file VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_pengajuan) REFERENCES pengajuan(id_pengajuan)
);

CREATE TABLE riwayat_pangkat (
    id_riwayat_pangkat INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    NIP VARCHAR(20) NOT NULL,
    pangkat_golongan VARCHAR(50) NOT NULL,
    jabatan VARCHAR(50) NOT NULL,
    tgl_mulai DATE NOT NULL,
    sk_pangkat VARCHAR(50) NOT NULL,
    is_current BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (NIP) REFERENCES pns(NIP)
);

CREATE TABLE riwayat_gaji (
    id_riwayat_gaji INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    NIP VARCHAR(20) NOT NULL,
    nominal_gaji DECIMAL(15, 2) NOT NULL,
    tgl_mulai DATE NOT NULL,
    sk_gaji VARCHAR(50) NOT NULL,
    is_current BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (NIP) REFERENCES pns(NIP)
);

SIKMA SIKMA BOY