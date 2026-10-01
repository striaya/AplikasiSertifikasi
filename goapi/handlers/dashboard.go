package handlers

import (
	"net/http"

	"github.com/gin-gonic/gin"

	"peserta-sertifikasi-api/models"
	"peserta-sertifikasi-api/utils"
)

type skemaSummary struct {
	ID            uint   `json:"id"`
	KodeSkema     string `json:"kode_skema"`
	NamaSkema     string `json:"nama_skema"`
	JumlahPeserta int64  `json:"jumlah_peserta"`
}

type statusSummary struct {
	Status string `json:"status"`
	Jumlah int64  `json:"jumlah"`
}

// Dashboard mengembalikan ringkasan/indikator data untuk halaman dashboard.
func (h *Handler) Dashboard(c *gin.Context) {
	var totalPeserta, totalSkema int64
	if err := h.DB.Model(&models.Peserta{}).Count(&totalPeserta).Error; err != nil {
		utils.ServerError(c)
		return
	}
	if err := h.DB.Model(&models.Skema{}).Count(&totalSkema).Error; err != nil {
		utils.ServerError(c)
		return
	}

	perSkema := []skemaSummary{}
	err := h.DB.Table("skema").
		Select("skema.id, skema.kode_skema, skema.nama_skema, COUNT(peserta.id) AS jumlah_peserta").
		Joins("LEFT JOIN peserta ON peserta.skema_id = skema.id").
		Group("skema.id, skema.kode_skema, skema.nama_skema").
		Order("skema.id ASC").
		Scan(&perSkema).Error
	if err != nil {
		utils.ServerError(c)
		return
	}

	perStatus := []statusSummary{}
	if err := h.DB.Table("peserta").
		Select("status, COUNT(*) AS jumlah").
		Group("status").
		Scan(&perStatus).Error; err != nil {
		utils.ServerError(c)
		return
	}

	terbaru := []models.Peserta{}
	if err := h.DB.Preload("Skema").Order("id DESC").Limit(5).Find(&terbaru).Error; err != nil {
		utils.ServerError(c)
		return
	}

	utils.Success(c, http.StatusOK, "Data dashboard berhasil diambil", gin.H{
		"total_peserta":      totalPeserta,
		"total_skema":        totalSkema,
		"peserta_per_skema":  perSkema,
		"peserta_per_status": perStatus,
		"peserta_terbaru":    terbaru,
	})
}
