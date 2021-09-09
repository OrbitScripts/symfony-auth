{{$cluster := or (env "SAAS_CLUSTER") "production"}}
# This file is the entry point to configure your own services.
# Files in the packages/ subdirectory configure your dependencies.

# Put parameters here that don't need to change on each machine where the app is deployed
# https://symfony.com/doc/current/best_practices/configuration.html#application-related-configuration
parameters:
    app.cluster: '{{ $cluster }}'
    customer_api_schema: '{{ or (env "CUSTOMER_API_SCHEMA") "http"}}'

    {{ range $idx, $node := service (printf "%s.redis" $cluster) }}{{ if eq $idx 0 }}
    redis.host: {{$node.Address}}
    redis.port: {{$node.Port}}{{end}}{{end}}

    saas.config_file: '%kernel.project_dir%/config/saas.php'

    cache.long: {{ or (env "CACHE_LONG") "1800"}}
    cache.medium: {{ or (env "CACHE_MEDIUM") "600"}}
    cache.short: {{ or (env "CACHE_SHORT") "60"}}
services:
    # default configuration for services in *this* file
    _defaults:
        autowire: true      # Automatically injects dependencies in your services.
        autoconfigure: true # Automatically registers your services as commands, event subscribers, etc.

    # makes classes in src/ available to be used as services
    # this creates a service per class whose id is the fully-qualified class name
    App\:
        resource: '../src/*'
        exclude: '../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}'

    # controllers are imported separately to make sure services can be injected
    # as action arguments even if you don't extend any base controller class
    App\Controller\:
        resource: '../src/Controller'
        tags: ['controller.service_arguments']

    # add more service definitions when explicit configuration is needed
    # please note that last definitions always *replace* previous ones

    App\Service\SaaS\CustomersConfigArrayFileService:
        arguments: ['%saas.config_file%']

    App\Service\Auth\CachedUsersService:
        arguments:
            $cache: '@cache.term.medium'

    App\Service\Sales\CachedOrdersService:
        arguments:
            $cache: '@cache.term.short'

    App\Service\Auth\UsersService: '@App\Service\Auth\CachedUsersService'
