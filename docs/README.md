# AULITA — Documentación del Proyecto

**Plataforma Integral de Gestión Escolar para Secundarias Privadas (SaaS)**

> Esta carpeta es la **fuente única de verdad** del proyecto. Según la metodología
> definida, **toda la documentación (FASE 1) debe completarse y aprobarse antes de
> generar una sola línea de código de la aplicación (FASE 2)**.

---

## Estado del proyecto

| | |
|---|---|
| Fase actual | **FASE 1 — Documentación** |
| Proyecto base | Nuevo Laravel 12 limpio (`aulita`) |
| Canales de notificación | Correo + WhatsApp (Meta Cloud API) |
| Multitenancy | Base compartida, filtrado obligatorio por `school_id` |

---

## Índice de documentos

| # | Documento | Contenido |
|---|-----------|-----------|
| 01 | [Documento Funcional](01-funcional.md) | Objetivos, alcance, reglas de negocio, casos de uso |
| 02 | [Documento Técnico](02-tecnico.md) | Arquitectura, patrones, estructura de carpetas, stack |
| 03 | [Diccionario de Datos](03-diccionario-datos.md) | Tablas, campos, tipos, restricciones, índices |
| 04 | [Modelo Entidad-Relación](04-modelo-er.md) | Diagrama ER completo (Mermaid) |
| 05 | [Diagramas](05-diagramas.md) | Arquitectura, casos de uso, flujo QR, flujo de notificaciones |
| 06 | [Roadmap](06-roadmap.md) | Fases de implementación y entregables |
| 07 | [Operación y Despliegue](07-operacion.md) | Puesta en marcha, Docker, scheduler, hardening, CI |

---

## Convenciones

- **Idioma de datos:** español (nombres de tablas y campos en español, en `snake_case`).
- **Multitenancy:** toda tabla del dominio incluye `school_id` y se filtra por un
  *Global Scope* automático. Ningún query cruza escuelas.
- **Identificadores:** `BIGINT UNSIGNED AUTO_INCREMENT` salvo indicación contraria.
- **Auditoría:** toda acción relevante se registra en `bitacora`.
- **Soft deletes:** las entidades de negocio principales usan borrado lógico
  (`deleted_at`) para preservar histórico y expediente.

---

## Cómo leer esta documentación

1. Empieza por el **Funcional (01)** para entender el *qué* y el *porqué*.
2. Continúa con el **Técnico (02)** para entender el *cómo*.
3. Usa **Diccionario (03)** + **ER (04)** como referencia del modelo de datos.
4. Los **Diagramas (05)** ilustran los flujos críticos (QR y notificaciones).
5. El **Roadmap (06)** define el orden de construcción en FASE 2.
