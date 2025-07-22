CREATE TABLE admin (
  id_admin int(11) NOT NULL AUTO_INCREMENT,
  nama_admin varchar(50) NOT NULL,
  username varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  PRIMARY KEY (id_admin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO admin (nama_admin, username, password) VALUES
('Administrator', 'admin', '21232f297a57a5a743894a0e4a801fc3'); -- pass: admin

CREATE TABLE pelanggan (
  id_pelanggan int(11) NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  nomor_kwh varchar(20) NOT NULL,
  nama_pelanggan varchar(50) NOT NULL,
  alamat text NOT NULL,
  id_tarif int(11) NOT NULL,
  PRIMARY KEY (id_pelanggan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pembayaran (
  id_pembayaran int(11) NOT NULL AUTO_INCREMENT,
  id_tagihan int(11) NOT NULL,
  id_pelanggan int(11) NOT NULL,
  tanggal_pembayaran datetime NOT NULL,
  biaya_admin int(11) NOT NULL,
  total_bayar int(11) NOT NULL,
  status varchar(20) NOT NULL,
  bukti varchar(255) NOT NULL,
  PRIMARY KEY (id_pembayaran)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tagihan (
  id_tagihan int(11) NOT NULL AUTO_INCREMENT,
  id_penggunaan int(11) NOT NULL,
  id_pelanggan int(11) NOT NULL,
  bulan varchar(20) NOT NULL,
  tahun varchar(4) NOT NULL,
  jumlah_meter int(11) NOT NULL,
  status varchar(20) NOT NULL,
  PRIMARY KEY (id_tagihan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tarif (
  id_tarif int(11) NOT NULL AUTO_INCREMENT,
  daya varchar(20) NOT NULL,
  tarif_per_kwh int(11) NOT NULL,
  PRIMARY KEY (id_tarif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE penggunaan (
  id_penggunaan int(11) NOT NULL AUTO_INCREMENT,
  id_pelanggan int(11) NOT NULL,
  bulan varchar(20) NOT NULL,
  tahun varchar(4) NOT NULL,
  meter_awal int(11) NOT NULL,
  meter_akhir int(11) NOT NULL,
  PRIMARY KEY (id_penggunaan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE pelanggan
  ADD KEY id_tarif (id_tarif);

ALTER TABLE pembayaran
  ADD KEY id_tagihan (id_tagihan),
  ADD KEY id_pelanggan (id_pelanggan);

ALTER TABLE tagihan
  ADD KEY id_penggunaan (id_penggunaan),
  ADD KEY id_pelanggan (id_pelanggan);

ALTER TABLE penggunaan
  ADD KEY id_pelanggan (id_pelanggan);
