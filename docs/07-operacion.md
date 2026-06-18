# 07 — Operación y Despliegue

Guía para correr, desplegar y mantener AULITA. Complementa el
[Documento Técnico](02-tecnico.md) y el [Roadmap](06-roadmap.md).

---

## 1. Requisitos

| Componente | Local (dev) | Producción |
|------------|-------------|-----------|
| PHP | 8.2+ (XAMPP) | **8.4** (Docker) |
| MySQL | 8 (XAMPP) | 8 (contenedor) |
| Node | 20+ | 20+ (build) |
| Cache/Colas | `database` | **Redis** |
| Workers | manual | **Supervisor** |
| Servidor web | `php artisan serve` | **Nginx** |

---

## 2. Puesta en marcha local

```bash
cd c:\xampp\proyectos-laravel\aulita
composer install
copy .env.example .env        # o cp en bash
php artisan key:generate
php artisan migrate --seed     # crea escuela demo + usuarios
npm install && npm run build   # o npm run dev
php artisan serve
php artisan queue:work         # procesa notificaciones (otra terminal)
```

**Usuarios sembrados** (contraseña `Password#1`):
- Super Admin: `superadmin@aulita.test`
- Director (Colegio Demo): `director@colegiodemo.test`

---

## 3. Variables de entorno clave

| Variable | Descripción |
|----------|-------------|
| `APP_KEY` | Generada con `key:generate`. Imprescindible. |
| `DB_*` | Conexión MySQL (`aulita`). |
| `QUEUE_CONNECTION` | `database` (local) / `redis` (prod). |
| `CACHE_STORE` | `database` (local) / `redis` (prod). |
| `REDIS_*` | Host/puerto de Redis en producción. |
| `MAIL_*` | SMTP real en producción (local: `log`). |
| `WHATSAPP_TOKEN` | Token de Meta Cloud API. |
| `WHATSAPP_PHONE_ID` | ID del número de WhatsApp Business. |
| `WHATSAPP_API_VERSION` | Ej. `v21.0`. |

> Sin `WHATSAPP_TOKEN`/`WHATSAPP_PHONE_ID`, el canal de WhatsApp hace **no-op**
> (no rompe el flujo); el correo se sigue enviando.

---

## 4. Despliegue con Docker

```bash
docker compose build
docker compose up -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force   # solo primera vez
docker compose exec app php artisan storage:link
docker compose exec app php artisan config:cache route:cache view:cache
```

Servicios ([docker-compose.yml](../docker-compose.yml)):
- **app** — PHP-FPM 8.4 ([docker/php/Dockerfile](../docker/php/Dockerfile)).
- **nginx** — sirve `public/` y hace proxy a PHP-FPM ([docker/nginx/default.conf](../docker/nginx/default.conf)).
- **mysql** — MySQL 8 con volumen persistente.
- **redis** — cache y colas.
- **worker** — Supervisor con `queue:work` (×2) y `schedule:work`
  ([docker/supervisor/supervisord.conf](../docker/supervisor/supervisord.conf)).

---

## 5. Tareas programadas (Scheduler)

El comando de faltas automáticas corre **cada minuto** y procesa cada escuela a su
hora de corte:

```bash
php artisan asistencia:detectar-faltas        # respeta hora de corte
php artisan asistencia:detectar-faltas --force --school=1   # manual
```

En producción lo dispara `schedule:work` (contenedor `worker`/Supervisor). En un
servidor sin Docker, agregar al cron del sistema:

```
* * * * * cd /ruta/aulita && php artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Colas y notificaciones

- Las notificaciones (correo + WhatsApp) son `ShouldQueue`.
- La ráfaga de las 07:15 se **espacia** (throttling) para respetar el rate limit de Meta.
- Procesar con `php artisan queue:work redis --tries=3` (Supervisor en prod).
- Reintentos con backoff; los fallos quedan en `failed_jobs` (`queue:retry`).

---

## 7. Seguridad (hardening)

| Control | Implementación |
|---------|----------------|
| Multitenancy | `TenantScope` + `ResolveTenant`; `school_id` nunca del cliente. |
| Autorización | Permisos Spatie + Form Requests + `Gate::before` (Super Admin). |
| Rate limiting | `throttle:activacion` (6/min) y `throttle:escaneo` (120/min); login (Breeze, 5 intentos). |
| Cabeceras | `SecurityHeaders` (nosniff, SAMEORIGIN, Referrer-Policy). |
| Archivos | Evidencias/adjuntos en disco privado, descarga por ruta autenticada. |
| Contraseñas | Política de complejidad en activación de padres. |
| Firmas | Registro no repudiable de fecha/hora/IP. |
| Auditoría | Bitácora de toda acción (Observer). |

---

## 8. Calidad y CI

- **Pint**: `vendor/bin/pint` (formatea) / `vendor/bin/pint --test` (verifica).
- **Pruebas**: `php artisan test` (sqlite en memoria).
- **CI** ([.github/workflows/ci.yml](../.github/workflows/ci.yml)): instala, construye assets,
  corre Pint y PHPUnit en cada push/PR (PHP 8.4).

---

## 9. Respaldos y mantenimiento

- **BD**: `mysqldump aulita` programado diario.
- **Archivos**: respaldar `storage/app/private` (evidencias, adjuntos).
- **Logs**: `storage/logs`; rotar en producción.
- **Limpieza**: `php artisan auth:clear-resets`; purgar `account_activations` vencidas.

---

## 10. Checklist de salida a producción

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` fijada.
- [ ] `MAIL_*` y `WHATSAPP_*` reales; plantillas de Meta aprobadas.
- [ ] `QUEUE_CONNECTION=redis`, `CACHE_STORE=redis`.
- [ ] `config:cache route:cache view:cache` ejecutados.
- [ ] `storage:link` creado; permisos de `storage/` correctos.
- [ ] Supervisor activo (`queue:work` + `schedule:work`).
- [ ] HTTPS configurado en Nginx.
- [ ] Respaldos automáticos verificados.
