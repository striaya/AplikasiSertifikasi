package handlers

import (
	"errors"
	"math"
	"net/http"
	"strconv"
	"strings"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"peserta-sertifikasi-api/dto"
	"peserta-sertifikasi-api/models"
	"peserta-sertifikasi-api/utils"
)

// applyPesertaFilters menerapkan pencarian & filter dari query string.
func applyPesertaFilters(q *gorm.DB, c *gin.Context) *gorm.DB {
	if s := strings.TrimSpace(c.Query("search")); s != "" {
		like := "%" + s + "%"
		q = q.Where("nama LIKE ? OR nik LIKE ? OR email LIKE ?", like, like, like)
	}
	if v := strings.TrimSpace(c.Query("skema_id")); v != "" {
		q = q.Where("skema_id = ?", v)
	}
	if v := strings.TrimSpace(c.Query("status")); v != "" {
		q = q.Where("status = ?", v)
	}
	return q
}

// pesertaConflicts mengecek NIK / email yang sudah dipakai peserta lain.
func (h *Handler) pesertaConflicts(nik, email string, excludeID uint) (map[string]string, error) {
	conflicts := map[string]string{}

	check := func(column, value, message, key string) error {
		var count int64
		q := h.DB.Model(&models.Peserta{}).Where(column+" = ?", value)
		if excludeID != 0 {
			q = q.Where("id <> ?", excludeID)
		}
		if err := q.Count(&count).Error; err != nil {
			return err
		}
		if count > 0 {
			conflicts[key] = message
		}
		return nil
	}

	if err := check("nik", nik, "NIK sudah terdaftar", "nik"); err != nil {
		return nil, err
	}
	if err := check("email", email, "email sudah terdaftar", "email"); err != nil {
		return nil, err
	}
	return conflicts, nil
}

// skemaExists memastikan skema_id yang dipilih benar-benar ada (relasi valid).
func (h *Handler) skemaExists(id uint) (bool, error) {
	var count int64
	err := h.DB.Model(&models.Skema{}).Where("id = ?", id).Count(&count).Error
	return count > 0, err
}

// ListPeserta menampilkan data peserta dengan pencarian, filter, dan pagination.
// Query: search, skema_id, status, page (default 1), limit (default 10, maks 100).
func (h *Handler) ListPeserta(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	if page < 1 {
		page = 1
	}
	limit, _ := strconv.Atoi(c.DefaultQuery("limit", "10"))
	if limit < 1 {
		limit = 10
	}
	if limit > 100 {
		limit = 100
	}

	var total int64
	if err := applyPesertaFilters(h.DB.Model(&models.Peserta{}), c).Count(&total).Error; err != nil {
		utils.ServerError(c)
		return
	}

	items := []models.Peserta{}
	err := applyPesertaFilters(h.DB.Model(&models.Peserta{}), c).
		Preload("Skema").
		Order("id DESC").
		Offset((page - 1) * limit).
		Limit(limit).
		Find(&items).Error
	if err != nil {
		utils.ServerError(c)
		return
	}

	utils.Success(c, http.StatusOK, "Data peserta berhasil diambil", gin.H{
		"items": items,
		"pagination": gin.H{
			"page":        page,
			"limit":       limit,
			"total":       total,
			"total_pages": int(math.Ceil(float64(total) / float64(limit))),
		},
	})
}

// GetPeserta menampilkan detail satu peserta beserta skemanya.
func (h *Handler) GetPeserta(c *gin.Context) {
	id, ok := parseID(c)
	if !ok {
		return
	}

	var peserta models.Peserta
	if err := h.DB.Preload("Skema").First(&peserta, id).Error; err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			utils.Error(c, http.StatusNotFound, "Peserta tidak ditemukan", nil)
			return
		}
		utils.ServerError(c)
		return
	}
	utils.Success(c, http.StatusOK, "Detail peserta berhasil diambil", peserta)
}

// CreatePeserta menambah peserta baru.
func (h *Handler) CreatePeserta(c *gin.Context) {
	var req dto.PesertaRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		utils.BindError(c, err)
		return
	}

	exists, err := h.skemaExists(req.SkemaID)
	if err != nil {
		utils.ServerError(c)
		return
	}
	if !exists {
		utils.Error(c, http.StatusUnprocessableEntity, "Validasi gagal, periksa kembali input Anda", gin.H{"skema_id": "skema tidak ditemukan"})
		return
	}

	conflicts, err := h.pesertaConflicts(req.NIK, req.Email, 0)
	if err != nil {
		utils.ServerError(c)
		return
	}
	if len(conflicts) > 0 {
		utils.Error(c, http.StatusConflict, "Data peserta bentrok dengan data yang sudah ada", conflicts)
		return
	}

	status := req.Status
	if status == "" {
		status = "terdaftar"
	}

	peserta := models.Peserta{
		NIK:          req.NIK,
		Nama:         req.Nama,
		Email:        req.Email,
		NoTelepon:    req.NoTelepon,
		JenisKelamin: req.JenisKelamin,
		TanggalLahir: req.TanggalLahir,
		Alamat:       req.Alamat,
		Status:       status,
		SkemaID:      req.SkemaID,
	}
	if err := h.DB.Create(&peserta).Error; err != nil {
		utils.ServerError(c)
		return
	}

	if err := h.DB.Preload("Skema").First(&peserta, peserta.ID).Error; err != nil {
		utils.ServerError(c)
		return
	}
	utils.Success(c, http.StatusCreated, "Peserta berhasil ditambahkan", peserta)
}

// UpdatePeserta mengubah data peserta berdasarkan ID.
func (h *Handler) UpdatePeserta(c *gin.Context) {
	id, ok := parseID(c)
	if !ok {
		return
	}

	var req dto.PesertaRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		utils.BindError(c, err)
		return
	}

	var peserta models.Peserta
	if err := h.DB.First(&peserta, id).Error; err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			utils.Error(c, http.StatusNotFound, "Peserta tidak ditemukan", nil)
			return
		}
		utils.ServerError(c)
		return
	}

	exists, err := h.skemaExists(req.SkemaID)
	if err != nil {
		utils.ServerError(c)
		return
	}
	if !exists {
		utils.Error(c, http.StatusUnprocessableEntity, "Validasi gagal, periksa kembali input Anda", gin.H{"skema_id": "skema tidak ditemukan"})
		return
	}

	conflicts, err := h.pesertaConflicts(req.NIK, req.Email, id)
	if err != nil {
		utils.ServerError(c)
		return
	}
	if len(conflicts) > 0 {
		utils.Error(c, http.StatusConflict, "Data peserta bentrok dengan data yang sudah ada", conflicts)
		return
	}

	peserta.NIK = req.NIK
	peserta.Nama = req.Nama
	peserta.Email = req.Email
	peserta.NoTelepon = req.NoTelepon
	peserta.JenisKelamin = req.JenisKelamin
	peserta.TanggalLahir = req.TanggalLahir
	peserta.Alamat = req.Alamat
	peserta.SkemaID = req.SkemaID
	if req.Status != "" {
		peserta.Status = req.Status
	}
	peserta.Skema = nil // hindari GORM menyimpan ulang relasi lama

	if err := h.DB.Save(&peserta).Error; err != nil {
		utils.ServerError(c)
		return
	}

	if err := h.DB.Preload("Skema").First(&peserta, peserta.ID).Error; err != nil {
		utils.ServerError(c)
		return
	}
	utils.Success(c, http.StatusOK, "Peserta berhasil diubah", peserta)
}

// DeletePeserta menghapus peserta. Wajib menyertakan ?confirm=true sebagai konfirmasi penghapusan.
func (h *Handler) DeletePeserta(c *gin.Context) {
	id, ok := parseID(c)
	if !ok {
		return
	}

	if c.Query("confirm") != "true" {
		utils.Error(c, http.StatusBadRequest, "Penghapusan harus dikonfirmasi: tambahkan query ?confirm=true", nil)
		return
	}

	var peserta models.Peserta
	if err := h.DB.First(&peserta, id).Error; err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			utils.Error(c, http.StatusNotFound, "Peserta tidak ditemukan", nil)
			return
		}
		utils.ServerError(c)
		return
	}

	if err := h.DB.Delete(&peserta).Error; err != nil {
		utils.ServerError(c)
		return
	}
	utils.Success(c, http.StatusOK, "Peserta berhasil dihapus", nil)
}
