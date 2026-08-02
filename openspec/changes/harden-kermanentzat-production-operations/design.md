## Context

El sitio público funciona, pero `docs/LEGAL_INFORMATION_REQUIRED.md` y `docs/PRIVACY_AUDIT.md` registran evidencia externa todavía pendiente. Ya se ha acreditado parte de la operación de OVH, Search Console y Analytics. La asociación ha decidido aplazar por ahora la revisión jurídica, fiscal y lingüística profesional y no crear buzones del dominio, sin que ello permita declarar cumplimiento integral ni cerrar las brechas de acceso y continuidad.

## Goals / Non-Goals

**Goals:**

- Convertir pendientes externos en evidencias verificables con responsables y fechas.
- Reducir riesgo de pérdida de acceso, datos o continuidad.
- Mantener identificadas las cautelas jurídicas, fiscales y lingüísticas mientras la revisión profesional permanezca aplazada.
- Completar pruebas manuales de accesibilidad y operación.

**Non-Goals:**

- Construir entidades `SRC/CLM` o flujos editoriales.
- Rediseñar el sitio o añadir métodos de pago.
- Publicar datos personales o documentos probatorios en Git.
- Crear por ahora buzones bajo `egiakermanentzat.eus` o migrar el canal público desde Gmail.
- Declarar como realizada una revisión jurídica, fiscal o lingüística profesional que no se ha contratado.

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

### 7. Estado operativo acreditado de OVH

La evidencia restringida identifica a OVH Hispano S.L. como proveedor contractual del dominio, DNS, hosting compartido Startup y MX Plan. El hosting está activo en `eu-west-gra` (Gravelines, Francia), clúster `cluster131`, con 100 GB y renovación automática prevista para julio de 2028. MX Plan está activo con capacidad para diez cuentas, pero no existe ningún buzón y la asociación ha decidido no crearlos por ahora; Gmail sigue siendo el canal público. El panel muestra una redirección de correo activa cuyo origen y destino todavía deben identificarse.

OVH muestra once copias disponibles de la base de datos. El 2 de agosto de 2026 se descargó y validó estructuralmente un volcado MySQL comprimido y se exportaron por SFTP los 4.074 archivos del alojamiento, incluida la configuración de WordPress, con coincidencia final de rutas y tamaños respecto del servidor. Esto acredita la exportación manual, pero no una política de retención ni una restauración ensayada.

La información operativa más reciente corrige que la cuenta principal de OVH no se comparte: solo Urtzi declara controlarla. Se ha creado el usuario local `Urtzi` y se decide mantenerlo por ahora con el grupo `DEFAULT`, sin adoptar la política IAM amplia que permitía todas las acciones. La posible eliminación del usuario local no está decidida. La cuenta principal continúa siendo una identidad raíz y todavía falta acreditar MFA, segundo custodio y recuperación antes de cerrar el control de accesos.

### 8. Estado operativo acreditado de Google

Search Console tiene la propiedad de dominio verificada, dos custodios autorizados y asociación con Google Analytics. La cuenta institucional y la cuenta del webmaster tienen 2FA confirmado. GA4 utiliza el flujo `G-46TRV43CBQ`, recibe datos, mantiene la medición mejorada y Google Signals desactivados y cuenta con dos administradores.

La conservación de eventos está en dos meses, pero la conservación de datos de usuario continúa en catorce meses. Falta acreditar las restantes opciones publicitarias, las condiciones contractuales y de transferencia y la recepción de `copy_iban` y `copy_bank_details` sin parámetros sensibles.

### 9. Revisión jurídica y fiscal aplazada

La asociación no contratará por ahora revisión jurídica, fiscal o lingüística profesional. Los textos seguirán identificados como borradores técnicos, no se prometerán deducciones ni certificados fiscales y no se declarará cumplimiento integral. Esta decisión no resuelve devoluciones, justificantes, conciliación, transparencia de aportaciones ni el procedimiento completo de alta de socios.

### 10. Copia externa y restauración aplazadas

La asociación decide descartar por ahora el almacenamiento permanente de una copia separada y el ensayo de restauración aislada. Los archivos descargados y el volcado SQL acreditan que la exportación manual es posible, pero no se consideran un sistema de copias aprobado ni permiten declarar RPO/RTO o continuidad ensayada. El requisito permanece abierto y el riesgo residual queda registrado explícitamente.

## Risks / Trade-offs

- [La evidencia depende de terceros] → Asignar responsable y mantener desactivada la función condicionada.
- [Una restauración puede afectar producción] → Ensayar solo con copias y destinos aislados.
- [Documentar accesos puede exponer datos] → Registrar roles y custodios de forma restringida, no credenciales.
- [Una sola persona controla la cuenta principal de OVH] → Mantener un usuario local para la operación diaria, activar MFA y designar un segundo custodio antes de cerrar el control de accesos.
- [Las copias nativas de OVH son insuficientes o no contractuales] → Exportar base de datos, archivos y configuración a almacenamiento separado y ensayar restauración.
- [La asociación aplaza la copia separada y la restauración] → Mantener el control abierto y no declarar RPO/RTO ni continuidad probada.
- [Una revisión cambia textos públicos] → Aplicar cambios bilingües conjuntamente y conservar rollback.

## Migration Plan

1. Inventariar brechas, proveedores, cuentas y responsables.
2. Resolver primero la cuenta compartida de OVH, Analytics, accesos privilegiados, copias y restauración.
3. Completar la validación registral y mantener documentado el aplazamiento jurídico, fiscal y lingüístico.
4. Ejecutar auditorías manuales y corregir defectos.
5. Aprobar el runbook, el calendario de revisión y el relevo técnico.

## Open Questions

- Resolución o certificado registral y aclaración del posible valor anómalo del artículo 16 de los estatutos.
- RPO/RTO, presupuesto, retención y almacenamiento externo aceptados por la asociación.
- MFA de la cuenta principal y del usuario local de OVH, segundo custodio y procedimiento de recuperación.
- Retención exacta de logs y copias de OVH, almacenamiento externo y resultado de una restauración aislada.
- Origen, destino, finalidad y custodios de la redirección activa de MX Plan.
- Inventario de Gmail y banco, incluidos accesos, recuperación, retención, exportación y baja.
- Ajuste de GA4 a dos meses también para datos de usuario, opciones publicitarias, condiciones de tratamiento y prueba de eventos.
- Datos y canal definitivos para tramitar el alta conforme a los estatutos.
