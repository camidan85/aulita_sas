# Manual de usuario — AULITA

> Plataforma de control escolar (asistencia, conducta, calificaciones, avisos y portal de padres).
> Acceso: **https://aulita.desarrollodesistemasinformaticos.com**

Este manual explica, **paso a paso y por tipo de usuario**, cómo se usa el sistema. Si solo te interesa
"cómo se toma la asistencia", ve directo a la sección **2. El flujo de asistencia (lo más importante)**.

---

## Índice

1. [Conceptos básicos](#1-conceptos-básicos)
2. [El flujo de asistencia (lo más importante)](#2-el-flujo-de-asistencia-lo-más-importante)
3. [Entrar al sistema y recuperar contraseña](#3-entrar-al-sistema-y-recuperar-contraseña)
4. [Qué puede hacer cada usuario (resumen)](#4-qué-puede-hacer-cada-usuario-resumen)
5. [Guía por rol](#5-guía-por-rol)
   - [5.1 Super Admin](#51-super-admin)
   - [5.2 Director](#52-director)
   - [5.3 Subdirector](#53-subdirector)
   - [5.4 Prefecto](#54-prefecto)
   - [5.5 Administrativo](#55-administrativo)
   - [5.6 Docente](#56-docente)
   - [5.7 Padre / Tutor (portal)](#57-padre--tutor-portal)
6. [Correos automáticos: quién recibe qué](#6-correos-automáticos-quién-recibe-qué)
7. [Preguntas frecuentes](#7-preguntas-frecuentes)

---

## 1. Conceptos básicos

- **Cada escuela es independiente.** Los usuarios de una escuela solo ven los datos de su escuela.
- **Roles (tipos de usuario):** Super Admin, Director, Subdirector, Prefecto, Administrativo, Docente y Padre.
  Cada rol ve **solo los menús que le corresponden**.
- **Módulos:** Asistencia, Alertas, Calificaciones, Reportes (conducta), Avisos, Citas, Portal de padres y
  Auditoría. El Super Admin puede **encender o apagar módulos por escuela** (una escuela puede tener, por
  ejemplo, **solo Asistencia**).
- **Menú principal:** está arriba (barra blanca). Lo que ves ahí depende de tu rol y de los módulos activos.
- En el celular, el menú se abre con el **botón de las tres rayas** (☰) arriba a la derecha.

---

## 2. El flujo de asistencia (lo más importante)

La asistencia se toma **escaneando el código QR de cada alumno** en la entrada. Hay **3 momentos**:

### Paso A — Una sola vez: generar e imprimir el QR de cada alumno

Cada alumno tiene un **QR único**. Se genera solo cuando das de alta al alumno.

1. Menú **Alumnos**.
2. Entra al alumno → botón/enlace **QR** (o desde la lista, el ícono de QR).
3. Se abre la credencial con el QR → **Imprimir** (o guardarlo como imagen/PDF).
4. Ese QR se pega en la **credencial**, gafete o cuaderno del alumno.

> 💡 El **contenido** del QR (qué dato lleva: matrícula, CURP, etc.) lo define el Super Admin por escuela.
> Mientras no cambies ese formato, **el QR impreso sigue sirviendo todo el ciclo**.

### Paso B — Cada día en la entrada: escanear

Lo hace quien recibe a los alumnos (normalmente **Prefecto** o **Docente**), desde un **celular o tableta**.

1. Inicia sesión en el celular.
2. Menú **Escanear QR**.
3. La primera vez el navegador pedirá **permiso para usar la cámara** → acepta. (Debe ser **https**, que ya lo es).
4. Apunta la cámara al QR del alumno. **No hay que presionar nada**: en cuanto lo enfoca, registra.
5. Aparece un **recuadro grande de color** con el resultado y **el celular vibra**:

   | Color | Significado | ¿Qué hacer? |
   |-------|-------------|-------------|
   | 🟢 **Presente** | Llegó a tiempo | Pasa al siguiente |
   | 🟡 **Retardo** | Llegó después de la hora de corte | Pasa al siguiente |
   | 🔵 **Ya registrada** | Ese alumno **ya fue escaneado hoy** | Pasa al siguiente (no se duplica) |
   | 🔴 **Error** | QR no válido o de otra escuela | Revisa el QR / regístralo a mano después |

6. El recuadro **se quita solo en ~2.6 segundos** y queda listo para el siguiente alumno. La fila avanza rápido.

> ✅ **No se registra dos veces.** Si escaneas al mismo alumno otra vez, sale **"Ya registrada"** y **no se envía
> otro correo**. Por eso puedes escanear con confianza aunque la cámara "lea de más".

#### ¿Cómo decide si es Presente o Retardo?

Cada escuela tiene una **hora de corte** (por ejemplo, 7:15 am):

- Escaneo **antes o justo a la hora de corte** → **Presente**.
- Escaneo **después de la hora de corte** → **Retardo**.

### Paso C — Automático: faltas y avisos a los papás

No tienes que hacer nada; el sistema lo hace solo:

- **Al registrar** cada asistencia, se manda un **correo al tutor** del alumno ("Tu hijo registró asistencia: presente/retardo").
- **Pasada la hora de corte**, a quien **no fue escaneado** el sistema lo marca como **falta** y **avisa a los tutores**
  (y al administrativo) que el alumno **no llegó**.
- Si el alumno llega **tarde** y lo escaneas después, su registro cambia automáticamente de falta a **retardo**.

### Ver / revisar la asistencia del día

1. Menú **Asistencias**.
2. Arriba puedes **elegir la fecha** para ver otro día.
3. La tabla muestra: hora, alumno, grupo, **estatus** (Presente / Retardo / Falta / Justificada), origen y quién registró.

> Los estatus se ven como etiquetas de color: 🟢 Presente, 🟡 Retardo, 🔴 Falta, ⚪ Falta pendiente, 🔵 Justificada.

---

## 3. Entrar al sistema y recuperar contraseña

### Iniciar sesión
1. Entra a **https://aulita.desarrollodesistemasinformaticos.com**.
2. Botón **Iniciar sesión** (o **/login**).
3. Escribe tu **correo** y **contraseña** → **Entrar**.

### Olvidé mi contraseña
1. En la pantalla de inicio de sesión, clic en **"¿Olvidaste tu contraseña?"**.
2. Escribe tu correo → **Enviar enlace**.
3. Te llega un correo **"Restablecer tu contraseña"** → clic en el botón → escribe la nueva contraseña.

### Cambiar mi contraseña / mis datos
- Arriba a la derecha, clic en **tu nombre → Perfil**. Ahí cambias nombre, correo y contraseña.

---

## 4. Qué puede hacer cada usuario (resumen)

| Acción / Menú | Super Admin | Director | Subdirector | Prefecto | Administrativo | Docente | Padre |
|---|:--:|:--:|:--:|:--:|:--:|:--:|:--:|
| Crear escuelas y módulos | ✅ | — | — | — | — | — | — |
| Ver alumnos | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | — |
| Crear / editar alumnos | ✅ | ✅ | — | — | ✅ | — | — |
| **Escanear QR (tomar asistencia)** | ✅ | ✅ | — | ✅ | — | ✅ | — |
| Ver asistencias | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | — |
| Capturar calificaciones | ✅ | ✅ | ✅ | — | — | ✅ | — |
| Crear reportes de conducta | ✅ | ✅ | — | ✅ | — | ✅ | — |
| Crear avisos | ✅ | ✅ | ✅ | — | ✅ | ✅ | — |
| Gestionar citas | ✅ | ✅ | — | ✅ | ✅ | — | pide cita |
| Catálogos (grados, materias, docentes, tutores) | ✅ | ✅ | parcial | — | parcial | — | — |
| Auditoría (bitácora) | ✅ | ✅ | ✅ | — | ✅ | — | — |
| Portal de padres | — | — | — | — | — | — | ✅ |

> "parcial" = solo algunos catálogos. Lo importante: **cada quien ve en su menú únicamente lo que puede hacer.**

---

## 5. Guía por rol

### 5.1 Super Admin

Es el dueño de la plataforma. Administra **todas las escuelas**.

**Dar de alta una escuela**
1. Menú **Escuelas → Crear escuela**.
2. Llena nombre, datos y la **hora de corte de faltas**.
3. En **"Módulos visibles"** marca/desmarca los módulos que tendrá esa escuela
   (p. ej. dejar **solo Asistencia**).
4. En **formato de QR** define qué dato lleva el QR de los alumnos.
5. **Guardar**.

**Quitar o agregar módulos a una escuela existente**
1. Menú **Escuelas** → en la escuela, **Configurar/Editar**.
2. Sección **"Módulos visibles"** → marca/desmarca → **Guardar**.
   (Lo que desmarques desaparece del menú y del acceso de esa escuela.)

**Trabajar dentro de una escuela**
- Como Super Admin no estás "amarrado" a una escuela. Para operar dentro de una, en **Escuelas** usa
  **"Seleccionar"** en la escuela deseada. Arriba aparecerá una etiqueta amarilla **"Gestionando: (escuela)"**.
- Para salir de esa escuela, clic en **"salir"** junto a la etiqueta amarilla.

> Mientras tengas una escuela "seleccionada", puedes crear alumnos, tomar asistencia, etc., **como si fueras
> de esa escuela**.

---

### 5.2 Director

Ve y hace **casi todo dentro de su escuela** (no administra otras escuelas ni módulos).

- **Alumnos:** crear, editar, ver, generar QR.
- **Asistencia:** puede **escanear** y ver el historial.
- **Calificaciones, Reportes, Avisos, Citas, Auditoría, Catálogos:** acceso completo en su escuela.
- Es el rol indicado para **supervisar** que todo se esté capturando.

---

### 5.3 Subdirector

Apoyo académico. **No toma asistencia** (solo la consulta).

- **Ve** alumnos y asistencias.
- **Captura calificaciones**.
- Gestiona **docentes, grupos, materias, horarios**.
- **Crea avisos**, ve reportes y auditoría.

---

### 5.4 Prefecto

Rol de **operación diaria en la entrada**. Su trabajo principal es la asistencia y la conducta.

- **Escanear QR** (tomar asistencia) ← su función estrella.
- **Ver asistencias** del día.
- **Crear reportes de conducta** (con evidencias).
- **Gestionar citas** con padres.
- Ver alumnos y avisos.

**Su día típico:** entra al sistema en el celular → **Escanear QR** en la puerta → durante el día, si hay
incidentes, **crea reportes de conducta**.

---

### 5.5 Administrativo

Rol de **oficina / control escolar**. **No toma asistencia** (la consulta).

- **Alta y edición de alumnos** (incluida la **carga masiva por Excel**).
- Gestiona **tutores** y **grupos**.
- **Crea avisos** y gestiona **citas**.
- Ve asistencias, reportes y auditoría.

**Carga masiva de alumnos (Excel)**
1. Menú **Alumnos**.
2. Botón **Descargar plantilla** → llena el Excel con los alumnos.
3. Botón **Importar** → sube el Excel.
4. El sistema crea los alumnos y **genera su QR** automáticamente.

---

### 5.6 Docente

- **Escanear QR** (puede tomar asistencia de su grupo).
- **Capturar calificaciones**.
- **Crear reportes de conducta**.
- **Crear avisos**.
- Ver alumnos y asistencias.

**Capturar calificaciones**
1. Menú **Calificaciones → Capturar**.
2. Elige grupo/materia/periodo, escribe las calificaciones → **Guardar**.
3. Desde **Calificaciones** se pueden generar **boletas** y **kardex** (PDF) de cada alumno.

---

### 5.7 Padre / Tutor (portal)

Los papás tienen un **portal aparte** para seguir a su hijo.

**Activar la cuenta (primera vez)**
1. El papá entra a **/activar** (o el enlace que le comparte la escuela).
2. Escribe su **correo** (el mismo que la escuela tiene registrado) → **Enviar**.
3. Le llega un correo **"Activa tu cuenta del portal de padres"** → clic en el botón → crea su contraseña.
4. Ya puede entrar con su correo y contraseña.

**Dentro del portal (Mi portal)**
- Ve la **asistencia** de su hijo, **avisos**, **reportes** y **calificaciones**.
- Puede **firmar de enterado** avisos/reportes que lo requieran.
- Puede **solicitar una cita** (menú **Citas → Nueva**).

---

## 6. Correos automáticos: quién recibe qué

| Evento | Tutores (papás) | Administrativo |
|---|:--:|:--:|
| **Asistencia registrada** (cada escaneo) | ✅ | — |
| **Ausencia / falta** (no llegó) | ✅ | ✅ |
| **Alerta de riesgo** (faltas o conducta reiteradas) | ✅ | ✅ |
| **Aviso** dirigido | ✅ (los destinatarios) | — |
| **Reporte de conducta** | ✅ | — |
| **Activación del portal** | ✅ (el papá) | — |

> El administrativo **no** recibe un correo por cada asistencia (sería demasiado); solo se entera de
> **ausencias y alertas**, que es lo relevante.
> Todos los correos cierran con **"Saludos, Aulita - (nombre de la escuela)"**.

---

## 7. Preguntas frecuentes

**¿Tengo que presionar algo para registrar la asistencia?**
No. Con solo enfocar el QR con la cámara, se registra y aparece el recuadro de color + vibración.

**Escaneé dos veces al mismo alumno, ¿pasa algo?**
No. La segunda vez dice **"Ya registrada"** y **no** manda otro correo ni crea otro registro.

**La cámara no abre.**
Asegúrate de **dar permiso de cámara** al navegador y de estar en **https** (el candado). En el celular,
revisa que ninguna otra app esté usando la cámara.

**Un alumno olvidó su QR.**
Puedes registrarlo más tarde desde su credencial, o el sistema lo marcará como falta y, cuando lo escanees,
cambiará a retardo.

**¿Por qué un usuario no ve cierto menú?**
Porque su **rol** no tiene ese permiso, **o** porque ese **módulo está apagado** para la escuela (lo controla
el Super Admin en Escuelas → Módulos visibles).

**¿Cómo cambio el dato que lleva el QR?**
Lo hace el Super Admin en **Escuelas → (escuela) → formato de QR**. Ojo: si lo cambias, hay que
**reimprimir** los QR de los alumnos.

---

*AULITA — Manual de usuario. Para dudas o ajustes, contacta al administrador de la plataforma.*
