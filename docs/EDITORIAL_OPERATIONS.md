# Operación editorial y suscripciones

El uso cotidiano está explicado en el [manual editorial ilustrado](README-ADMIN-WORDPRESS.md). La validación técnica y los límites de aceptación actuales se registran en la [evidencia del despliegue editorial en staging](STAGING_EDITORIAL_ACCEPTANCE.md).

## Despliegue de la fase editorial

1. Ejecutar `staging.ps1 deploy -ExpectedSha <sha>` para desplegar tema, plugin y montajes sin aplicar todavía la migración.
2. Revisar el resultado estricto de `wp kermanentzat editorial migrate --dry-run --strict`.
3. Activar `kermanentzat-editorial`. El vínculo bilingüe nativo funciona sin Polylang; cualquier incorporación futura de Polylang o ACF debe evaluarse y probarse antes, porque los campos y relaciones esenciales ya están registrados por código.
4. Ejecutar `staging.ps1 migrate -ExpectedSha <sha>`; el comando crea y restaura una copia aislada de base de datos y `wp-content/uploads` antes de escribir.
5. Conservar el identificador de backup mostrado por el comando.
6. Comparar las URLs actuales, contenido, canonical, `hreflang`, metadatos, sitemaps, accesibilidad y logs.
7. Repetir el comando: debe informar que la versión ya está registrada y no modificar contenido.
8. Aplicar el mismo procedimiento en producción durante una ventana con backup verificado.

El rollback restaura la base de datos, los medios y la versión previa del código. No se debe utilizar el seed como mecanismo de restauración.

## Cuenta y activación de Sender

Sender solo se activa en un entorno cuando se cumplen todas estas condiciones. El 12/08/2026 la asociación confirmó y archivó fuera de Git la revisión de DPA, subencargados, transferencias y conservación/supresión; esta confirmación no equivale a una declaración de cumplimiento jurídico integral:

- cuenta institucional y dominio remitente verificado;
- revisión de condiciones, DPA, subencargados, transferencias y conservación/supresión;
- dirección física de la asociación configurada en el proveedor;
- SPF, DKIM y DMARC comprobados;
- un formulario bilingüe publicado, conectado al grupo `Suscriptores web` y con double opt-in;
- textos legales bilingües revisados;
- constante `KERMANENTZAT_SENDER_APPROVED=true`;
- secreto canónico `KERMANENTZAT_SENDER_API_TOKEN` disponible solo en servidor; `SENDER_API_TOK` se admite únicamente como compatibilidad transitoria;
- ID público de cuenta, ID SDK, grupo, URL alternativa y remitente configurados en Ajustes → Kermanentzat Editorial.

El hosting debe ejecutar WP-Cron cada cinco minutos. Desactivar `KERMANENTZAT_SENDER_APPROVED` detiene formularios y nuevos envíos sin borrar la lista externa.

Staging permanece conectado al grupo real `Suscriptores web`. Esto permite una prueba extremo a extremo, pero también implica riesgo de envíos accidentales a personas reales: no publiques contenido de prueba con el aviso activo. Producción conserva su aprobación separada y no se modifica desde staging.

## Importación consentida

El archivo de trabajo debe tener `email`, `consent_date`, `consent_source` y `consent_scope`; puede incluir `status`. Se aceptan CSV UTF-8 y XLSX sencillo con esos encabezados en la primera hoja.

```powershell
python scripts/prepare-sender-import.py contactos.xlsx `
  --suppressions bajas.csv `
  --output tmp/sender-import.csv `
  --report tmp/sender-import-report.json
```

El informe no contiene emails. El CSV de salida sí contiene datos personales: no debe versionarse, enviarse por canales no acordados ni conservarse después de comprobar la importación. Las bajas y reclamaciones prevalecen siempre. Una fila sin evidencia suficiente queda excluida.

## Envíos y fallos

- En una publicación nueva y con Sender configurado, la casilla “Enviar aviso al publicar” aparece marcada; desmárcala antes de publicar si no procede un aviso.
- Las publicaciones existentes sin metadato permanecen desmarcadas y no se activan retrospectivamente.
- Guardar, corregir o añadir una traducción después de un envío no genera otro correo.
- El correo bilingüe presenta euskera primero y castellano después.
- Una pareja traducida genera como máximo una campaña.
- Sender exige `{{unsubscribe_link}}` en campañas HTML; no debe retirarse del template.
- Tras tres fallos el estado queda en “Fallido”. Corregir configuración o conectividad antes de usar “Reintentar”.
- Rotar el token en Sender y en el gestor de secretos; nunca pegarlo en WordPress, Git, tickets o logs.
- Revisar capacidad al alcanzar el 80 % de 2.500 suscriptores o 15.000 envíos mensuales, o si Sender cambia sus condiciones.
