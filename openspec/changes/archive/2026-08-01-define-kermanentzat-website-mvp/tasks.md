## 1. Reconciliación del MVP entregado

- [x] 1.1 Verificar que producción responde en las rutas principales ES/EU y no publica `noindex`
- [x] 1.2 Contrastar la superficie pública con el tema, el seed, los sitemaps y la documentación de privacidad
- [x] 1.3 Ajustar propuesta y diseño al WordPress desplegado y al contenido gestionado desde el repositorio
- [x] 1.4 Limitar las especificaciones del MVP a capacidades públicas demostrables

## 2. Disposición del backlog original

- [x] 2.1 Clasificar cada grupo de tareas original como entregado, sustituido o trasladado sin convertir pendientes en completados
- [x] 2.2 Trasladar controles jurídicos, lingüísticos, de acceso, recuperación y operación a `harden-kermanentzat-production-operations`
- [x] 2.3 Trasladar `SRC/CLM`, flujos, roles y biblioteca multimedia a `build-kermanentzat-editorial-platform`

| Tareas originales | Disposición | Destino o evidencia |
|---|---|---|
| 1.1–1.20, 5.1, 5.6, 6.3–6.4 y 7.2 | Entregadas en el MVP | Tema, seed, QA, metadatos, sitemaps y producción |
| 5.2 | Sustituida | La entrega incorpora además `Berriak/Actualidad`, excluida temporalmente del sitemap |
| 3.2 | Dividida | WordPress/Gutenberg está entregado; `SRC/CLM` pasa al cambio editorial |
| 6.1 | Dividida | El correo sin formulario está entregado; MFA, recuperación y responsables pasan a operaciones |
| 7.1 y 7.3 | Divididas | Identidad, contacto y consentimiento técnico están entregados; prensa y evidencia contractual pasan a sus cambios sucesores |
| 2.1, 2.3, 2.5–2.6, 3.1, 3.3–3.5, 5.4–5.5, 6.2, 6.5, 7.4–7.7 y 8.1–8.4 | Trasladadas | `harden-kermanentzat-production-operations` |
| 2.2, 2.4, 2.7, 4.1–4.6 y 5.3 | Trasladadas | `build-kermanentzat-editorial-platform` |

## 3. Verificación y cierre

- [x] 3.1 Crear cambios sucesores con ámbitos no solapados y trazabilidad hacia las tareas originales
- [x] 3.2 Verificar metadatos sociales, rutas bilingües, canonicals, `hreflang`, sitemap, robots y cabeceras en producción
- [x] 3.3 Verificar que la prueba de privacidad admite explícitamente producción con consentimiento analítico activo
- [x] 3.4 Sincronizar las cinco capacidades entregadas con `openspec/specs/`
- [x] 3.5 Validar los tres cambios y dejar este cambio sin tareas abiertas, listo para archivar
