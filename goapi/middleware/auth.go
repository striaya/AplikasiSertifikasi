package middleware

import (
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"

	"peserta-sertifikasi-api/utils"
)

// Auth membatasi akses hanya untuk request dengan header "Authorization: Bearer <token>" yang valid.
func Auth(secret string) gin.HandlerFunc {
	return func(c *gin.Context) {
		parts := strings.SplitN(c.GetHeader("Authorization"), " ", 2)
		if len(parts) != 2 || !strings.EqualFold(parts[0], "Bearer") {
			utils.Error(c, http.StatusUnauthorized, "Akses ditolak: token tidak ditemukan", nil)
			c.Abort()
			return
		}

		claims, err := utils.ParseToken(secret, strings.TrimSpace(parts[1]))
		if err != nil {
			utils.Error(c, http.StatusUnauthorized, "Akses ditolak: token tidak valid atau sudah kedaluwarsa", nil)
			c.Abort()
			return
		}

		c.Set("user_id", claims.UserID)
		c.Set("username", claims.Username)
		c.Next()
	}
}
