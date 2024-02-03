## Hosts file
```bash
echo '127.0.0.1 wp.local' | sudo tee -a /etc/hosts
```
```bash
echo '127.0.0.1 monitor.local' | sudo tee -a /etc/hosts
```

## Containers
```bash
docker compose down --remove-orphans
```
```bash
docker-compose up -d
```

## Certificates
```bash
mkdir certs && cd certs && mkcert wp.local
```

## Permissions (for local development)
```bash
sudo chown -R $USER:$USER ./wordpress

```

## The site and traefik dashboard
wp: https://wp.local/  
traefik: http://monitor.local  
