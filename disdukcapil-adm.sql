CREATE TABLE pns (
    NIP VARCHAR(20) NOT NULL PRIMARY KEY,
    nama VARCHAR(30) NOT NULL,
    no_telepon VARCHAR(12) NOT NULL,
    tgl_lahir DATE,
    alamat TEXT NOT NULL,
    status_asn VARCHAR(10) DEFAULT 'Aktif' CHECK (status_asn IN ('Aktif', 'Cuti', 'Pensiun')),
    tgl_masuk DATE
);