```bash
docker-compose down
```

```bash
docker-compose up -d
```
Inside project repo:
```bash
mkcert wp.local
```

wp: https://wp.local/  
traefik: http://monitor.local  

etc/hosts:  
127.0.0.1 wp.local  
127.0.0.1 monitor.local  
