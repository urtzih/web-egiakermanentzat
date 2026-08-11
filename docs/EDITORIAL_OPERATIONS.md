# Operación editorial y suscripciones

## Despliegue de la fase editorial

1. Crear una copia restaurable de base de datos y `wp-content/uploads`.
2. Desplegar tema, plugin y montajes sin ejecutar todavía la migración.
3. Activar `kermanentzat-editorial` y Polylang Free; ACF Free puede utilizarse para extender formularios, pero los campos esenciales permanecen registrados por código.
4. Ejecutar `wp kermanentzat editorial migrate --dry-run` y revisar cada operación.
5. Migrar primero en staging con `wp kermanentzat editorial migrate`.
6. Comparar las URLs actuales, contenido, canonical, `hreflang`, metadatos, sitemaps, accesibilidad y logs.
7. Repetir el comando: debe informar que la versión ya está registrada y no modificar contenido.
8. Aplicar el mismo procedimiento en producción durante una ventana con backup verificado.

El rollback restaura la base de datos, los medios y la versión previa del código. No se debe utilizar el seed como mecanismo de restauración.

## Cuenta y activación de Sender

Sender permanece desactivado salvo que se cumplan todas estas condiciones:

- cuenta institucional y dominio remitente verificado;
- revisión de condiciones, DPA, transferencias y conservación;
- dirección física de la asociación configurada en el proveedor;
- SPF, DKIM y DMARC comprobados;
- un formulario bilingüe publicado, conectado al grupo `Suscriptores web` y con double opt-in;
- textos legales bilingües revisados;
- constante `KERMANENTZAT_SENDER_APPROVED=true`;
- secreto `SENDER_API_TOK` disponible solo en servidor y mapeado internamente a `KERMANENTZAT_SENDER_API_TOKEN`;
- ID público de cuenta, ID SDK, grupo, URL alternativa y remitente configurados en Ajustes → Kermanentzat Editorial.

El hosting debe ejecutar WP-Cron cada cinco minutos. Desactivar `KERMANENTZAT_SENDER_APPROVED` detiene formularios y nuevos envíos sin borrar la lista externa.

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

- La casilla “Enviar aviso al publicar” está desmarcada por defecto.
- Una pareja traducida genera como máximo una campaña.
- Sender exige `{{unsubscribe_link}}` en campañas HTML; no debe retirarse del template.
- Tras tres fallos el estado queda en “Fallido”. Corregir configuración o conectividad antes de usar “Reintentar”.
- Rotar el token en Sender y en el gestor de secretos; nunca pegarlo en WordPress, Git, tickets o logs.
- Revisar capacidad al alcanzar el 80 % de 2.500 suscriptores o 15.000 envíos mensuales, o si Sender cambia sus condiciones.
