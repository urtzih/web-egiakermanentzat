## ADDED Requirements

### Requirement: Cuentas y custodios controlados
La operación MUST usar cuentas individuales, MFA para privilegios, mínimo privilegio, al menos dos custodios autorizados para dominio y servicio, y un procedimiento de alta, baja y recuperación.

#### Scenario: Baja de una persona técnica
- **WHEN** termina una colaboración con acceso privilegiado
- **THEN** se revocan sus accesos, se conserva la auditoría y otro custodio puede recuperar y operar el servicio

#### Scenario: Cuenta privilegiada compartida
- **WHEN** varias personas utilizan una misma cuenta sin MFA
- **THEN** el control permanece abierto hasta migrar a identidades individuales, activar MFA y verificar recuperación con dos custodios

### Requirement: Inventario verificable de proveedores
Hosting, dominio, correo, banco y analítica MUST registrar entidad contractual, ubicación, subencargados, retención, accesos, soporte, exportación y baja.

#### Scenario: Renovación del hosting
- **WHEN** se revisa o renueva el proveedor
- **THEN** existe una ficha vigente con costes, residencia, soporte, copias y salida

#### Scenario: Correo provisionado sin buzones
- **WHEN** el proveedor incluye un servicio de correo que no tiene cuentas creadas ni uso operativo
- **THEN** se registra como contratado y no utilizado, sin atribuirle tratamientos activos ni eliminarlo sin una decisión de baja

### Requirement: Copias y restauración ensayadas
La operación MUST mantener copias separadas de archivos, base de datos y configuración, y MUST ensayar restauración en un destino aislado con RPO/RTO observados.

#### Scenario: Prueba de recuperación
- **WHEN** se ejecuta el ensayo programado
- **THEN** se restaura una versión utilizable, se registran tiempos, pérdidas y defectos y no se altera producción

### Requirement: Gestión de cambios e incidentes
La operación MUST definir responsables de actualizaciones, monitorización, logs, incidente, comunicación, rollback y conservación de evidencia mínima.

#### Scenario: Actualización defectuosa
- **WHEN** una actualización rompe una ruta pública crítica
- **THEN** el responsable puede detectar el fallo, restaurar una versión validada y registrar el incidente

### Requirement: Revisión periódica
Contenido, traducciones, accesos, proveedores, riesgos, costes y recuperación MUST revisarse según un calendario aprobado y con resultados trazables.

#### Scenario: Revisión anual
- **WHEN** llega la fecha programada
- **THEN** se auditan accesos, copias, seguridad, privacidad, accesibilidad, proveedores y mantenimiento y se asignan acciones
