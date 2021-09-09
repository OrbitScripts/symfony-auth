{{ with secret "secrets/jwt" }}{{ .Data.private }}{{end}}
