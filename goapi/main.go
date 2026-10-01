package main

import (
	"log"

	"peserta-sertifikasi-api/config"
	"peserta-sertifikasi-api/database"
	"peserta-sertifikasi-api/routes"
	"peserta-sertifikasi-api/utils"
)

func main() {
	cfg := config.Load()
	utils.RegisterJSONTagName()

	db, err := database.Connect(cfg)
	if err != nil {
		log.Fatalf("gagal terhubung ke database: %v", err)
	}
	if err := database.Migrate(db); err != nil {
		log.Fatalf("gagal migrasi database: %v", err)
	}
	if err := database.Seed(db, cfg); err != nil {
		log.Fatalf("gagal seeding data awal: %v", err)
	}

	router := routes.Setup(db, cfg)
	log.Printf("Server berjalan di http://localhost:%s (database: %s)", cfg.AppPort, cfg.DBDriver)
	if err := router.Run(":" + cfg.AppPort); err != nil {
		log.Fatalf("gagal menjalankan server: %v", err)
	}
}
