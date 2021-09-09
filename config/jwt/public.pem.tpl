{{ with secret "secrets/jwt" }}{{ .Data.public }}{{end}}
