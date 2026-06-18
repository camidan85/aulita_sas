# 05 — Diagramas

Todos los diagramas en **Mermaid**.

---

## 1. Arquitectura de despliegue

```mermaid
flowchart TB
    subgraph Cliente
        M["📱 Profesor/Prefecto (PWA)"]
        P["📱 Padre (PWA)"]
        A["💻 Admin/Director (Web)"]
    end

    subgraph Infra["Docker Host"]
        NX["Nginx (reverse proxy)"]
        APP["PHP-FPM 8.4 — Laravel 12"]
        Q["Worker de colas (Supervisor)"]
        SCH["Scheduler (cron)"]
        RDS[("Redis — cache/colas")]
        DB[("MySQL 8")]
    end

    META["WhatsApp Cloud API (Meta)"]
    SMTP["Servidor SMTP / Correo"]

    M --> NX
    P --> NX
    A --> NX
    NX --> APP
    APP <--> DB
    APP <--> RDS
    APP -- "encola" --> RDS
    Q -- "consume" --> RDS
    Q --> DB
    Q -- "WhatsApp (plantilla)" --> META
    Q -- "correo" --> SMTP
    SCH --> APP
```

---

## 2. Arquitectura por capas

```mermaid
flowchart TB
    REQ["HTTP Request"] --> MW["Middleware: Auth + ResolveTenant + Throttle"]
    MW --> CTRL["Controller (delgado)"]
    CTRL --> FR["Form Request (validación)"]
    CTRL --> SVC["Service Layer (lógica de negocio)"]
    SVC --> REPO["Repository (acceso a datos)"]
    REPO --> MODEL["Eloquent + TenantScope"]
    MODEL --> DB[("MySQL")]
    SVC --> EVT["Event"]
    EVT --> LIS["Listener"]
    LIS --> JOB["Job (Redis Queue)"]
    JOB --> NOTIF["Notification: Mail + WhatsApp"]
    SVC --> OBS["Observer → Bitácora"]
```

---

## 3. Diagrama de casos de uso

```mermaid
flowchart LR
    subgraph Actores
        SA([Super Admin])
        DIR([Director])
        SUB([Subdirector])
        PRE([Prefecto])
        ADM([Administrativo])
        DOC([Docente])
        PAD([Padre])
    end

    SA --- UC0["Administrar escuelas (SaaS)"]
    DIR --- UC10["Consultar auditoría"]
    SUB --- UC6["Capturar calificaciones / boletas"]
    DOC --- UC1["Registrar asistencia QR"]
    DOC --- UC6
    DOC --- UC7["Levantar reporte de conducta"]
    PRE --- UC1
    PRE --- UC7
    ADM --- UC8["Emitir aviso segmentado"]
    PAD --- UC3["Activar cuenta"]
    PAD --- UC4["Consultar dashboard"]
    PAD --- UC5["Firmar de enterado"]
    PAD --- UC9["Solicitar cita"]

    SYS([Sistema/Scheduler]) --- UC2["Detección automática de faltas"]
    SYS --- UCR["Generar alertas de riesgo"]
```

---

## 4. Flujo de asistencia por QR

```mermaid
flowchart TD
    Start(["Profesor pulsa ESCANEAR QR"]) --> Read["Leer QR (id, matrícula, token)"]
    Read --> ValTok{"¿Token válido y firmado?"}
    ValTok -- No --> Rej["Rechazar + auditar intento"]
    ValTok -- Sí --> ValSchool{"¿Alumno de la misma escuela?"}
    ValSchool -- No --> Rej
    ValSchool -- Sí --> Dup{"¿Ya hay registro hoy?"}

    Dup -- "Sí (falta_pendiente)" --> Tarde{"¿Después de hora corte?"}
    Dup -- No --> Hora{"¿Después de hora corte?"}

    Hora -- No --> Pres["estatus = presente"]
    Hora -- Sí --> Ret["estatus = retardo"]
    Tarde -- Sí --> Upd["Actualizar falta_pendiente → retardo"]

    Pres --> Save["Guardar (fecha, hora, profesor, IP, dispositivo, origen=qr)"]
    Ret --> Save
    Upd --> Save

    Save --> Evt["Emitir AsistenciaRegistrada"]
    Evt --> Notif["Encolar notificaciones (correo + WhatsApp)"]
    Evt --> Bit["Registrar en bitácora"]
    Notif --> End(["Padre recibe aviso + dashboard en tiempo real"])
    Bit --> End
```

---

## 5. Flujo de notificaciones (correo + WhatsApp)

```mermaid
sequenceDiagram
    participant S as Service Asistencia
    participant E as Event Bus
    participant L as Listener
    participant Q as Cola (Redis)
    participant J as Job Notificación
    participant N as Notification
    participant MAIL as SMTP
    participant WA as Meta Cloud API
    participant T as Tutor

    S->>E: AsistenciaRegistrada
    E->>L: NotificarTutores
    L->>Q: dispatch(EnviarNotificacionJob)
    Note over Q,J: Procesado en segundo plano
    Q->>J: handle()
    J->>N: notify(tutores + administrativo)
    N->>N: via() = [mail, whatsapp]
    par Correo
        N->>MAIL: toMail() (asunto + cuerpo)
        MAIL-->>T: 📧 Correo de asistencia
    and WhatsApp
        N->>WA: toWhatsApp() (plantilla aprobada + variables)
        WA-->>T: 💬 WhatsApp de asistencia
    end
    Note over J: Reintentos con backoff; fallo de WA no afecta al correo
```

---

## 6. Flujo de faltas automáticas (Scheduler)

```mermaid
flowchart TD
    Cron(["Scheduler corre cada minuto"]) --> Check{"¿Es la hora de corte de alguna escuela?"}
    Check -- No --> Wait["Esperar"]
    Check -- Sí --> Loop["Por cada escuela en hora de corte"]
    Loop --> Alum["Obtener alumnos activos sin asistencia hoy"]
    Alum --> Mark["Crear asistencia estatus=falta_pendiente (origen=automatico)"]
    Mark --> Bit["Registrar en bitácora"]
    Bit --> Notif["Notificar tutor + administrativo (correo + WhatsApp)"]
    Notif --> Risk["Evaluar reglas de riesgo (3 consecutivas / 5 mes / 10 retardos)"]
    Risk --> Alert{"¿Se cumple una regla?"}
    Alert -- Sí --> Gen["Generar alerta_riesgo + notificar"]
    Alert -- No --> Done(["Fin del ciclo"])
    Gen --> Done
```

---

## 7. Flujo de activación del portal de padres

```mermaid
flowchart TD
    Start(["Padre abre 'Activar cuenta'"]) --> In["Captura CURP + apellido paterno"]
    In --> Match{"¿Coincide con un alumno?"}
    Match -- No --> Err["Mostrar error genérico (no revelar datos)"]
    Match -- Sí --> Data["Solicitar correo + teléfono"]
    Data --> Token["Generar token firmado (expira 24 h)"]
    Token --> Send["Enviar enlace de validación por correo"]
    Send --> Click{"¿Abre el enlace antes de 24 h?"}
    Click -- No --> Exp["Token expirado → reiniciar"]
    Click -- Sí --> Pass["Crear contraseña segura (8+, may/min/núm/especial)"]
    Pass --> Acc["Crear cuenta (user rol Padre) + vincular tutor"]
    Acc --> End(["Acceso al dashboard del padre"])
```
