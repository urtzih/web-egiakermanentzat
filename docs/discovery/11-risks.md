# 11 — Registro inicial de riesgos

Fecha de revisión: 2026-07-20. Probabilidad e impacto: baja, media, alta.

Leyenda: `CONFIRMADO` · `HIPÓTESIS` · `PENDIENTE DE VERIFICACIÓN` · `DECISIÓN NECESARIA` · `RIESGO` · `FUENTE NECESARIA`.

| ID | Riesgo | Prob. | Impacto | Mitigación | Responsable propuesto | Decisión pendiente |
|---|---|---:|---:|---|---|---|
| RSK-001 | Error factual o fuente insuficiente | Alta | Alta | `SRC/CLM`, doble revisión, fecha y corrección. | Editor factual | Autoridad de verificación. |
| RSK-002 | Atribución de culpabilidad/problema jurídico | Media | Alta | Separar voces, regla de revisión jurídica, lenguaje neutral. | Junta + jurídico | Matriz de escalado. |
| RSK-003 | Revictimización o tratamiento indigno | Media | Alta | Aprobación familiar, revisión de sensibilidad, no CCTV. | Revisor de memoria | Persona con autoridad. |
| RSK-004 | Exposición de datos personales | Media | Alta | Minimización, anonimización, revisión de documentos, plazos. | Responsable privacidad | Inventario/base jurídica. |
| RSK-005 | Derechos de imagen/autor vulnerados | Alta | Alta | Manifiesto de activos, permiso escrito, crédito, bloqueo de publicación. | Gestor de medios | Modelo de autorización. |
| RSK-006 | CMS comprometido o vandalizado | Media | Alta | MFA, mínimo privilegio, parcheo, WAF/monitor, backups. | Admin técnico | Hosting y SLA. |
| RSK-007 | Pérdida de acceso al CMS/dominio | Media | Alta | Gestor institucional, dos custodios, recuperación ensayada. | Junta + admin | Titularidad y custodios. |
| RSK-008 | Dependencia de una sola persona | Alta | Alta | Documentación, suplentes, hosting gestionado, exportaciones. | Junta | Presupuesto/roles. |
| RSK-009 | Suplantación de web o donaciones | Media | Alta | Dominio/canales verificados, aviso anti-fraude, monitorización. | Junta + tesorería | Dominio y canales oficiales. |
| RSK-010 | Donaciones fraudulentas/chargebacks | Media | Media/Alta | Proveedor, alertas, conciliación, devolución y registro. | Tesorería | Proveedor/política. |
| RSK-011 | Estado judicial desactualizado | Alta | Alta | Revisión programada, caducidad visible, responsable y alertas. | Editor + jurídico | Frecuencia/SLA. |
| RSK-012 | Publicación accidental | Media | Alta | Sin auto-publicación; permisos, preview, doble aprobación sensible. | Editor/admin | Reglas por tipo. |
| RSK-013 | Traducción incorrecta o desigual | Alta | Alta | Fuente bloqueada, traducción enlazada, revisión humana y paridad. | Traductor/revisor | Responsables ES/EU. |
| RSK-014 | Información sensible en archivos/metadatos | Media | Alta | Tratamiento, OCR/revisión, limpieza de metadatos y copia publicable. | Documentalista | Herramienta/proceso. |
| RSK-015 | Ataque o campaña coordinada | Media | Alta | Plan de incidentes, moderación cerrada, logs, mensajes preparados. | Portavocía + admin | Escalado/comunicación. |
| RSK-016 | Falta de mantenimiento | Alta | Alta | Presupuesto anual, propietario por tarea, calendario y contrato. | Junta | Financiación plurianual. |
| RSK-017 | Destino de donaciones confuso | Media | Alta | Texto concreto, informes, condiciones y conciliación. | Tesorería | Destino/transparencia. |
| RSK-018 | Proveedor bloquea/exporta mal contenido | Media | Media/Alta | Exportación probada, copias abiertas, cláusula de salida. | Admin/junta | CMS/proveedor. |
| RSK-019 | Traducción automática publicada | Media | Alta | Estado no publicable y aprobación lingüística obligatoria. | Editor | Configuración CMS. |
| RSK-020 | Comentarios/testimonios generan daño o moderación | Alta | Alta | Excluir del MVP; canal privado mínimo. | Junta | Revaluar después. |
| RSK-021 | Fotografía de multitud expone a personas | Media | Media/Alta | Necesidad, contexto, encuadre, derechos y respuesta de retirada. | Gestor medios | Criterio/consentimiento. |
| RSK-022 | Copias/analítica filtran datos | Baja/Media | Alta | Cifrado, acceso, retención, DPA y pruebas. | Admin + privacidad | Proveedores. |

## Cinco riesgos prioritarios

1. RSK-001 — errores factuales.
2. RSK-002 — responsabilidad/afirmaciones jurídicas.
3. RSK-005 — derechos de imagen y autor.
4. RSK-011 — estado judicial desactualizado.
5. RSK-008/016 — dependencia y falta de mantenimiento.

`DECISIÓN NECESARIA` La junta debe aceptar propietario, tolerancia y frecuencia de revisión de cada riesgo antes del desarrollo.

`FUENTE NECESARIA` Asesoría jurídica, inventario de datos, contratos/proveedores, política financiera y responsables nominales.
