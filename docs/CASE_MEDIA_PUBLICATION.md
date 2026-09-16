# Publicacion de videos e informe del resumen del caso

Fecha tecnica: 2026-09-16.

La persona responsable del proyecto solicito expresamente publicar ocho videos
aportados en `D:/Kerman` y dejar descargable el PDF
`InformeOrgCriminalSep.pdf` en las paginas del resumen del caso:

- `/kasuaren-laburpena/`
- `/es/resumen-del-caso/`

Esta autorizacion concreta no cambia la regla general del proyecto: los
documentos restringidos, CCTV, certificados, firmas, identificadores personales
y originales no aprobados siguen fuera de Git y fuera del sitio publico.

## Correspondencia editorial

| Archivo original | Archivo publico | Bloque | Tratamiento |
|---|---|---|---|
| `01 Apustua.mp4` | `01-apustua.mp4` | Las preguntas de la familia / Familiaren galderak | Hipotesis de posible apuesta atribuida a la familia. |
| `02 Doloa.mp4` | `02-doloa.mp4` | Lo que recogio la instruccion / Instrukzioan jasotakoa | Declaracion familiar sobre capacidad lesiva e intencionalidad. |
| `03 Kontakizuna.mp4` | `03-kontakizuna.mp4` | El cambio en el recorrido judicial / Ibilbide judizialaren aldaketa | Declaracion familiar sobre el peso del relato del agresor. |
| `04 ProbintziaAuzitegia.mp4` | `04-probintzia-auzitegia.mp4` | El cambio en el recorrido judicial / Ibilbide judizialaren aldaketa | Valoracion familiar de la Audiencia Provincial. |
| `05 GeldiketaM.mp4` | `05-geldiketa.mp4` | Que ocurrio aquella noche / Zer gertatu zen gau hartan | Secuencia documental de denegacion de acceso. |
| `06 ItxarM.mp4` | `06-itxaronaldia.mp4` | Que ocurrio aquella noche / Zer gertatu zen gau hartan | Secuencia documental de espera. |
| `07 Prestaketa.mp4` | `07-prestaketa.mp4` | Que ocurrio aquella noche / Zer gertatu zen gau hartan | Secuencia documental de reunion previa. |
| `08 KolpeaMD.mp4` | `08-kolpea.mp4` | Que ocurrio aquella noche / Zer gertatu zen gau hartan | Secuencia documental de la agresion, con el mismo tratamiento visual que el resto. |
| `InformeOrgCriminalSep.pdf` | `crimen-de-mitika-informe-familiar-2026-09-03.pdf` | Documentacion / Dokumentazioa | PDF descargable original, autoria familiar, 42 paginas, aprox. 4,4 MB, version de portada del 3 de septiembre de 2026. |

## Implementacion

Los binarios se importan mediante `wp eval-file
wp-content/themes/kermanentzat-prototype/inc/sync-case-media.php` con la variable
`KERMANENTZAT_CASE_MEDIA_SOURCE_DIR` apuntando a una carpeta montada que contiene
los originales y una subcarpeta `posters/` con las portadas JPEG generadas.

El script:

- copia los recursos a `wp-content/uploads/kermanentzat-case-media/`;
- registra los ocho MP4 y el PDF en la biblioteca multimedia con metadatos
  `_kermanentzat_case_media_*`;
- genera las pistas `.vtt` versionadas desde `inc/case-media-content.php`;
- actualiza de forma completa las dos paginas existentes sin duplicar bloques;
- guarda una copia JSON previa de cada pagina en
  `wp-content/uploads/kermanentzat-case-media-backups/`.

Los reproductores usan controles nativos, reproduccion manual y
`preload="none"`. No se cargan iframes, proveedores externos, scripts nuevos,
cookies ni almacenamiento de navegador.

## Verificacion

Antes de publicar se debe comprobar:

- reproduccion, sonido, busqueda temporal y pantalla completa de los ocho MP4;
- ausencia de descargas automaticas de video antes de pulsar reproduccion;
- equivalencia editorial ES/EU, portadas, titulos, descripciones y subtitulos;
- enlaces de descarga/apertura del PDF en ambos idiomas;
- hash del PDF publico frente al original;
- escritorio y movil, teclado, foco visible y ausencia de desbordamientos;
- `scripts/test-privacy.ps1` y lint PHP.
