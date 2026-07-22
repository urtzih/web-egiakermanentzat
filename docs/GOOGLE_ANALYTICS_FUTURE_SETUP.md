# Configuración futura de Google Analytics

Estado actual: **no instalado y no autorizado**. El repositorio no contiene identificadores de medición, etiquetas, solicitudes, dependencias ni código de Google.

## Condiciones previas

Analytics solo podrá evaluarse si existe una finalidad aprobada, análisis jurídico, proveedor/transferencias/retención documentados y una alternativa proporcional. Antes de implementarlo se actualizarán el inventario, las políticas bilingües, la versión del registro y las pruebas.

## Diseño desacoplado futuro

1. Mantener el servicio desactivado por defecto.
2. Leer el identificador exclusivamente desde una variable de entorno futura llamada `KERMANENTZAT_GA_MEASUREMENT_ID`; nunca incluir valores reales en Git.
3. Registrar un adaptador `analytics` mediante `kermanentzat_optional_services` con `enabled` controlado por configuración de producción.
4. Renderizar controles accesibles de aceptar, rechazar y cambiar preferencias solo cuando exista ese servicio.
5. No descargar ni ejecutar ninguna biblioteca de Google hasta obtener consentimiento analítico afirmativo.
6. Activar Consent Mode v2 únicamente junto con Analytics, con estado denegado antes de la elección y actualización tras una acción inequívoca. La configuración no sustituye el consentimiento requerido.
7. Ofrecer retirada tan sencilla como la aceptación y detener solicitudes futuras al retirar.

Referencia técnica oficial: https://developers.google.com/tag-platform/security/guides/consent

## Pruebas obligatorias antes de activar

- Cero solicitudes, cookies o identificadores antes de aceptar y tras rechazar.
- Solicitudes limitadas al servicio inventariado después de aceptar analítica.
- Categorías independientes; marketing no se activa por aceptar analítica.
- Persistencia y retirada con versión, fecha y elección mínima, sin identificadores innecesarios.
- Funcionamiento con JavaScript desactivado, teclado, lector de pantalla, zoom y ambos idiomas.
- CSP, política, inventario y registro sincronizados; auditoría de red en móvil y escritorio.

