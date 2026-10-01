package utils

import (
	"encoding/json"
	"errors"
	"fmt"
	"net/http"
	"reflect"
	"strings"

	"github.com/gin-gonic/gin"
	"github.com/gin-gonic/gin/binding"
	"github.com/go-playground/validator/v10"
)

// RegisterJSONTagName membuat validator memakai nama field JSON (bukan nama field struct)
// pada pesan error, sehingga klien menerima kunci seperti "nama" bukan "Nama".
func RegisterJSONTagName() {
	if v, ok := binding.Validator.Engine().(*validator.Validate); ok {
		v.RegisterTagNameFunc(func(f reflect.StructField) string {
			name := strings.SplitN(f.Tag.Get("json"), ",", 2)[0]
			if name == "-" {
				return ""
			}
			return name
		})
	}
}

func fieldMessage(fe validator.FieldError) string {
	switch fe.Tag() {
	case "required":
		return "wajib diisi"
	case "email":
		return "format email tidak valid"
	case "len":
		return fmt.Sprintf("harus tepat %s karakter", fe.Param())
	case "min":
		return fmt.Sprintf("minimal %s karakter", fe.Param())
	case "max":
		return fmt.Sprintf("maksimal %s karakter", fe.Param())
	case "numeric":
		return "harus berupa angka"
	case "oneof":
		return fmt.Sprintf("harus salah satu dari: %s", strings.ReplaceAll(fe.Param(), " ", ", "))
	case "datetime":
		return "format tanggal harus YYYY-MM-DD"
	default:
		return "tidak valid"
	}
}

// BindError menerjemahkan error hasil ShouldBindJSON menjadi response yang ramah pengguna.
func BindError(c *gin.Context, err error) {
	var ve validator.ValidationErrors
	if errors.As(err, &ve) {
		errs := make(map[string]string, len(ve))
		for _, fe := range ve {
			errs[fe.Field()] = fieldMessage(fe)
		}
		Error(c, http.StatusUnprocessableEntity, "Validasi gagal, periksa kembali input Anda", errs)
		return
	}

	var syntaxErr *json.SyntaxError
	var typeErr *json.UnmarshalTypeError
	switch {
	case errors.As(err, &syntaxErr):
		Error(c, http.StatusBadRequest, "Format JSON tidak valid", nil)
	case errors.As(err, &typeErr):
		Error(c, http.StatusBadRequest, fmt.Sprintf("Tipe data field '%s' tidak sesuai", typeErr.Field), nil)
	default:
		Error(c, http.StatusBadRequest, "Request body tidak valid atau kosong", nil)
	}
}
