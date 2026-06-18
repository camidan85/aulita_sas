# 01 — Documento Funcional

## 1. Objetivos

### Objetivo general
Desarrollar **AULITA**, una plataforma **SaaS multitenant** para la gestión integral
de secundarias privadas, que centralice el control escolar, académico, disciplinario y
de asistencias, con comunicación directa y automatizada con los padres de familia.

### Objetivos específicos
1. Registrar asistencia diaria mediante **escaneo de QR desde el celular** del docente/prefecto.
2. Notificar **en tiempo real** a los tutores el ingreso, retardo o falta de su hijo, por **correo y WhatsApp**.
3. Automatizar la detección de faltas (proceso programado por escuela, p. ej. 07:15 AM).
4. Generar **alertas de riesgo** por patrones de inasistencia o conducta.
5. Ofrecer un **portal de autogestión para padres** con activación segura por CURP.
6. Gestionar calificaciones por periodo y emitir **boletas y kardex en PDF**.
7. Proveer **dashboards ejecutivos** con indicadores y gráficas.
8. Garantizar **trazabilidad total** mediante auditoría y bitácora exportable.
9. Aislar completamente los datos de cada escuela (`school_id`), sin fuga entre tenants.

---

## 2. Alcance

### Dentro del alcance (MVP comercializable)
- Multitenancy con alta de escuelas (Super Admin).
- Gestión de usuarios y 7 roles con permisos (Spatie).
- CRUD de alumnos, tutores, docentes, grados, grupos, materias, horarios.
- Expediente digital con línea de tiempo y documentos adjuntos.
- Asistencia por QR + registro automático de faltas y retardos.
- Notificaciones correo + WhatsApp (Meta) y notificaciones en dashboard.
- Calificaciones, promedios, boletas PDF y kardex.
- Reportes de conducta con evidencias (imagen/PDF/video corto).
- Avisos segmentados (escuela/grado/grupo/alumno) con firma de enterado.
- Portal de padres (dashboard, firmas, citas, módulo médico).
- Auditoría/bitácora con exportación PDF/Excel/CSV.
- Diseño responsive y PWA para portales de padres y profesores.

### Fuera del alcance (fases posteriores)
- Cobranza / facturación electrónica.
- Integración con SEP / sistemas oficiales de control escolar.
- App nativa (se cubre con PWA).
- Mensajería bidireccional tipo chat con padres.
- Pasarela de pagos para colegiaturas.

---

## 3. Roles y permisos

| Rol | Ámbito | Capacidades clave |
|-----|--------|-------------------|
| **Super Admin** | Global (SaaS) | Alta de escuelas, configuración global, monitoreo, estadísticas. Sin acceso a datos operativos de aula salvo soporte. |
| **Director** | Su escuela | Control total de la escuela: todos los módulos. |
| **Subdirector** | Su escuela | Control académico (materias, calificaciones, horarios). |
| **Prefecto** | Su escuela | Asistencias y disciplina (reportes, alertas). |
| **Administrativo** | Su escuela | Gestión escolar (alumnos, tutores, documentos, avisos). |
| **Docente** | Sus grupos | Asistencia QR, calificaciones, avisos, reportes. |
| **Padre de Familia** | Sus hijos | Consulta y autogestión (dashboard, firmas, citas, médico). |

> El control de acceso se implementa con **Policies + Gates** y el filtrado por
> `school_id` se aplica **siempre**, incluso si un permiso lo concediera.

---

## 4. Reglas de negocio

### RN-Tenancy
- **RN-T01:** Todo registro de dominio pertenece a una `school_id`. Ningún usuario
  (excepto Super Admin) puede leer ni escribir datos de otra escuela.
- **RN-T02:** El `school_id` se deriva del usuario autenticado, **nunca** de un parámetro
  enviado por el cliente.
- **RN-T03:** El `email` de usuario es **único en toda la plataforma**; el login por correo
  resuelve la escuela del usuario sin ambigüedad.

### RN-Alumnos / Tutores
- **RN-A01:** La `matrícula` y el `CURP` son únicos **dentro de la misma escuela**.
- **RN-A02:** Un alumno puede tener **un tutor principal** y **un tutor secundario**.
- **RN-A03:** Un alumno pertenece a exactamente un grupo activo por ciclo escolar.

### RN-Asistencia
- **RN-AS01:** Solo puede existir **un registro de asistencia por alumno por día**
  (clave única `alumno_id + fecha`).
- **RN-AS02:** Estatus posibles: `presente`, `retardo`, `falta`, `falta_pendiente`,
  `justificada`.
- **RN-AS03:** Si el alumno escanea **antes** de la hora de corte → `presente`.
- **RN-AS04:** Si escanea **después** de la hora de corte → `retardo`, y se actualiza un
  registro previo `falta_pendiente` si existiera.
- **RN-AS05:** Cada registro guarda: fecha, hora, profesor, IP, dispositivo y origen
  (`qr | automatico | manual`).
- **RN-AS06:** El QR de un alumno contiene `id`, `matrícula` y un **token seguro** firmado;
  un token inválido o de otra escuela se rechaza.

### RN-Faltas automáticas
- **RN-FA01:** Cada escuela define su **hora de corte** (default `07:15`).
- **RN-FA02:** A la hora de corte, el Scheduler crea `falta_pendiente` para todo alumno
  activo sin registro del día.
- **RN-FA03:** Un registro tardío convierte `falta_pendiente` → `retardo` y notifica la
  actualización.

### RN-Alertas de riesgo
- **RN-R01:** **3 faltas consecutivas** → alerta.
- **RN-R02:** **5 faltas en un mes** calendario → alerta.
- **RN-R03:** **10 retardos** acumulados (ciclo) → alerta.
- **RN-R04:** Cada alerta se notifica a tutor + administrativo y queda registrada.

### RN-Notificaciones
- **RN-N01:** Al registrar asistencia se notifica a **tutor principal, tutor secundario
  y administrativo** por correo y WhatsApp.
- **RN-N02:** WhatsApp se envía mediante **plantillas aprobadas (Meta Cloud API)**, ya que
  el mensaje lo inicia el sistema.
- **RN-N03:** Toda notificación se encola (Queue/Redis); un fallo de WhatsApp **no bloquea**
  el correo ni la operación.
- **RN-N04:** El padre ve la notificación en tiempo real en su dashboard
  ("Su hijo ingresó a la escuela").

### RN-Calificaciones
- **RN-C01:** Periodos: Primer, Segundo y Tercer trimestre.
- **RN-C02:** Se calculan promedios por **materia**, **alumno** y **grupo**.
- **RN-C03:** Una materia con promedio por debajo del umbral configurable marca
  "materia en riesgo".

### RN-Firma de enterado
- **RN-F01:** Reportes, citatorios y avisos importantes requieren firma del padre.
- **RN-F02:** La firma guarda `fecha`, `hora` e `IP` del padre.

### RN-Portal de padres
- **RN-P01:** Activación con **CURP del alumno + apellido paterno**. Si coinciden, se
  solicitan correo y teléfono y se envía enlace de validación.
- **RN-P02:** El enlace de validación **expira a las 24 horas**.
- **RN-P03:** La contraseña exige: mínimo 8 caracteres, mayúscula, minúscula, número y
  carácter especial.

### RN-Auditoría
- **RN-AU01:** Se registra **toda** acción relevante (login, logout, asistencia, reportes,
  avisos, cambios) con usuario, fecha, hora, IP, navegador y acción.

---

## 5. Casos de uso

### CU-01 — Registrar asistencia por QR
**Actor:** Docente / Prefecto
**Precondición:** Sesión iniciada en celular; alumno con QR válido.
**Flujo principal:**
1. El actor pulsa **ESCANEAR QR**.
2. El sistema lee el QR y valida el token y la escuela.
3. Valida que el alumno pertenezca a la escuela del actor.
4. Determina estatus (`presente`/`retardo`) según la hora de corte.
5. Registra asistencia (fecha, hora, profesor, IP, dispositivo, origen `qr`).
6. Dispara evento `AsistenciaRegistrada`.
7. Encola notificaciones a tutores y administrativo (correo + WhatsApp).
8. Registra el movimiento en bitácora.
**Postcondición:** Asistencia guardada y notificaciones encoladas.
**Flujos alternos:** Token inválido / alumno de otra escuela → se rechaza y se audita el intento.

### CU-02 — Detección automática de faltas
**Actor:** Sistema (Scheduler)
**Flujo:** A la hora de corte de cada escuela → revisa alumnos activos → crea
`falta_pendiente` para los que no tienen registro → notifica a tutor y administrativo →
registra en bitácora.

### CU-03 — Activar cuenta de padre
**Actor:** Padre de familia
**Flujo:** Ingresa CURP + apellido paterno → si coincide, captura correo y teléfono →
recibe enlace de validación (24 h) → crea contraseña segura → accede a su dashboard.

### CU-04 — Consultar dashboard del padre
**Actor:** Padre
**Flujo:** Ve asistencia (asistencias/retardos/faltas), académico (promedio,
calificaciones, materias en riesgo), comunicados y conducta (reportes/felicitaciones).

### CU-05 — Firmar de enterado
**Actor:** Padre
**Flujo:** Recibe aviso/reporte/citatorio que requiere firma → confirma lectura → se guarda
fecha, hora e IP.

### CU-06 — Capturar calificaciones y emitir boleta
**Actor:** Docente / Subdirector
**Flujo:** Captura calificación por alumno/materia/periodo → el sistema recalcula promedios →
genera boleta PDF y kardex.

### CU-07 — Levantar reporte de conducta con evidencia
**Actor:** Docente / Prefecto
**Flujo:** Desde el celular selecciona alumno → tipo de reporte → descripción → adjunta
evidencia → guarda → notifica al tutor (con firma si aplica).

### CU-08 — Emitir aviso segmentado
**Actor:** Administrativo / Director
**Flujo:** Crea aviso → elige alcance (escuela/grado/grupo/alumno) → adjunta archivos →
publica → notifica a los destinatarios; exige firma si es importante.

### CU-09 — Solicitar cita
**Actor:** Padre
**Flujo:** Desde el portal solicita cita con docente/prefecto/director → indica motivo y
fecha → el destinatario confirma/reprograma.

### CU-10 — Consultar auditoría
**Actor:** Director / Administrativo
**Flujo:** Filtra movimientos por fecha/alumno/profesor/grupo/módulo → exporta a PDF/Excel/CSV.

### CU-11 — Administrar escuelas (SaaS)
**Actor:** Super Admin
**Flujo:** Da de alta escuelas, configura parámetros globales, monitorea uso y consulta
estadísticas agregadas.
