package routes

import (
	"net/http"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"peserta-sertifikasi-api/config"
	"peserta-sertifikasi-api/handlers"
	"peserta-sertifikasi-api/middleware"
	"peserta-sertifikasi-api/utils"
)

// Setup menyusun router Gin beserta seluruh endpoint API.
func Setup(db *gorm.DB, cfg *config.Config) *gin.Engine {
	r := gin.New()
	r.Use(gin.Logger())
	// Recovery kustom: panic apa pun dijawab dengan JSON error yang rapi.
	r.Use(gin.CustomRecovery(func(c *gin.Context, _ interface{}) {
		utils.ServerError(c)
		c.Abort()
	}))
	r.Use(middleware.CORS())

	h := handlers.New(db, cfg)

	r.NoRoute(func(c *gin.Context) {
		utils.Error(c, http.StatusNotFound, "Endpoint tidak ditemukan", nil)
	})
	r.NoMethod(func(c *gin.Context) {
		utils.Error(c, http.StatusMethodNotAllowed, "Method tidak diizinkan", nil)
	})

	api := r.Group("/api")
	{
		api.GET("/health", func(c *gin.Context) {
			utils.Success(c, http.StatusOK, "API berjalan", gin.H{"status": "ok"})
		})

		// Publik
		api.POST("/auth/login", h.Login)

		// Terproteksi (wajib token JWT)
		protected := api.Group("")
		protected.Use(middleware.Auth(cfg.JWTSecret))
		{
			protected.GET("/auth/me", h.Me)
			protected.GET("/dashboard", h.Dashboard)

			protected.GET("/skema", h.ListSkema)
			protected.POST("/skema", h.CreateSkema)
			protected.GET("/skema/:id", h.GetSkema)
			protected.PUT("/skema/:id", h.UpdateSkema)
			protected.DELETE("/skema/:id", h.DeleteSkema)

			protected.GET("/peserta", h.ListPeserta)
			protected.POST("/peserta", h.CreatePeserta)
			protected.GET("/peserta/:id", h.GetPeserta)
			protected.PUT("/peserta/:id", h.UpdatePeserta)
			protected.DELETE("/peserta/:id", h.DeletePeserta)
		}
	}

	return r
}
