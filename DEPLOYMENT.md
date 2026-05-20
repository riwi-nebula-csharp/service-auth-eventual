# Guía de Deployment - Service Auth Eventual

## Descripción
Este proyecto implementa un pipeline de CI/CD con GitHub Actions que automáticamente:
1. Construye una imagen Docker cuando hay cambios en la rama `main`
2. Push la imagen a GitHub Container Registry (GHCR)
3. Despliega la imagen en un VPS

## Archivos creados

- **Dockerfile**: Configura el entorno PHP 8.2 con Laravel
- **.github/workflows/production.yml**: Workflow de CI/CD
- **nginx.conf**: Configuración de Nginx
- **supervisord.conf**: Gestión de procesos (PHP-FPM + Nginx)
- **.dockerignore**: Archivos excluidos en la imagen Docker

## Configuración de Secretos en GitHub

Debes configurar los siguientes secretos en tu repositorio GitHub:

### Pasos:
1. Ve a tu repositorio en GitHub
2. Settings → Secrets and variables → Actions
3. Haz clic en "New repository secret" y agrega:

### Secretos requeridos:

| Nombre | Descripción | Ejemplo |
|--------|-------------|---------|
| `VPS_HOST` | IP o dominio del VPS | `192.168.1.100` |
| `VPS_USER` | Usuario SSH (generalmente root) | `root` |
| `SSH_KEY` | Clave privada SSH | `-----BEGIN OPENSSH PRIVATE KEY-----` |
| `SSH_PORT` | Puerto SSH (opcional, default: 22) | `22` |
| `APP_KEY` | Clave de encriptación Laravel | `base64:xxx` (genera con `php artisan key:generate`) |
| `DB_HOST` | Host de la base de datos | `localhost` |
| `DB_PORT` | Puerto de la BD | `3306` |
| `DB_DATABASE` | Nombre de la base de datos | `service_auth` |
| `DB_USERNAME` | Usuario de la BD | `root` |
| `DB_PASSWORD` | Contraseña de la BD | `tu_contraseña` |

### Cómo generar la SSH Key:
```bash
ssh-keygen -t ed25519 -C "github-actions"
# Guarda en un archivo, luego copia el contenido de la clave privada
cat ~/.ssh/id_ed25519
```

### Cómo generar APP_KEY:
```bash
php artisan key:generate
# Copia la clave del archivo .env
```

## Flujo de Deployment

1. **Hacer push a main**
   ```bash
   git add .
   git commit -m "cambios"
   git push origin main
   ```

2. **GitHub Actions automáticamente:**
   - Construye la imagen Docker
   - Push a GHCR
   - Se conecta al VPS vía SSH
   - Detiene el contenedor anterior
   - Inicia el nuevo contenedor
   - Accede en: `http://tu-vps.com:8062`

## Preparación del VPS

Asegúrate que tu VPS tenga:
- Docker instalado
- SSH accesible
- Puerto 8062 abierto en el firewall

## Comandos locales útiles

```bash
# Construir la imagen localmente
docker build -t service-auth-eventual .

# Ejecutar localmente
docker run -p 8062:8080 service-auth-eventual

# Ejecutar con variables de entorno
docker run -p 8062:8080 \
  -e APP_ENV=production \
  -e APP_KEY=base64:xxx \
  service-auth-eventual
```

## Troubleshooting

- **Errores de autenticación**: Verifica que SSH_KEY sea la clave PRIVADA
- **Contenedor no inicia**: Revisa los logs en el VPS: `docker logs service-auth-eventual`
- **Imagen no se construye**: Verifica que el Dockerfile esté en la raíz del proyecto
