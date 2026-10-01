package config

import (
	"os"
	"strconv"

	"github.com/joho/godotenv"
)

// Config menampung seluruh konfigurasi aplikasi yang dibaca dari environment / file .env.
type Config struct {
	AppPort        string
	DBDriver       string
	DBPath         string
	DBHost         string
	DBPort         string
	DBUser         string
	DBPassword     string
	DBName         string
	JWTSecret      string
	JWTExpireHours int
	AdminUsername  string
	AdminPassword  string
}

// Load membaca file .env (jika ada) lalu mengisi Config dengan nilai default yang aman untuk demo.
func Load() *Config {
	_ = godotenv.Load()

	hours, err := strconv.Atoi(getEnv("JWT_EXPIRE_HOURS", "24"))
	if err != nil || hours <= 0 {
		hours = 24
	}

	return &Config{
		AppPort:        getEnv("APP_PORT", "8080"),
		DBDriver:       getEnv("DB_DRIVER", "sqlite"),
		DBPath:         getEnv("DB_PATH", "peserta.db"),
		DBHost:         getEnv("DB_HOST", "127.0.0.1"),
		DBPort:         getEnv("DB_PORT", "3306"),
		DBUser:         getEnv("DB_USER", "root"),
		DBPassword:     getEnv("DB_PASSWORD", ""),
		DBName:         getEnv("DB_NAME", "db_peserta_sertifikasi"),
		JWTSecret:      getEnv("JWT_SECRET", "dummy-secret-ubah-saya"),
		JWTExpireHours: hours,
		AdminUsername:  getEnv("ADMIN_USERNAME", "admin"),
		AdminPassword:  getEnv("ADMIN_PASSWORD", "admin123"),
	}
}

func getEnv(key, fallback string) string {
	if v, ok := os.LookupEnv(key); ok && v != "" {
		return v
	}
	return fallback
}
