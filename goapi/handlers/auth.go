package handlers

import (
	"errors"
	"net/http"

	"github.com/gin-gonic/gin"
	"golang.org/x/crypto/bcrypt"
	"gorm.io/gorm"

	"peserta-sertifikasi-api/dto"
	"peserta-sertifikasi-api/models"
	"peserta-sertifikasi-api/utils"
)

// Login memverifikasi kredensial admin dan mengembalikan token JWT.
func (h *Handler) Login(c *gin.Context) {
	var req dto.LoginRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		utils.BindError(c, err)
		return
	}

	var user models.User
	err := h.DB.Where("username = ?", req.Username).First(&user).Error
	if err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			utils.Error(c, http.StatusUnauthorized, "Username atau password salah", nil)
			return
		}
		utils.ServerError(c)
		return
	}

   if user.Username != req.Username {
       utils.Error(c, http.StatusUnauthorized, "Username atau password salah", nil)
       return
   }
   
	if err := bcrypt.CompareHashAndPassword([]byte(user.Password), []byte(req.Password)); err != nil {
		utils.Error(c, http.StatusUnauthorized, "Username atau password salah", nil)
		return
	}

	token, err := utils.GenerateToken(h.Cfg.JWTSecret, h.Cfg.JWTExpireHours, user.ID, user.Username)
	if err != nil {
		utils.ServerError(c)
		return
	}

	utils.Success(c, http.StatusOK, "Login berhasil", gin.H{
		"token": token,
		"type":  "Bearer",
		"user":  user,
	})
}

// Me mengembalikan data admin yang sedang login.
func (h *Handler) Me(c *gin.Context) {
	userID, _ := c.Get("user_id")

	var user models.User
	if err := h.DB.First(&user, userID).Error; err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			utils.Error(c, http.StatusUnauthorized, "Akun tidak ditemukan", nil)
			return
		}
		utils.ServerError(c)
		return
	}

	utils.Success(c, http.StatusOK, "Data pengguna berhasil diambil", user)
	
}
