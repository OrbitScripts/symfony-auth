lexik_jwt_authentication:
    secret_key: '%kernel.project_dir%/config/jwt/private.pem'
    public_key: '%kernel.project_dir%/config/jwt/public.pem'{{ with secret "secrets/jwt" }}
    pass_phrase: '{{ .Data.password }}'{{ end }}
    token_ttl: 86400
