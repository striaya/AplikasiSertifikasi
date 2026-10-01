package models

import "time"

// User adalah akun administrator aplikasi.
type User struct {
	ID        uint      `gorm:"primaryKey" json:"id"`
	Username  string    `gorm:"size:50;uniqueIndex;not null" json:"username"`
	Password  string    `gorm:"size:255;not null" json:"-"`
	Nama      string    `gorm:"size:100;not null" json:"nama"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

func (User) TableName() string { return "users" }

// Skema adalah data skema sertifikasi (KKNI / Okupasi / Klaster).
type Skema struct {
	ID        uint      `gorm:"primaryKey" json:"id"`
	KodeSkema string    `gorm:"size:50;uniqueIndex;not null" json:"kode_skema"`
	NamaSkema string    `gorm:"size:150;not null" json:"nama_skema"`
	Jenis     string    `gorm:"size:20;not null" json:"jenis"`
	Deskripsi string    `gorm:"type:text" json:"deskripsi"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

func (Skema) TableName() string { return "skema" }

// Peserta adalah data peserta sertifikasi yang berelasi (many-to-one) ke Skema lewat SkemaID.
type Peserta struct {
	ID           uint      `gorm:"primaryKey" json:"id"`
	NIK          string    `gorm:"column:nik;size:16;uniqueIndex;not null" json:"nik"`
	Nama         string    `gorm:"size:100;not null" json:"nama"`
	Email        string    `gorm:"size:100;uniqueIndex;not null" json:"email"`
	NoTelepon    string    `gorm:"size:15;not null" json:"no_telepon"`
	JenisKelamin string    `gorm:"size:1;not null" json:"jenis_kelamin"`
	TanggalLahir string    `gorm:"size:10;not null" json:"tanggal_lahir"`
	Alamat       string    `gorm:"type:text;not null" json:"alamat"`
	Status       string    `gorm:"size:20;not null;default:terdaftar" json:"status"`
	SkemaID      uint      `gorm:"not null;index" json:"skema_id"`
	Skema        *Skema    `gorm:"foreignKey:SkemaID;references:ID;constraint:OnUpdate:CASCADE,OnDelete:RESTRICT" json:"skema,omitempty"`
	CreatedAt    time.Time `json:"created_at"`
	UpdatedAt    time.Time `json:"updated_at"`
}

func (Peserta) TableName() string { return "peserta" }
