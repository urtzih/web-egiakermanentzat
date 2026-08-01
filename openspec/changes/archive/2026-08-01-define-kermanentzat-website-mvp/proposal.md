## Why

Egia Kermanentzat necesita una presencia pública oficial, bilingüe y usable que preserve la memoria de Kerman, explique el caso con una voz responsable y facilite apoyo y contacto. El MVP ya está desplegado en `https://egiakermanentzat.eus/`, por lo que este cambio se reconcilia con la entrega real antes de archivarlo.

## What Changes

- Publicar el sitio WordPress en euskera en `/` y castellano bajo `/es/`, con equivalentes contextuales, páginas legales bilingües y metadatos de idioma.
- Ofrecer Inicio, Resumen del caso, Ayuda y aportaciones, Contacto y una sección `Berriak/Actualidad` preparada para futuras publicaciones; el marcador de actualidad queda fuera del sitemap hasta contener noticias reales.
- Presentar una narrativa digna y accesible que separe el relato institucional de las valoraciones expresadas por la familia y la asociación.
- Publicar la transferencia bancaria verificada como único método económico y el correo confirmado como canal de contacto, sin formularios ni pagos integrados.
- Aplicar privacidad por defecto, consentimiento previo para analítica opcional, cabeceras de seguridad, indexación de producción, SEO bilingüe y tarjetas sociales aprobadas.
- Gestionar el contenido público desde el repositorio mediante un tema WordPress y un seed idempotente. La plataforma editorial avanzada para personas no técnicas queda fuera de este MVP.
- Trasladar la gobernanza operativa pendiente a `harden-kermanentzat-production-operations` y los flujos `SRC/CLM`, derechos y aprobación editorial a `build-kermanentzat-editorial-platform`.

## Capabilities

### New Capabilities

- `memory-and-case-narrative`: Estructura pública, memoria de Kerman, resumen del caso y separación responsable de voces.
- `bilingual-publication`: Rutas equivalentes ES/EU, selector contextual, canonicals, `hreflang` y paridad de la superficie pública.
- `support-and-donations`: Transferencia bancaria informada y minimizada como único método económico del MVP.
- `contact-and-participation`: Contacto por correo sin formulario, finalidades claras y precauciones para documentación sensible.
- `trust-accessibility-and-discoverability`: Privacidad técnica, seguridad frontal, accesibilidad, rendimiento, SEO y compartición responsable.

### Modified Capabilities

No existen especificaciones principales previas.

## Impact

La entrega usa WordPress, el tema `kermanentzat-prototype`, el MU-plugin de protección, contenido bilingüe versionado y scripts de verificación. No introduce una API pública ni un modelo editorial avanzado. Los requisitos jurídicos, lingüísticos y operativos que requieren evidencia externa permanecen explícitamente fuera del cierre técnico del MVP.
