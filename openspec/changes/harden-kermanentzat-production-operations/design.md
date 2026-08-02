## Context

El sitio público funciona, pero `docs/LEGAL_INFORMATION_REQUIRED.md` y `docs/PRIVACY_AUDIT.md` registran evidencia externa todavía pendiente. El trabajo abarca asociación, asesoría, traducción, hosting, correo, Analytics, accesos y continuidad, sin depender del futuro modelo editorial.

## Goals / Non-Goals

**Goals:**

- Convertir pendientes externos en evidencias verificables con responsables y fechas.
- Reducir riesgo de pérdida de acceso, datos o continuidad.
- Validar legal y lingüísticamente textos y tratamientos activos.
- Completar pruebas manuales de accesibilidad y operación.

**Non-Goals:**

- Construir entidades `SRC/CLM` o flujos editoriales.
- Rediseñar el sitio o añadir métodos de pago.
- Publicar datos personales o documentos probatorios en Git.

## Decisions

### 1. Registro de evidencia restringido

El repositorio conservará estado, fecha, responsable funcional y referencia opaca; contratos, certificados, firmas, credenciales y datos personales permanecerán en almacenamiento restringido. La ausencia de evidencia mantendrá el requisito abierto.

### 2. Controles por proveedor

Hosting, Gmail, Analytics, dominio y banco tendrán fichas separadas con entidad contractual, ubicación, subencargados, retención, accesos, recuperación y baja. No se dará por válido un proveedor solo porque el servicio funcione.

### 3. Continuidad ensayada

Las copias deberán estar separadas del servicio principal. Se medirá una restauración real en un entorno aislado y se documentarán RPO/RTO observados, rollback y responsables.

### 4. Accesibilidad manual reproducible

La auditoría combinará automatización con teclado, lector de pantalla, zoom 200 %, móvil y reducción de movimiento, registrando ruta, navegador, tecnología de apoyo, resultado y defecto.

### 5. Activación condicionada de Analytics

La configuración técnica de consentimiento no sustituye la aprobación contractual. Si la evidencia exigida no está disponible, Analytics se desactivará hasta resolverla; Search Console y el sitemap pueden mantenerse sin analítica.

### 6. Alta de socios en dos pasos

La web solo invitará a expresar interés por correo, sin pedir DNI/NAN ni documentación en el primer mensaje. Antes de solicitar una ficha completa, la asociación deberá aprobar el órgano y las reglas de admisión, justificar cada dato, definir accesos y conservación y entregar la información de privacidad aplicable.

## Risks / Trade-offs

- [La evidencia depende de terceros] → Asignar responsable y mantener desactivada la función condicionada.
- [Una restauración puede afectar producción] → Ensayar solo con copias y destinos aislados.
- [Documentar accesos puede exponer datos] → Registrar roles y custodios de forma restringida, no credenciales.
- [Una revisión cambia textos públicos] → Aplicar cambios bilingües conjuntamente y conservar rollback.

## Migration Plan

1. Inventariar brechas, proveedores, cuentas y responsables.
2. Resolver primero Analytics, accesos privilegiados, copias y restauración.
3. Completar validaciones registrales, fiscales, jurídicas y lingüísticas.
4. Ejecutar auditorías manuales y corregir defectos.
5. Aprobar el runbook, el calendario de revisión y el relevo técnico.

## Open Questions

- Identidad de asesoría y revisores profesionales.
- RPO/RTO y retención aceptados por la asociación.
- Proveedor contractual, ubicación y retención definitiva de hosting y correo.
- Datos y canal definitivos para tramitar el alta conforme a los estatutos.
