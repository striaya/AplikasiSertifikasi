package handlers

import (
	"errors"
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"peserta-sertifikasi-api/dto"
	"peserta-sertifikasi-api/models"
	"peserta-sertifikasi-api/utils"
)

// kodeSkemaTaken mengecek apakah kode skema sudah dipakai skema lain.
func (h *Handler) kodeSkemaTaken(kode string, excludeID uint) (bool, error) {
	var count int64
	q := h.DB.Model(&models.Skema{}).Where("kode_skema = ?", kode)
	if excludeID != 0 {
		q = q.Where("id <> ?", excludeID)
	}
	err := q.Count(&count).Error
	return count > 0, err
}

// ListSkema menampilkan seluruh skema (opsional ?search= pada kode/nama).
func (h *Handler) ListSkema(c *gin.Context) {
	q := h.DB.Model(&models.Skema{})
	if s := strings.TrimSpace(c.Query("search")); s != "" {
		like := "%" + s + "%"
		q = q.Where("kode_skema LIKE ? OR nama_skema LIKE ?", like, like)
	}

	items := []models.Skema{}
	if err := q.Order("id ASC").Find(&items).Error; err != nil {
		utils.ServerError(c)
		return
	}
	utils.Success(c, http.StatusOK, "Data skema berhasil diambil", items)
}

// GetSkema menampilkan satu skema berdasarkan ID.
func (h *Handler) GetSkema(c *gin.Context) {
	id, ok := parseID(c)
	if !ok {
		return
	}

	var skema models.Skema
	if err := h.DB.First(&skema, id).Error; err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			utils.Error(c, http.StatusNotFound, "Skema tidak ditemukan", nil)
			return
		}
		utils.ServerError(c)
		return
	}
	utils.Success(c, http.StatusOK, "Detail skema berhasil diambil", skema)
}

// CreateSkema menambah skema baru.
func (h *Handler) CreateSkema(c *gin.Context) {
	var req dto.SkemaRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		utils.BindError(c, err)
		return
	}

	taken, err := h.kodeSkemaTaken(req.KodeSkema, 0)
	if err != nil {
		utils.ServerError(c)
		return
	}
	if taken {
		utils.Error(c, http.StatusConflict, "Kode skema sudah digunakan", gin.H{"kode_skema": "sudah digunakan"})
		return
	}

	skema := models.Skema{
		KodeSkema: req.KodeSkema,
		NamaSkema: req.NamaSkema,
		Jenis:     req.Jenis,
		Deskripsi: req.Deskripsi,
	}
	if err := h.DB.Create(&skema).Error; err != nil {
		utils.ServerError(c)
		return
	}
	utils.Success(c, http.StatusCreated, "Skema berhasil ditambahkan", skema)
}

// UpdateSkema mengubah skema berdasarkan ID.
func (h *Handler) UpdateSkema(c *gin.Context) {
	id, ok := parseID(c)
	if !ok {
		return
	}

	var req dto.SkemaRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		utils.BindError(c, err)
		return
	}

	var skema models.Skema
	if err := h.DB.First(&skema, id).Error; err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			utils.Error(c, http.StatusNotFound, "Skema tidak ditemukan", nil)
			return
		}
		utils.ServerError(c)
		return
	}

	taken, err := h.kodeSkemaTaken(req.KodeSkema, id)
	if err != nil {
		utils.ServerError(c)
		return
	}
	if taken {
		utils.Error(c, http.StatusConflict, "Kode skema sudah digunakan", gin.H{"kode_skema": "sudah digunakan"})
		return
	}

	skema.KodeSkema = req.KodeSkema
	skema.NamaSkema = req.NamaSkema
	skema.Jenis = req.Jenis
	skema.Deskripsi = req.Deskripsi
	if err := h.DB.Save(&skema).Error; err != nil {
		utils.ServerError(c)
		return
	}
	utils.Success(c, http.StatusOK, "Skema berhasil diubah", skema)
}

// DeleteSkema menghapus skema. Ditolak (409) jika masih dipakai oleh peserta.
func (h *Handler) DeleteSkema(c *gin.Context) {
	id, ok := parseID(c)
	if !ok {
		return
	}

	var skema models.Skema
	if err := h.DB.First(&skema, id).Error; err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			utils.Error(c, http.StatusNotFound, "Skema tidak ditemukan", nil)
			return
		}
		utils.ServerError(c)
		return
	}

	var used int64
	if err := h.DB.Model(&models.Peserta{}).Where("skema_id = ?", id).Count(&used).Error; err != nil {
		utils.ServerError(c)
		return
	}
	if used > 0 {
		utils.Error(c, http.StatusConflict, "Skema tidak dapat dihapus karena masih digunakan oleh data peserta", gin.H{"jumlah_peserta": used})
		return
	}

	if err := h.DB.Delete(&skema).Error; err != nil {
		utils.ServerError(c)
		return
	}
	utils.Success(c, http.StatusOK, "Skema berhasil dihapus", nil)
}
