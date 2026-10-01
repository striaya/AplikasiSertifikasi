package dto

// LoginRequest adalah body untuk POST /api/auth/login.
type LoginRequest struct {
	Username string `json:"username" binding:"required"`
	Password string `json:"password" binding:"required"`
}

// SkemaRequest adalah body untuk tambah / ubah skema.
type SkemaRequest struct {
	KodeSkema string `json:"kode_skema" binding:"required,max=50"`
	NamaSkema string `json:"nama_skema" binding:"required,max=150"`
	Jenis     string `json:"jenis" binding:"required,oneof=KKNI Okupasi Klaster"`
	Deskripsi string `json:"deskripsi"`
}

// PesertaRequest adalah body untuk tambah / ubah peserta.
type PesertaRequest struct {
	NIK          string `json:"nik" binding:"required,len=16,numeric"`
	Nama         string `json:"nama" binding:"required,min=3,max=100"`
	Email        string `json:"email" binding:"required,email,max=100"`
	NoTelepon    string `json:"no_telepon" binding:"required,min=9,max=15,numeric"`
	JenisKelamin string `json:"jenis_kelamin" binding:"required,oneof=L P"`
	TanggalLahir string `json:"tanggal_lahir" binding:"required,datetime=2006-01-02"`
	Alamat       string `json:"alamat" binding:"required"`
	SkemaID      uint   `json:"skema_id" binding:"required"`
	Status       string `json:"status" binding:"omitempty,oneof=terdaftar kompeten belum_kompeten"`
}
