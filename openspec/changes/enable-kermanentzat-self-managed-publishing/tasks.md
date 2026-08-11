## 1. Sustitución y base técnica

- [x] 1.1 Retirar `build-kermanentzat-editorial-platform` como cambio sustituido y comprobar que solo permanece la nueva propuesta editorial activa
- [x] 1.2 Crear el plugin `kermanentzat-editorial` con bootstrap seguro, versión de esquema, feature flags y avisos de dependencias gratuitos
- [x] 1.3 Añadir una infraestructura de pruebas que cargue WordPress o stubs controlados y cubra activación, capacidades y ausencia de efectos en el frontal

## 2. Modelo editorial y administración

- [x] 2.1 Registrar `kerman_update`, sus tipos y metadatos comunes y específicos con sanitización, autorización y soporte REST controlado
- [x] 2.2 Registrar `kerman_timeline` y su orden cronológico determinista, fechas parciales, destacados y relaciones de fuentes
- [x] 2.3 Registrar `kerman_source` privado con identificadores `SRC-###`, atribución, URLs y comprobación
- [x] 2.4 Incorporar metadatos y validaciones de crédito, derechos y texto alternativo para adjuntos editoriales
- [x] 2.5 Configurar el rol editorial, la checklist sensible y la referencia no pública de aprobación externa
- [x] 2.6 Registrar campos administrativos versionados por código y mostrar únicamente opciones pertinentes por tipo

## 3. Experiencia pública autoadministrable

- [x] 3.1 Crear patrones Gutenberg con estructura protegida para portada, resumen y páginas estructurales sin depender de HTML monolítico
- [x] 3.2 Implementar archivos y plantillas de novedades, hemeroteca, actividades y cronología con filtros, paginación y estados temporales
- [x] 3.3 Implementar módulos dinámicos reutilizables para portada, resumen del caso, cronología y suscripción
- [x] 3.4 Integrar relaciones Polylang opcionales, fallback del selector y consultas limitadas al idioma actual
- [x] 3.5 Adaptar canonical, `hreflang`, metadatos sociales, datos estructurados y sitemaps a páginas y tipos dinámicos
- [x] 3.6 Añadir estilos de frontend/editor y validaciones accesibles para tarjetas, filtros, formularios y mensajes administrativos
- [x] 3.7 Restaurar en la cronología dinámica la banda, rejilla, jerarquía y medida de lectura del resumen original

## 4. Seed y migración

- [x] 4.1 Convertir el seed en bootstrap no destructivo que cree solo páginas ausentes y nunca actualice contenido editorial existente
- [x] 4.2 Implementar comando WP-CLI de migración con `--dry-run`, versión, precondiciones y ejecución idempotente
- [x] 4.3 Migrar el contenido de resumen y actualidad a bloques y entidades estructuradas conservando slugs, padres, SEO y la referencia ORAIN
- [x] 4.4 Documentar backup, ejecución, verificación y rollback y comprobar que una segunda ejecución no modifica contenido

## 5. Suscripciones y campañas

- [x] 5.1 Implementar configuración segura de Sender, comprobación de disponibilidad y estados desactivados sin token o aprobación
- [x] 5.2 Crear módulo de suscripción EU/ES con carga del proveedor tras interacción, enlace alternativo, consentimiento y double opt-in documentado
- [x] 5.3 Añadir la solicitud de aviso desmarcada por defecto y la máquina de estados sin almacenar direcciones
- [x] 5.4 Implementar cola idempotente por grupo de traducción, cron, límite de tres reintentos y avisos administrativos
- [x] 5.5 Implementar cliente de campañas ordinarias de Sender, plantilla accesible EU/ES, envío al grupo y persistencia segura del resultado
- [x] 5.6 Implementar una herramienta de simulación/importación de Excel o CSV que valide evidencia, duplicados y supresiones sin versionar datos personales
- [x] 5.7 Añadir monitor preventivo de capacidad y runbook para límites, fallos, rotación del token, DNS y apagado del servicio

## 6. Privacidad, pruebas y entrega

- [x] 6.1 Actualizar registro de servicios, inventario, auditoría y textos legales bilingües con el tratamiento real de suscripciones y sus validaciones pendientes
- [ ] 6.2 Probar alta, confirmación, baja, importación, campaña única, traducciones, publicaciones programadas, reintentos y ausencia de secretos o emails en logs
- [ ] 6.3 Ejecutar pruebas PHP, OpenSpec, privacidad, accesibilidad, SEO, sitemaps, metadatos sociales y regresión de producción
- [ ] 6.4 Documentar e implementar el despliegue por fases en staging y producción, dejando explícitas las acciones externas de cuenta, DNS y revisión humana
- [ ] 6.5 Crear el manual administrativo ilustrado con capturas sanitizadas, diagramas Mermaid y prueba guiada de una persona no técnica
- [ ] 6.6 Realizar la aceptación editorial y operativa; sincronizar y archivar el cambio únicamente cuando ambas fases estén desplegadas y verificadas
