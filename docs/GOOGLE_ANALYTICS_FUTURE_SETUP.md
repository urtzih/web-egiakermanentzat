# Alta y activación de Google Analytics y Search Console

El código está preparado, pero Analytics permanece desactivado hasta completar esta lista.

## 1. Propiedad institucional de GA4

1. Entrar en https://analytics.google.com/ con la cuenta institucional.
2. Crear la cuenta `Egia Kermanentzat`, desactivando la compartición opcional no necesaria.
3. Crear la propiedad `Egia Kermanentzat — Web`, zona horaria España y moneda EUR.
4. Aceptar las condiciones y la adenda de tratamiento.
5. Crear el flujo web `Web principal` para `https://egiakermanentzat.eus`.
6. Desactivar medición mejorada, Google Signals, personalización publicitaria y recogida granular de ubicación/dispositivo.
7. No configurar User-ID, Google Ads ni Google Tag Manager.
8. Fijar la conservación de datos de usuario y eventos en 2 meses.
9. Añadir una segunda persona administradora, 2FA y recuperación institucional.
10. Copiar el ID `G-…`, sin pegar el snippet en WordPress.

## 2. Revisión previa y variables de producción

Archivar evidencia de:

- titularidad y accesos de la cuenta;
- adenda de tratamiento y condiciones aceptadas;
- garantías aplicables a transferencias;
- retención de dos meses y publicidad desactivada;
- revisión jurídica y lingüística de los textos.

Después configurar en el hosting:

```text
KERMANENTZAT_GA_MEASUREMENT_ID=G-XXXXXXXXXX
KERMANENTZAT_GA_APPROVED=true
```

El ID no es una credencial, pero se mantiene fuera de Git para desacoplar entornos. Sin la segunda variable el servicio no se registra.

## 3. Verificación tras desplegar

1. Abrir una ventana limpia y comprobar que no hay solicitudes a Google antes de elegir.
2. Rechazar y confirmar que no aparecen `_ga` ni solicitudes externas.
3. Aceptar y verificar en GA4 Tiempo real la página actual.
4. Copiar el IBAN y todos los datos; comprobar `copy_iban` y `copy_bank_details` en DebugView y que no contienen valores.
5. Retirar desde el pie y confirmar limpieza de cookies y ausencia de solicitudes tras la recarga.

## 4. Search Console

1. Crear una propiedad de dominio `egiakermanentzat.eus`.
2. Añadir en DNS el TXT facilitado por Google y conservarlo.
3. Verificar y añadir una segunda persona propietaria.
4. Enviar `https://egiakermanentzat.eus/sitemap.xml`.
5. Confirmar los dos hijos y 14 URLs.
6. Inspeccionar `/`, `/es/`, `/lagundu-eta-ekarpenak/` y `/es/ayuda-y-donaciones/`.
7. Revisar indexación, HTTPS, resultados enriquecidos y Core Web Vitals.

## 5. Vinculación

En GA4: Administración → Enlaces de productos → Search Console. Seleccionar la propiedad de dominio y el flujo `Web principal`; después publicar la colección de informes desde la Biblioteca.

Referencias oficiales:

- https://support.google.com/analytics/answer/9304153
- https://developers.google.com/tag-platform/security/guides/consent
- https://support.google.com/webmasters/answer/34592
- https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap
- https://support.google.com/analytics/answer/10737381
