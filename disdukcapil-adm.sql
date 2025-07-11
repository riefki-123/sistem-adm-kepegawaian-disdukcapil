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
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE jenis_pengajuan (
    kode_jenis VARCHAR(10) NOT NULL PRIMARY KEY,
    nama_jenis VARCHAR(50) NOT NULL,
    kategori_pengajuan ENUM('Kenaikan Pangkat', 'Kenaikan Gaji') NOT NULL,
    persyaratan TEXT NOT NULL
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

-- Dummy data untuk tabel pns (8 data)
INSERT INTO pns (NIP, nama, no_telepon, tgl_lahir, alamat, status_asn, tgl_masuk) VALUES
('198709231001', 'Rina Amalia', '081234567890', '1987-09-23', 'Jl. Cicopenhagen No.10', 'Aktif', '2010-01-05'),
('199201150002', 'Doni Saputra', '085612345678', '1992-01-15', 'Jl. Ciboston No.22', 'Aktif', '2015-07-10'),
('198505070003', 'Siti Nurjanah', '082198765432', '1985-05-07', 'Jl. Pangea No.7', 'Pensiun', '2005-03-12'),
('199012041004', 'Bambang Wijaya', '081298765432', '1990-12-04', 'Jl. Merdeka No.15', 'Aktif', '2012-08-20'),
('198803180005', 'Maya Indrawati', '085234567891', '1988-03-18', 'Jl. Sudirman No.33', 'Cuti', '2011-02-14'),
('199505220006', 'Ahmad Fauzi', '082345678912', '1995-05-22', 'Jl. Gatot Subroto No.8', 'Aktif', '2018-09-03'),
('198411300007', 'Dewi Kartika', '081567891234', '1984-11-30', 'Jl. Diponegoro No.45', 'Aktif', '2007-06-18'),
('199308250008', 'Rudi Hermawan', '085789123456', '1993-08-25', 'Jl. Ahmad Yani No.12', 'Aktif', '2016-04-25');

-- Dummy data untuk tabel operator (8 data)
INSERT INTO operator (nama_lengkap_operator, peran, no_telepon, is_active) VALUES
('Sari Wulandari', 'Admin', '081111222333', TRUE),
('Budi Santoso', 'BKD', '082444555666', TRUE),
('Linda Kusuma', 'Admin', '083777888999', TRUE),
('Agus Priyanto', 'BKD', '081222333444', TRUE),
('Ratna Sari', 'Admin', '085555666777', FALSE),
('Hendro Prasetyo', 'BKD', '082888999111', TRUE),
('Novi Rahayu', 'Admin', '083111222444', TRUE),
('Dedi Kurniawan', 'BKD', '081333444555', TRUE);

-- Dummy data untuk tabel jenis_pengajuan (8 data)
INSERT INTO jenis_pengajuan (kode_jenis, nama_jenis, kategori_pengajuan, persyaratan) VALUES
('KP001', 'Kenaikan Pangkat Reguler', 'Kenaikan Pangkat', 'SK terakhir, DP3 2 tahun terakhir, Sertifikat diklat'),
('KP002', 'Kenaikan Pangkat Pilihan', 'Kenaikan Pangkat', 'SK terakhir, DP3 3 tahun terakhir, Ijazah pendidikan'),
('KP003', 'Kenaikan Pangkat Struktural', 'Kenaikan Pangkat', 'SK terakhir, SK jabatan, Sertifikat kepemimpinan'),
('KG001', 'Kenaikan Gaji Berkala', 'Kenaikan Gaji', 'SK terakhir, DP3 2 tahun terakhir'),
('KG002', 'Kenaikan Gaji Luar Biasa', 'Kenaikan Gaji', 'SK terakhir, Surat rekomendasi atasan, Prestasi kerja'),
('KP004', 'Kenaikan Pangkat Fungsional', 'Kenaikan Pangkat', 'SK terakhir, Angka kredit, Sertifikat kompetensi'),
('KG003', 'Penyesuaian Gaji', 'Kenaikan Gaji', 'SK terakhir, SK pangkat terbaru, DP3'),
('KP005', 'Kenaikan Pangkat Anumerta', 'Kenaikan Pangkat', 'Surat Putusan, SK terakhir, Surat rekomendasi');

-- Dummy data untuk tabel pengajuan (8 data)
INSERT INTO pengajuan (NIP, kode_jenis, id_operator, tgl_pengajuan, status_pengajuan, tanggal_persetujuan, keterangan) VALUES
('198709231001', 'KP001', 1, '2024-01-15', 'Disetujui', '2024-01-25', 'Pengajuan disetujui sesuai persyaratan'),
('199201150002', 'KG001', 2, '2024-02-10', 'Diajukan', NULL, NULL),
('198505070003', 'KP002', 3, '2024-01-20', 'Ditolak', '2024-01-30', 'Dokumen pendukung tidak lengkap'),
('199012041004', 'KG002', 4, '2024-03-05', 'Disetujui', '2024-03-15', 'Pengajuan disetujui berdasarkan prestasi'),
('198803180005', 'KP003', 1, '2024-02-28', 'Diajukan', NULL, NULL),
('199505220006', 'KG001', 2, '2024-03-12', 'Disetujui', '2024-03-20', 'Memenuhi masa kerja'),
('198411300007', 'KP004', 3, '2024-01-08', 'Diajukan', NULL, NULL),
('199308250008', 'KG003', 4, '2024-02-18', 'Disetujui', '2024-02-25', 'Penyesuaian gaji telah sesuai');

-- Dummy data untuk tabel dokumen_pendukung (8 data)
INSERT INTO dokumen_pendukung (id_pengajuan, nama_dokumen, Path_file) VALUES
(1, 'SK Pangkat Terakhir', '/documents/sk_pangkat_rina.pdf'),
(1, 'DP3 2023', '/documents/dp3_2023_rina.pdf'),
(2, 'SK Gaji Terakhir', '/documents/sk_gaji_doni.pdf'),
(3, 'Ijazah S1', '/documents/ijazah_s1_siti.pdf'),
(4, 'Surat Rekomendasi', '/documents/rekomendasi_bambang.pdf'),
(5, 'SK Jabatan', '/documents/sk_jabatan_maya.pdf'),
(6, 'DP3 2023', '/documents/dp3_2023_ahmad.pdf'),
(7, 'Sertifikat Kompetensi', '/documents/sertifikat_dewi.pdf');

-- Dummy data untuk tabel riwayat_pangkat (8 data)
INSERT INTO riwayat_pangkat (NIP, pangkat_golongan, jabatan, tgl_mulai, sk_pangkat, is_current) VALUES
('198709231001', 'Penata Muda Tk.I/III.b', 'Analis Kepegawaian', '2020-04-01', 'SK/001/2020', TRUE),
('199201150002', 'Penata/III.c', 'Kepala Seksi', '2021-01-01', 'SK/002/2021', TRUE),
('198505070003', 'Pembina/IV.a', 'Kepala Bidang', '2018-03-01', 'SK/003/2018', FALSE),
('199012041004', 'Penata Muda/III.a', 'Staff Administrasi', '2019-08-01', 'SK/004/2019', TRUE),
('198803180005', 'Penata Tk.I/III.d', 'Kepala Sub Bagian', '2022-06-01', 'SK/005/2022', TRUE),
('199505220006', 'Pengatur Tk.I/II.d', 'Staff Operasional', '2023-02-01', 'SK/006/2023', TRUE),
('198411300007', 'Pembina Tk.I/IV.b', 'Kepala Divisi', '2020-11-01', 'SK/007/2020', TRUE),
('199308250008', 'Penata Muda Tk.I/III.b', 'Analis Data', '2021-09-01', 'SK/008/2021', TRUE);

-- Dummy data untuk tabel riwayat_gaji (8 data)
INSERT INTO riwayat_gaji (NIP, nominal_gaji, tgl_mulai, sk_gaji, is_current) VALUES
('198709231001', 4250000.00, '2020-04-01', 'SK-GAJI/001/2020', TRUE),
('199201150002', 4850000.00, '2021-01-01', 'SK-GAJI/002/2021', TRUE),
('198505070003', 6200000.00, '2018-03-01', 'SK-GAJI/003/2018', FALSE),
('199012041004', 3950000.00, '2019-08-01', 'SK-GAJI/004/2019', TRUE),
('198803180005', 5150000.00, '2022-06-01', 'SK-GAJI/005/2022', TRUE),
('199505220006', 3200000.00, '2023-02-01', 'SK-GAJI/006/2023', TRUE),
('198411300007', 6850000.00, '2020-11-01', 'SK-GAJI/007/2020', TRUE),
('199308250008', 4100000.00, '2021-09-01', 'SK-GAJI/008/2021', TRUE);

-- Slide 1
INSERT INTO pns (NIP, nama, no_telepon, tgl_lahir, alamat, status_asn, tgl_masuk)
VALUES ('198412300009', 'Lebron James', '081298765499', '1984-12-30', 'Jl. LA Lakers No.23', 'Aktif', '2025-06-30');

-- Slide 2
INSERT INTO pengajuan (NIP, kode_jenis, id_operator, tgl_pengajuan, status_pengajuan)
VALUES ('198412300009', 'KP001', 1, '2025-06-30', 'Diajukan');

-- Slide 3
INSERT INTO dokumen_pendukung (id_pengajuan, nama_dokumen, Path_file)
VALUES (9, 'SK Pangkat Terakhir', '/documents/sk_pangkat_lebron.pdf');

-- Slide 4
SELECT p.nama, j.nama_jenis, d.nama_dokumen, d.Path_file
FROM pengajuan pg
JOIN pns p ON pg.NIP = p.NIP
JOIN jenis_pengajuan j ON pg.kode_jenis = j.kode_jenis
JOIN dokumen_pendukung d ON pg.id_pengajuan = d.id_pengajuan
WHERE pg.NIP = '198412300009';

-- Slide 5
-- Slide 6
UPDATE pengajuan
SET status_pengajuan = 'Disetujui',
    tanggal_persetujuan = CURRENT_DATE,
    id_operator = 2
WHERE NIP = '198412300009';

-- Slide 7
INSERT INTO riwayat_pangkat (NIP, pangkat_golongan, jabatan, tgl_mulai, sk_pangkat, is_current)
VALUES ('198412300009', 'Penata Muda/III.a', 'Analis Kepegawaian', '2025-07-01', 'SK/009/2025', TRUE);

