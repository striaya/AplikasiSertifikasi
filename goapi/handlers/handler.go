package handlers

import (
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"peserta-sertifikasi-api/config"
	"peserta-sertifikasi-api/utils"
)

// Handler menampung dependensi (database & konfigurasi) yang dipakai semua endpoint.
type Handler struct {
	DB  *gorm.DB
	Cfg *config.Config
}

// New membuat Handler baru.
func New(db *gorm.DB, cfg *config.Config) *Handler {
	return &Handler{DB: db, Cfg: cfg}
}

// parseID membaca parameter path :id dan memastikan berupa angka positif.
func parseID(c *gin.Context) (uint, bool) {
	id, err := strconv.ParseUint(c.Param("id"), 10, 64)
	if err != nil || id == 0 {
		utils.Error(c, http.StatusBadRequest, "ID tidak valid", nil)
		return 0, false
	}
	return uint(id), true
}
