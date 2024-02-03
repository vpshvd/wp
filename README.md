## Hosts file
etc/hosts:  
127.0.0.1 wp.local  
127.0.0.1 monitor.local

## Containers
```bash
docker compose down --remove-orphans
```

```bash
docker-compose up -d
```

## Certificates
Inside project repo:
```bash
mkcert wp.local
```
and move the certs to the ./certs folder

## The site and traefik dashboard
wp: https://wp.local/  
traefik: http://monitor.local  


## Permissions (for local development)
```bash
sudo chown -R $USER:$USER ./wordpress

```
