# Symfony Auth Example

## General information
This repository contains sample Symfony code

## Deployment

The following environment variables can be used to configure the application:
* ```APP_ENV``` - sets the application environment. Possible values: ```prod```, ```dev```. Default value - ```prod```
* ```APP_DEBUG``` - controls the debug mode. Possible values: ```0```, ```1```. Default value - ```0```.
* ```SAAS_CLUSTER``` - specifies the cluster of clients served by this service instance. Default value - ```production```.
* ```CONSUL_HTTP_ADDR``` - sets the Consul address. Default value: ```http://consul:8500```.
* ```VAULT_HTTP_ADDR``` - sets the Vault address. Default value: ```http://vault:8200```.
* ```VAULT_POLICY``` - sets the policy used by the Vault token. Default value: ```symfony-example-policy```.
* ```CACHE_LONG``` - the lifetime (in seconds) of the longest cache pool. Used to cache information. Default value: ```60```.
* ```CACHE_MEDIUM``` - the lifetime (in seconds) of the mid-term cache pool. Used to cache information. Default value: ```30```.
* ```CACHE_SHORT``` - the lifetime (in seconds) of the most dynamic cache pool. Used to cache information. Default value: ```12```.

Before starting the container, you need to configure the environment:
* Add to Vault policy, which will be used by the application to access secrets:
```
vault policy write symfony-example-policy ./policy.hcl
```
