# Publicación de noticias del 16/09/2026

Fecha técnica: 2026-09-16.

La actualización de Berriak / Actualidad incorpora al proyecto el paquete que se
usó para publicar manualmente en producción las referencias aportadas en
`D:/Kerman/Berriak.odt`. El objetivo de este registro es que `main` conserve el
mismo estado editorial y que futuras instalaciones no vuelvan al listado
anterior.

## Alcance

- Se prepararon 35 tarjetas por idioma a partir de 39 referencias del documento
  original.
- La noticia de ORAIN ya existente se preserva íntegra y se añaden 34 piezas.
- Las parejas bilingües de EITB se muestran una vez por idioma.
- El duplicado exacto de elDiario.es no se publica dos veces.
- El enlace del homenaje de EITB se corrigió retirando una `a` final que
  devolvía 404.
- No se copiaron artículos completos, fotografías ni embeds externos.
- No se activó Sender ni se crearon campañas de correo.

## Archivos versionados

- `scripts/prepare-berriak-sources.py`: lee el ODT local restringido y genera la
  comprobación de fuentes en `output/berriak-20260916/source-check.json`.
- `scripts/berriak-20260916.tsv`: títulos y resúmenes editoriales ES/EU usados
  para construir el paquete.
- `scripts/build-berriak-updater.py`: genera `tools/kermanentzat-berriak-update/news.json`,
  una vista previa local y el ZIP temporal de instalación.
- `scripts/check-berriak-preview.cjs`: revisa las vistas resultantes con
  Playwright sin acceder a producción.
- `tools/kermanentzat-berriak-update/`: plugin temporal de WordPress para
  aplicar, respaldar y restaurar únicamente el listado HTML de `/berriak/` y
  `/es/actualidad/`.
- `tests/berriak-updater-integration.php`: prueba aislada del merge, backup,
  restauración, idempotencia y bloqueo ante cambios posteriores.

Los artefactos de salida, capturas y ZIP permanecen en `output/` y no se
versionan.

## Uso seguro del paquete temporal

1. Instalar y activar `kermanentzat-berriak-update.zip` en WordPress.
2. Revisar Herramientas > Actualizar noticias.
3. Descargar la copia de seguridad antes de escribir.
4. Aplicar noticias.
5. Comprobar las páginas públicas y que una segunda aplicación no duplique nada.
6. Desactivar y eliminar el plugin temporal.

El plugin se detiene si el listado público no coincide con la vista prevista, si
la previsualización queda obsoleta o si hubo una edición posterior a la copia.
La copia queda en la opción `kermanentzat_berriak_backup_20260916_v1` para poder
restaurar con el mismo ZIP.
