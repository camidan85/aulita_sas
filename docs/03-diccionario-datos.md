# 03 — Diccionario de Datos

> Motor: **MySQL 8 / InnoDB / utf8mb4**. Toda tabla de dominio incluye `school_id`
> (FK a `schools`) e índice sobre `school_id`. PK = `id BIGINT UNSIGNED AUTO_INCREMENT`.
> Marcas de tiempo `created_at` / `updated_at` salvo indicación. `deleted_at` cuando
> aplica *soft delete*.

## Convenciones de tipos

| Abreviatura | Tipo MySQL |
|-------------|-----------|
| PK | `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` |
| FK | `BIGINT UNSIGNED` |
| str(n) | `VARCHAR(n)` |
| text | `TEXT` |
| enum(...) | `ENUM(...)` |
| bool | `TINYINT(1)` |
| ts | `TIMESTAMP NULL` |
| date | `DATE` |
| time | `TIME` |
| dec(p,s) | `DECIMAL(p,s)` |
| json | `JSON` |

---

## 1. `schools` — Escuelas (tenant raíz)

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | Identificador |
| nombre | str(150) | NOT NULL | Nombre de la escuela |
| slug | str(150) | UNIQUE, NOT NULL | Identificador URL |
| cct | str(20) | NULL | Clave del centro de trabajo |
| logo | str(255) | NULL | Ruta del logo |
| direccion | str(255) | NULL | |
| telefono | str(20) | NULL | |
| correo | str(150) | NULL | |
| hora_corte_faltas | time | NOT NULL, default `07:15:00` | Hora de detección automática |
| timezone | str(40) | NOT NULL, default `America/Mexico_City` | |
| umbral_riesgo_calif | dec(4,2) | default `6.00` | Promedio mínimo aprobatorio |
| settings | json | NULL | Parámetros configurables |
| estatus | enum(`activa`,`suspendida`,`baja`) | default `activa` | |
| created_at / updated_at | ts | | |

**Índices:** UNIQUE(`slug`), INDEX(`estatus`).

---

## 1B. `ciclos_escolares` — Ciclo escolar (catálogo)

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| nombre | str(9) | NOT NULL | "2025-2026" |
| fecha_inicio | date | NOT NULL | |
| fecha_fin | date | NOT NULL | |
| vigente | bool | default 0 | Marca el ciclo activo de la escuela |
| timestamps | | | |

**Índices:** UNIQUE(`school_id`,`nombre`), INDEX(`school_id`,`vigente`).
**Nota:** sustituye el antiguo `VARCHAR ciclo_escolar` que estaba repetido en varias tablas;
ahora se referencia por `ciclo_id`.

---

## 2. `users` — Usuarios del sistema

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NULL | NULL solo para Super Admin |
| name | str(150) | NOT NULL | |
| email | str(150) | NOT NULL, **UNIQUE global** | Un correo existe en una sola escuela |
| email_verified_at | ts | NULL | |
| password | str(255) | NOT NULL | Hash |
| telefono | str(20) | NULL | E.164 |
| estatus | enum(`activo`,`inactivo`) | default `activo` | |
| last_login_at | ts | NULL | |
| remember_token | str(100) | NULL | |
| created_at / updated_at | ts | | |

**Índices:** **UNIQUE(`email`) global**, INDEX(`school_id`), FK `school_id`→`schools.id`.
**Nota (login SaaS):** el `email` es **único en toda la plataforma** → el login por correo
resuelve la escuela sin ambigüedad. El `school_id` del usuario fija su tenant tras autenticar.
**Nota:** los roles se gestionan con Spatie (tablas `roles`, `permissions`,
`model_has_roles`, `model_has_permissions`, `role_has_permissions`).

---

## 3. `docentes` — Perfil de docente

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| user_id | FK | UNIQUE, NOT NULL | Cuenta asociada |
| numero_empleado | str(30) | NULL | |
| nombre | str(100) | NOT NULL | |
| apellido_paterno | str(100) | NOT NULL | |
| apellido_materno | str(100) | NULL | |
| telefono | str(20) | NULL | |
| estatus | enum(`activo`,`inactivo`) | default `activo` | |
| timestamps | | | |

**Índices:** INDEX(`school_id`), UNIQUE(`user_id`), FKs a `schools`, `users`.

---

## 4. `grados`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| nombre | str(30) | NOT NULL | "1°","2°","3°" |
| nivel | tinyint | NOT NULL | 1,2,3 |
| timestamps | | | |

**Índices:** UNIQUE(`school_id`,`nivel`).

---

## 5. `grupos`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| grado_id | FK | NOT NULL | |
| nombre | str(10) | NOT NULL | "A","B" |
| ciclo_id | FK | NOT NULL | FK→`ciclos_escolares.id` |
| docente_titular_id | FK | NULL | Docente titular (FK→`docentes.id`; no confundir con `tutores`) |
| timestamps | | | |

**Índices:** UNIQUE(`school_id`,`grado_id`,`nombre`,`ciclo_id`), FKs.

---

## 6. `alumnos`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| grupo_id | FK | NULL | Grupo activo |
| matricula | str(30) | NOT NULL | |
| nombre | str(100) | NOT NULL | |
| apellido_paterno | str(100) | NOT NULL | |
| apellido_materno | str(100) | NULL | |
| curp | str(18) | NOT NULL | |
| fecha_nacimiento | date | NULL | |
| sexo | enum(`M`,`F`,`X`) | NULL | |
| correo | str(150) | NULL | |
| telefono | str(20) | NULL | |
| fotografia | str(255) | NULL | |
| estatus | enum(`activo`,`baja`,`egresado`,`suspendido`) | default `activo` | |
| deleted_at | ts | NULL | Soft delete |
| timestamps | | | |

**Índices:** UNIQUE(`school_id`,`matricula`), UNIQUE(`school_id`,`curp`),
INDEX(`school_id`,`grupo_id`), INDEX(`estatus`).

---

## 7. `tutores`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| user_id | FK | NULL | Cuenta del portal (si activada) |
| nombre | str(150) | NOT NULL | |
| correo | str(150) | NULL | |
| telefono | str(20) | NULL | E.164 para WhatsApp |
| parentesco | str(40) | NULL | Padre/Madre/Tutor |
| timestamps | | | |

**Índices:** INDEX(`school_id`), INDEX(`user_id`).

---

## 8. `alumno_tutor` — Pivot alumno↔tutor

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | NOT NULL | |
| tutor_id | FK | NOT NULL | |
| tipo | enum(`principal`,`secundario`) | NOT NULL | |
| timestamps | | | |

**Índices:** UNIQUE(`alumno_id`,`tutor_id`), UNIQUE(`alumno_id`,`tipo`) *(un principal y un secundario por alumno)*.

---

## 9. `qr_tokens` — Token de asistencia por alumno

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | NOT NULL | |
| token | str(128) | UNIQUE, NOT NULL | Token firmado (HMAC) |
| activo | bool | default 1 | Permite rotación |
| timestamps | | | |

**Índices:** UNIQUE(`token`), INDEX(`alumno_id`,`activo`).
**Cardinalidad:** relación **1:N** (un alumno acumula histórico de tokens) con la regla de
negocio de **un único token `activo = 1` por alumno** a la vez. Validar en la capa de servicio.

---

## 10. `asistencias`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | NOT NULL | |
| fecha | date | NOT NULL | |
| hora | time | NULL | Hora de registro |
| estatus | enum(`presente`,`retardo`,`falta`,`falta_pendiente`,`justificada`) | NOT NULL | |
| origen | enum(`qr`,`automatico`,`manual`) | NOT NULL | |
| registrado_por | FK | NULL | `users.id` de quien registra |
| ip | str(45) | NULL | IPv4/IPv6 |
| dispositivo | str(255) | NULL | User-Agent |
| observaciones | str(255) | NULL | |
| timestamps | | | |

**Índices:** UNIQUE(`alumno_id`,`fecha`) *(RN-AS01)*, INDEX(`school_id`,`fecha`),
INDEX(`estatus`).
**Nota de diseño:** `registrado_por` apunta a **`users.id`** (no a `docentes.id`) **a propósito**,
porque un **prefecto** —que no necesariamente es docente— también registra asistencia.
Mismo criterio en `reportes.profesor_id`. En cambio `asignaciones`/`horarios` sí usan
`docentes.id` porque solo un docente imparte materias.
**Nota sobre `justificada`:** el estatus existe en el enum, pero el **flujo de justificantes**
(captura de evidencia y aprobación) está como **decisión abierta #5** en el roadmap; no
implementar hasta confirmarla.

---

## 11. `periodos`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| nombre | enum(`trimestre_1`,`trimestre_2`,`trimestre_3`) | NOT NULL | |
| ciclo_id | FK | NOT NULL | FK→`ciclos_escolares.id` |
| fecha_inicio | date | NOT NULL | |
| fecha_fin | date | NOT NULL | |
| timestamps | | | |

**Índices:** UNIQUE(`school_id`,`nombre`,`ciclo_id`).

---

## 12. `materias`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| clave | str(20) | NULL | |
| nombre | str(100) | NOT NULL | |
| timestamps | | | |

**Índices:** UNIQUE(`school_id`,`nombre`).

---

## 13. `asignaciones` — Docente·Materia·Grupo

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| docente_id | FK | NOT NULL | |
| materia_id | FK | NOT NULL | |
| grupo_id | FK | NOT NULL | |
| ciclo_id | FK | NOT NULL | FK→`ciclos_escolares.id` |
| timestamps | | | |

**Índices:** UNIQUE(`docente_id`,`materia_id`,`grupo_id`,`ciclo_id`).

---

## 14. `horarios`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| grupo_id | FK | NOT NULL | |
| materia_id | FK | NOT NULL | |
| docente_id | FK | NOT NULL | |
| dia_semana | tinyint | NOT NULL | 1=Lun … 5=Vie |
| hora_inicio | time | NOT NULL | |
| hora_fin | time | NOT NULL | |
| timestamps | | | |

**Índices:** INDEX(`school_id`,`grupo_id`,`dia_semana`).

---

## 15. `calificaciones`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | NOT NULL | |
| materia_id | FK | NOT NULL | |
| periodo_id | FK | NOT NULL | |
| calificacion | dec(4,2) | NOT NULL | 0.00–10.00 |
| capturado_por | FK | NULL | `users.id` |
| timestamps | | | |

**Índices:** UNIQUE(`alumno_id`,`materia_id`,`periodo_id`), INDEX(`school_id`).

---

## 16. `reportes` — Conducta / felicitaciones / avisos individuales

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | NOT NULL | |
| profesor_id | FK | NOT NULL | `users.id` |
| tipo | enum(`mala_conducta`,`incidencia_academica`,`incidencia_disciplinaria`,`aviso`,`felicitacion`,`citatorio`) | NOT NULL | |
| descripcion | text | NOT NULL | |
| fecha | date | NOT NULL | |
| hora | time | NULL | |
| requiere_firma | bool | default 0 | |
| timestamps | | | |

**Índices:** INDEX(`school_id`,`alumno_id`), INDEX(`tipo`).

---

## 17. `evidencias` — Adjuntos de reportes

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| reporte_id | FK | NOT NULL | |
| tipo | enum(`imagen`,`pdf`,`documento`,`video`) | NOT NULL | |
| path | str(255) | NOT NULL | |
| nombre_original | str(255) | NULL | |
| mime | str(100) | NULL | |
| size | int unsigned | NULL | bytes |
| timestamps | | | |

**Índices:** INDEX(`reporte_id`).

---

## 18. `avisos`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| titulo | str(150) | NOT NULL | |
| contenido | text | NOT NULL | |
| alcance | enum(`escuela`,`grado`,`grupo`,`alumno`) | NOT NULL | |
| target_id | bigint | NULL | Referencia **lógica** (no FK real) a grado/grupo/alumno según `alcance`; `NULL` si alcance=`escuela` |
| requiere_firma | bool | default 0 | |
| publicado_por | FK | NOT NULL | `users.id` |
| fecha_publicacion | ts | NOT NULL | |
| timestamps | | | |

**Índices:** INDEX(`school_id`,`alcance`), INDEX(`fecha_publicacion`).

---

## 19. `aviso_adjuntos`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| aviso_id | FK | NOT NULL | |
| path | str(255) | NOT NULL | |
| mime | str(100) | NULL | |
| size | int unsigned | NULL | |
| timestamps | | | |

**Índices:** INDEX(`aviso_id`).

---

## 20. `firmas_enterado` — Firma polimórfica

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| firmable_type | str(120) | NOT NULL | Modelo (Reporte/Aviso) |
| firmable_id | FK | NOT NULL | id del firmable |
| user_id | FK | NOT NULL | Padre que firma |
| fecha | date | NOT NULL | |
| hora | time | NOT NULL | |
| ip | str(45) | NOT NULL | |
| timestamps | | | |

**Índices:** UNIQUE(`firmable_type`,`firmable_id`,`user_id`),
INDEX(`school_id`).

---

## 21. `alertas_riesgo`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | NOT NULL | |
| tipo | enum(`3_faltas_consecutivas`,`5_faltas_mes`,`10_retardos`) | NOT NULL | |
| detalle | str(255) | NULL | |
| atendida | bool | default 0 | |
| generada_en | ts | NOT NULL | |
| timestamps | | | |

**Índices:** INDEX(`school_id`,`alumno_id`), INDEX(`tipo`,`atendida`).

---

## 22. `expediente_medico`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | UNIQUE, NOT NULL | 1:1 con alumno |
| tipo_sangre | str(5) | NULL | |
| alergias | text | NULL | |
| medicamentos | text | NULL | |
| contacto_emergencia_nombre | str(150) | NULL | |
| contacto_emergencia_telefono | str(20) | NULL | |
| timestamps | | | |

**Índices:** UNIQUE(`alumno_id`).

---

## 23. `documentos` — Expediente documental del alumno

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | NOT NULL | |
| tipo | enum(`curp`,`acta`,`certificado_primaria`,`comprobante_domicilio`,`otro`) | NOT NULL | |
| path | str(255) | NOT NULL | |
| nombre_original | str(255) | NULL | |
| mime | str(100) | NULL | |
| size | int unsigned | NULL | |
| subido_por | FK | NULL | `users.id` |
| timestamps | | | |

**Índices:** INDEX(`school_id`,`alumno_id`), INDEX(`tipo`).

---

## 24. `citas`

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | NOT NULL | |
| solicitante_user_id | FK | NOT NULL | Padre |
| con_rol | enum(`docente`,`prefecto`,`director`) | NOT NULL | |
| con_user_id | FK | NULL | Asignado al confirmar |
| motivo | text | NOT NULL | |
| fecha_solicitada | date | NOT NULL | |
| hora_solicitada | time | NULL | |
| estatus | enum(`solicitada`,`confirmada`,`reprogramada`,`cancelada`,`atendida`) | default `solicitada` | |
| timestamps | | | |

**Índices:** INDEX(`school_id`,`estatus`), INDEX(`alumno_id`).

---

## 25. `account_activations` — Activación del portal de padres

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NOT NULL | |
| alumno_id | FK | NOT NULL | |
| curp | str(18) | NOT NULL | Verificado contra alumno |
| apellido_paterno | str(100) | NOT NULL | Verificado |
| correo | str(150) | NOT NULL | |
| telefono | str(20) | NULL | |
| token | str(128) | UNIQUE, NOT NULL | Enlace firmado |
| expires_at | ts | NOT NULL | +24 h |
| used_at | ts | NULL | |
| timestamps | | | |

**Índices:** UNIQUE(`token`), INDEX(`school_id`,`alumno_id`).

---

## 26. `bitacora` — Auditoría

| Campo | Tipo | Restricciones | Descripción |
|-------|------|---------------|-------------|
| id | PK | | |
| school_id | FK | NULL | NULL para acciones de Super Admin |
| user_id | FK | NULL | Autor |
| accion | str(80) | NOT NULL | login, logout, crear, actualizar, ... |
| modulo | str(80) | NULL | asistencias, reportes, ... |
| model_type | str(120) | NULL | Entidad afectada |
| model_id | FK | NULL | |
| descripcion | str(255) | NULL | |
| ip | str(45) | NULL | |
| user_agent | str(255) | NULL | Navegador |
| created_at | ts | NOT NULL | |

**Índices:** INDEX(`school_id`,`created_at`), INDEX(`user_id`), INDEX(`modulo`),
INDEX(`model_type`,`model_id`).

---

## 27. `notifications` — (Laravel, canal `database`)

Tabla estándar de Laravel para notificaciones en dashboard del padre
(`id UUID`, `type`, `notifiable_type`, `notifiable_id`, `data json`, `read_at`, timestamps).

---

## 28. Tablas de infraestructura (Laravel / Spatie)

`migrations`, `password_reset_tokens`, `sessions`, `jobs`, `job_batches`,
`failed_jobs`, `cache`, `cache_locks`, `roles`, `permissions`, `model_has_roles`,
`model_has_permissions`, `role_has_permissions`, `personal_access_tokens`.

---

## Reglas transversales de integridad

- **FKs** con `ON DELETE RESTRICT` en catálogos y `ON DELETE CASCADE` en dependientes
  débiles (evidencias→reportes, adjuntos→avisos).
- **utf8mb4_unicode_ci** en todas las tablas.
- **Índice compuesto que inicia por `school_id`** en toda tabla de dominio para
  acelerar el filtrado multitenant.
