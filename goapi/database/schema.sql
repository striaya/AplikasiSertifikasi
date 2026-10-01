CREATE DATABASE IF NOT EXISTS db_peserta_sertifikasi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_peserta_sertifikasi;

CREATE TABLE IF NOT EXISTS users (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(50)  NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  nama       VARCHAR(100) NOT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS skema (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  kode_skema VARCHAR(50)  NOT NULL UNIQUE,
  nama_skema VARCHAR(150) NOT NULL,
  jenis      VARCHAR(20)  NOT NULL,
  deskripsi  TEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS peserta (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nik           VARCHAR(16)  NOT NULL UNIQUE,
  nama          VARCHAR(100) NOT NULL,
  email         VARCHAR(100) NOT NULL UNIQUE,
  no_telepon    VARCHAR(15)  NOT NULL,
  jenis_kelamin VARCHAR(1)   NOT NULL,
  tanggal_lahir VARCHAR(10)  NOT NULL,
  alamat        TEXT NOT NULL,
  status        VARCHAR(20)  NOT NULL DEFAULT 'terdaftar',
  skema_id      BIGINT UNSIGNED NOT NULL,
  created_at    DATETIME NULL,
  updated_at    DATETIME NULL,
  INDEX idx_peserta_skema_id (skema_id),
  CONSTRAINT fk_peserta_skema FOREIGN KEY (skema_id) REFERENCES skema (id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;
