vault {
  address = "${VAULT_HTTP_ADDR}"

  ssl {
    enabled = false
    verify = false
  }

  token = "${VAULT_TOKEN}"
}

template {
  source = "/var/www/html/config/services.yaml.tpl"
  destination = "/var/www/html/config/services.yaml"
}

template {
  source = "/var/www/html/config/saas.php.tpl"
  destination = "/var/www/html/config/saas.php"
}

template {
  source = "/var/www/html/config/jwt/public.pem.tpl"
  destination = "/var/www/html/config/jwt/public.pem"
}

template {
  source = "/var/www/html/config/jwt/private.pem.tpl"
  destination = "/var/www/html/config/jwt/private.pem"
}

template {
  source = "/var/www/html/config/packages/lexik_jwt_authentication.yaml.tpl"
  destination = "/var/www/html/config/packages/lexik_jwt_authentication.yaml"
}

exec {
  command = "php-fpm"
  splay = "5s"
  reload_signal = "SIGUSR2"
}

wait {
  min = "5s"
  max = "10s"
}
