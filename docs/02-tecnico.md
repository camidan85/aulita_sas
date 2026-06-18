# 02 — Documento Técnico

## 1. Stack tecnológico

| Capa | Tecnología | Notas |
|------|-----------|-------|
| Lenguaje | **PHP 8.4** | Tipado estricto en servicios y repositorios |
| Framework | **Laravel 12+** | Base del backend y del renderizado Blade |
| Auth | **Laravel Breeze** (Blade) | Base, extendida con roles y portal de padres |
| Permisos | **spatie/laravel-permission** | 7 roles + permisos granulares |
| Colas | **Laravel Queues + Redis** | Notificaciones, jobs pesados |
| Cache | **Redis** | Cache de consultas y sesiones |
| Eventos/Jobs | **Events, Listeners, Jobs** | Desacople de asistencia ↔ notificación |
| Tareas programadas | **Laravel Scheduler** | Faltas automáticas, alertas de riesgo |
| Vistas | **Blade + Bootstrap 5.3** | Responsive, mobile-first |
| Interacción | **Alpine.js** | Reactividad ligera en cliente |
| Gráficas | **Chart.js** | Dashboards |
| Reportes | **Laravel Excel + DomPDF** | Exportaciones y boletas/kardex |
| Base de datos | **MySQL 8** | InnoDB, utf8mb4 |
| Notificaciones | **Mail + WhatsApp (Meta Cloud API)** | Canal WhatsApp personalizado |
| Infra | **Docker + Nginx + Supervisor** | Supervisor administra los workers de cola |
| Calidad | **PHPUnit + Laravel Pint** | Pruebas y estilo de código |

---

## 2. Arquitectura

### 2.1 Estilo arquitectónico
Aplicación **monolítica modular** sobre Laravel, organizada por **capas**:

```
HTTP (Controllers/Requests)
        │
   Service Layer  ← lógica de negocio, transacciones, orquestación de eventos
        │
  Repository Layer ← acceso a datos, queries, aplicación de scope de tenant
        │
   Eloquent Models ← entidades + Global Scope (school_id)
        │
     MySQL 8
```

- Los **controladores** son delgados: validan (Form Requests), delegan en servicios y
  devuelven vistas/respuestas.
- Los **servicios** contienen la lógica de negocio y coordinan repositorios, eventos y jobs.
- Los **repositorios** encapsulan el acceso a datos; nunca se hace query directo desde el
  controlador.
- Los **modelos** aplican el *Global Scope* de tenant y relaciones.

### 2.2 Multitenancy
- **Estrategia:** base de datos **compartida**, fila por escuela, discriminada por
  `school_id` (single database, shared schema).
- **Aislamiento:** un `TenantScope` (Global Scope de Eloquent) añade
  `WHERE school_id = :current` a toda consulta de modelos de dominio.
- **Resolución del tenant:** middleware `ResolveTenant` fija el `school_id` del usuario
  autenticado en un *contexto* de petición; los modelos lo leen al crear/consultar.
- **Super Admin:** opera sin scope (puede ver todas las escuelas) mediante un bypass
  explícito y auditado.

### 2.3 Eventos y asincronía
- Acción de dominio (p. ej. registrar asistencia) → emite **Evento**.
- **Listeners** despachan **Jobs** a la cola (Redis).
- Los Jobs envían notificaciones (Mail + WhatsApp) y escriben bitácora.
- Beneficio: la operación del usuario responde rápido; las notificaciones se procesan en
  segundo plano y son reintentables.

```
AsistenciaRegistrada (Event)
   ├─► NotificarTutoresListener ─► EnviarNotificacionAsistenciaJob (queue)
   │                                   ├─ MailMessage
   │                                   └─ WhatsAppChannel (Meta)
   └─► RegistrarBitacoraListener
```

---

## 3. Patrones utilizados

| Patrón | Uso en AULITA |
|--------|---------------|
| **Service Layer** | Toda lógica de negocio (`app/Services/*`). |
| **Repository** | Acceso a datos por entidad (`app/Repositories/*` + contratos). |
| **Global Scope (Multitenancy)** | `TenantScope` aplica `school_id` automáticamente. |
| **Observer** | Auditoría automática de modelos (created/updated/deleted). |
| **Event–Listener** | Desacople asistencia/reportes ↔ notificaciones/bitácora. |
| **Notification (multicanal)** | `mail` + canal personalizado `whatsapp`. |
| **Policy / Gate** | Autorización por rol y propiedad del recurso. |
| **Form Request** | Validación de entrada por caso de uso. |
| **DTO** | Transporte de datos entre capas en operaciones complejas. |
| **Strategy (exportación)** | PDF / Excel / CSV intercambiables. |

---

## 4. Estructura de carpetas

```
app/
├── Console/
│   └── Commands/                 # comandos del scheduler (faltas, alertas)
├── Domain/                       # opcional: agrupación por dominio
├── Events/                       # AsistenciaRegistrada, FaltaDetectada, ...
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                # super admin (escuelas, global)
│   │   ├── School/               # operación por escuela
│   │   └── Parent/               # portal de padres
│   ├── Middleware/
│   │   ├── ResolveTenant.php
│   │   └── EnsureSameSchool.php
│   └── Requests/                 # Form Requests por caso de uso
├── Jobs/                         # EnviarNotificacionAsistenciaJob, ...
├── Listeners/
├── Models/
│   ├── Concerns/
│   │   └── BelongsToSchool.php   # trait + TenantScope
│   └── *.php
├── Notifications/
│   ├── Channels/
│   │   └── WhatsAppChannel.php   # canal Meta Cloud API
│   ├── AsistenciaRegistradaNotification.php
│   ├── FaltaDetectadaNotification.php
│   └── AlertaRiesgoNotification.php
├── Policies/
├── Providers/
├── Repositories/
│   ├── Contracts/                # interfaces
│   └── Eloquent/                 # implementaciones
├── Scopes/
│   └── TenantScope.php
├── Services/
│   ├── Asistencia/
│   ├── Calificaciones/
│   ├── Notificaciones/
│   ├── Reportes/
│   └── Portal/
└── Support/                      # helpers, QR, firmas

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── views/
│   ├── layouts/
│   ├── components/
│   ├── admin/
│   ├── school/
│   └── parent/
├── js/                           # Alpine, Chart.js, lector QR
└── css/                          # Bootstrap 5.3

routes/
├── web.php
├── admin.php
├── parent.php
└── console.php

docker/
├── nginx/
├── php/
└── supervisor/
```

---

## 5. Seguridad

| Control | Implementación |
|---------|----------------|
| CSRF | Middleware de Laravel en todas las rutas web. |
| Autorización | Policies + Gates por rol y propiedad. |
| Aislamiento de tenant | `TenantScope` + middleware; `school_id` nunca del cliente. |
| Rate limiting | Throttle en login, activación de padres y escaneo QR. |
| Validación de correo | Flujo de verificación con enlace firmado (24 h). |
| Contraseñas | Política de complejidad (8+, mayús/minús/número/especial), hash bcrypt/argon. |
| QR seguro | Token firmado (HMAC) por alumno; verificación de escuela y vigencia. |
| Firma de enterado | Registro de fecha/hora/IP no repudiable. |
| Auditoría | Observer + bitácora de toda acción sensible. |
| Subida de archivos | Validación de tipo/tamaño; almacenamiento fuera de `public` con acceso controlado. |

---

## 6. Notificaciones WhatsApp (Meta Cloud API)

- Canal personalizado `WhatsAppChannel` (en `app/Notifications/Channels`).
- Envío vía **Graph API** (`/{phone_id}/messages`) con **plantillas aprobadas** (HSM),
  porque los mensajes los inicia el sistema.
- Configuración por entorno: `WHATSAPP_TOKEN`, `WHATSAPP_PHONE_ID`, versión de API.
- El número del tutor se almacena en `tutores.telefono` (formato E.164).
- Cada notificación implementa `toMail()` y `toWhatsApp()`; `via()` decide los canales.
- Todo se ejecuta vía Queue (Redis); reintentos con backoff y registro de fallos.

> El detalle del flujo está en [05-diagramas.md](05-diagramas.md#flujo-de-notificaciones).

---

## 7. Entornos y despliegue

- **Docker Compose** con servicios: `app` (PHP-FPM 8.4), `nginx`, `mysql`, `redis`,
  `queue` (worker bajo Supervisor), `scheduler`.
- **Supervisor** mantiene `queue:work` y el `schedule:work` vivos y reiniciables.
- **Nginx** sirve estáticos y hace proxy a PHP-FPM.
- Variables sensibles en `.env` (no versionado); `.env.example` documenta las claves.

---

## 8. Calidad

- **PHPUnit:** pruebas unitarias (servicios, reglas de negocio) y de feature
  (asistencia, activación de padres, aislamiento de tenant).
- **Laravel Pint:** estilo de código consistente (PSR-12).
- **Pruebas críticas obligatorias:** aislamiento `school_id`, unicidad de asistencia
  diaria, transición `falta_pendiente → retardo`, expiración de enlaces (24 h),
  generación de alertas de riesgo.
