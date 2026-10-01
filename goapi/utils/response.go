package utils

import (
	"net/http"

	"github.com/gin-gonic/gin"
)

// Success mengirim response JSON sukses dengan format seragam.
func Success(c *gin.Context, status int, message string, data interface{}) {
	c.JSON(status, gin.H{
		"success": true,
		"message": message,
		"data":    data,
	})
}

// Error mengirim response JSON gagal dengan format seragam.
func Error(c *gin.Context, status int, message string, errs interface{}) {
	body := gin.H{
		"success": false,
		"message": message,
	}
	if errs != nil {
		body["errors"] = errs
	}
	c.JSON(status, body)
}

// ServerError dipakai saat terjadi kesalahan tak terduga (mis. database error).
// Detail error asli tidak dikirim ke klien agar tidak membocorkan informasi internal.
func ServerError(c *gin.Context) {
	Error(c, http.StatusInternalServerError, "Terjadi kesalahan pada server, silakan coba lagi", nil)
}
