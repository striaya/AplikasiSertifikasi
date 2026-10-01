package database

import (
	"fmt"

	"github.com/glebarez/sqlite"
	"golang.org/x/crypto/bcrypt"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"gorm.io/gorm/logger"

	"peserta-sertifikasi-api/config"
	"peserta-sertifikasi-api/models"
)

// Connect membuka koneksi database sesuai DB_DRIVER
func Connect(cfg *config.Config) (*gorm.DB, error) {
	var dialector gorm.Dialector

	switch cfg.DBDriver {
	case "mysql":
		dsn := fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?charset=utf8mb4&parseTime=True&loc=Local",
			cfg.DBUser, cfg.DBPassword, cfg.DBHost, cfg.DBPort, cfg.DBName)
		dialector = mysql.Open(dsn)
	default:
		dialector = sqlite.Open(cfg.DBPath + "?_pragma=foreign_keys(1)")
	}

	return gorm.Open(dialector, &gorm.Config{
		Logger: logger.Default.LogMode(logger.Warn),
	})
}

// Migrate membuat / menyesuaikan tabel dari struct model.
func Migrate(db *gorm.DB) error {
	return db.AutoMigrate(&models.User{}, &models.Skema{}, &models.Peserta{})
}

// Seed mengisi data dummy awal (akun admin, beberapa skema, beberapa peserta) bila tabel masih kosong.
func Seed(db *gorm.DB, cfg *config.Config) error {
	var count int64

	if err := db.Model(&models.User{}).Count(&count).Error; err != nil {
		return err
	}
	if count == 0 {
		hash, err := bcrypt.GenerateFromPassword([]byte(cfg.AdminPassword), bcrypt.DefaultCost)
		if err != nil {
			return err
		}
		admin := models.User{Username: cfg.AdminUsername, Password: string(hash), Nama: "Administrator"}
		if err := db.Create(&admin).Error; err != nil {
			return err
		}
	}

	if err := db.Model(&models.Skema{}).Count(&count).Error; err != nil {
		return err
	}
	if count == 0 {
		skema := []models.Skema{
			{KodeSkema: "SKM-JWD", NamaSkema: "Junior Web Developer", Jenis: "Okupasi", Deskripsi: "Skema okupasi untuk pengembang web tingkat junior"},
			{KodeSkema: "SKM-JMD", NamaSkema: "Junior Mobile Developer", Jenis: "Okupasi", Deskripsi: "Skema okupasi untuk pengembang aplikasi mobile tingkat junior"},
			{KodeSkema: "SKM-DBA", NamaSkema: "Database Administrator", Jenis: "KKNI", Deskripsi: "Skema KKNI untuk pengelolaan basis data"},
		}
		if err := db.Create(&skema).Error; err != nil {
			return err
		}

		if err := db.Model(&models.Peserta{}).Count(&count).Error; err != nil {
			return err
		}
		if count == 0 {
			peserta := []models.Peserta{
				{NIK: "3201010101010001", Nama: "Alvian Cahyo Pambudi", Email: "tisha@example.com", NoTelepon: "081234567801", JenisKelamin: "L", TanggalLahir: "2000-01-15", Alamat: "Jl. Merdeka No. 1, Depok", Status: "terdaftar", SkemaID: skema[0].ID},
				{NIK: "3201010101010002", Nama: "Satria Henggar Yoselino", Email: "ruth@example.com", NoTelepon: "081234567802", JenisKelamin: "P", TanggalLahir: "1999-05-20", Alamat: "Jl. Kenanga No. 2, Bogor", Status: "kompeten", SkemaID: skema[0].ID},
				{NIK: "3201010101010003", Nama: "Pam Budi", Email: "budi@example.com", NoTelepon: "081234567803", JenisKelamin: "L", TanggalLahir: "2001-09-10", Alamat: "Jl. Melati No. 3, Jakarta", Status: "terdaftar", SkemaID: skema[1].ID},
			}
			if err := db.Create(&peserta).Error; err != nil {
				return err
			}
		}
	}

	return nil
}
