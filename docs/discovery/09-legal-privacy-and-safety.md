# 09 — Legal, privacidad, accesibilidad, seguridad y gobernanza

Fecha de revisión: 2026-07-21. Requisitos iniciales; los textos legales deben adaptarse por profesional al tratamiento real.

Leyenda: `CONFIRMADO` · `HIPÓTESIS` · `PENDIENTE DE VERIFICACIÓN` · `DECISIÓN NECESARIA` · `RIESGO` · `FUENTE NECESARIA`.

## Accesibilidad y lenguaje

Objetivo `CONFIRMADO`: **WCAG 2.2 nivel AA**.

- HTML semántico, regiones y jerarquía de encabezados coherente.
- Navegación completa por teclado, foco visible/no oculto y sin trampas.
- Contraste AA, estados no dependientes solo de color y zoom/reflow.
- Selector de idioma, menús, diálogos y formularios con nombre accesible.
- Etiquetas, ayuda, errores claros y resumen de errores; no borrar datos al fallar.
- Alt text aprobado en ES/EU; imágenes decorativas vacías; transcripciones/subtítulos en multimedia.
- PDFs publicables accesibles o alternativa HTML equivalente.
- Lectura sencilla: resumen inicial, fechas explícitas, términos explicados y párrafos breves.
- Responsive desde móvil; objetivos táctiles, sin scroll horizontal indebido.
- Presupuesto de rendimiento: contenido principal rápido en red móvil razonable y sin saltos evitables.
- Respetar `prefers-reduced-motion`; nunca autoplay de material sensible.
- Pruebas automáticas, teclado, lector de pantalla y revisión manual con personas usuarias.

Fuentes: [WCAG](https://www.w3.org/WAI/standards-guidelines/wcag/) y [novedades WCAG 2.2](https://www.w3.org/WAI/standards-guidelines/wcag/new-in-22/).

## Privacidad

`CONFIRMADO` El responsable previsto es **Egia Kermanentzat Elkartea**, NIF `G93797744`. El domicilio estatutario se conoce, pero su publicación queda pendiente de una decisión expresa porque podría coincidir con una dirección de especial sensibilidad.

`CONFIRMADO` El correo público provisional es `justiziakermanentzat@gmail.com`. Antes de usarlo para formularios o solicitudes de derechos se definirán responsables de acceso, MFA, recuperación, conservación y sustitución futura por correo del dominio.

- Inventario de tratamientos y responsable legal antes del lanzamiento.
- Minimización: contacto pide solo datos necesarios para la finalidad elegida.
- Separar contacto, prensa, colaboración, donación y comunicaciones; no reutilizar sin base válida.
- Información por capas, base jurídica, destinatarios/proveedores, transferencias, conservación y derechos.
- Sin analítica ni marketing no esenciales antes de consentimiento cuando corresponda.
- Preferir analítica sin cookies y métricas agregadas; documentar aun así proveedor y retención.
- No enviar por email abierto documentos sensibles o datos de terceros sin canal/proceso adecuado.
- No publicar el certificado bancario íntegro: contiene identificadores personales de personas apoderadas. Para transferencias bastan titular, IBAN/BIC, finalidad y condiciones aprobadas.
- Borrado/anonimización por plazo; exportación y atención de derechos con responsable y registro.
- Formulario anti-spam respetuoso: honeypot, límites de frecuencia y desafío accesible solo si es necesario.

Fuentes: AEPD sobre [protección de datos por defecto](https://www.aepd.es/derechos-y-deberes/cumple-tus-deberes/medidas-de-cumplimiento/proteccion-de-datos-por-defecto), [principios](https://www.aepd.es/preguntas-frecuentes/2-tus-obligaciones-como-responsable-del-tratamiento/4-los-principios-del-tratamiento/FAQ-0207-que-principios-debo-cumplir) y [cookies](https://www.aepd.es/es/documento/guia-cookies.pdf).

## Seguridad y continuidad

- MFA obligatorio para administradores, editores y proveedores; cuentas individuales, no compartidas.
- Mínimo privilegio, revisión trimestral de accesos y baja inmediata al terminar colaboración.
- Gestor de contraseñas de la asociación y procedimiento de recuperación con dos responsables.
- Actualizaciones del CMS/extensiones con responsable, ventana y entorno de prueba.
- Backups cifrados, separados, automáticos y con restauración ensayada; definir RPO/RTO.
- Registro de inicio de sesión, cambios, publicación, permisos y configuración; alertas de actividad anómala.
- TLS, cabeceras de seguridad, limitación de intentos, WAF/CDN si el riesgo lo justifica y monitorización.
- Formularios con validación servidor, anti-CSRF, límites, saneado y entrega fiable.
- Plan ante vandalismo/campaña coordinada: congelar publicación, preservar evidencia, restaurar, comunicar.
- Verificación pública de canales oficiales, dominio y métodos de donación; protección contra suplantación.
- Política de divulgación de vulnerabilidades y contacto técnico.

`RIESGO` El caso puede atraer ataques dirigidos, suplantación y campañas coordinadas; la disponibilidad y autenticidad son requisitos de confianza.

## Privacidad, imagen y derechos editoriales

- Evaluar necesidad y proporcionalidad de cada nombre, imagen y dato.
- Registrar autor, titular, licencia/permiso, crédito, recorte/adaptación y caducidad.
- Consentimiento específico cuando proceda; especial cuidado con menores, duelo, salud y testigos.
- Tratar documentos antes de publicar: datos personales, firmas, domicilios, identificadores y metadatos ocultos.
- No asumir que citar una red social autoriza copiar el recurso.
- Revisión jurídica obligatoria para afirmaciones de responsabilidad, contenido procesal sensible, privacidad dudosa o requerimientos.

## Flujo de gobernanza editorial

1. **Redactar** — autor crea borrador, audiencia y finalidad.
2. **Añadir fuentes** — editor asigna `SRC-###` y `CLM-###`.
3. **Revisar hechos** — revisor independiente comprueba texto contra fuente.
4. **Revisar lenguaje** — claridad, atribución, cuidado y no sensacionalismo.
5. **Revisar implicaciones legales** — condicional por regla de riesgo; resultado registrado.
6. **Traducir** — desde versión fuente bloqueada.
7. **Revisar traducción** — equivalencia factual, tono, nombres y CTA.
8. **Aprobar** — persona autorizada confirma versión, medios y fecha.
9. **Publicar** — editor autorizado; revisión visual/SEO/OG final.
10. **Corregir/archivar** — nota pública para cambio material y auditoría interna.

## Roles

| Rol | Puede | No debe |
|---|---|---|
| Administrador técnico | Configurar, actualizar, recuperar, gestionar cuentas. | Aprobar relato por autoridad técnica. |
| Autor | Crear borradores y proponer fuentes. | Publicar ni autoaprobar. |
| Editor | Estructurar, coordinar revisiones, programar/publicar aprobado. | Saltar revisión obligatoria. |
| Revisor de la asociación | Validar voz, objetivos, memoria y mandato. | Sustituir verificación documental. |
| Revisor factual | Validar afirmaciones contra fuentes. | Emitir asesoramiento jurídico fuera de competencia. |
| Traductor | Traducir contenido bloqueado y registrar decisiones. | Publicar traducción automática sin revisión. |
| Revisor jurídico | Evaluar riesgo, atribución, privacidad y publicación cuando proceda. | Ser cuello de botella de contenido no sensible sin regla acordada. |
| Tesorería | Validar receptor, destino, métodos, conciliación y transparencia. | Administrar técnicamente el CMS salvo rol separado. |

`DECISIÓN NECESARIA` Nombrar personas, suplentes y matriz de contenido que exige revisión jurídica.

`PENDIENTE DE VERIFICACIÓN` Base jurídica, plazos, proveedores, alojamiento, política de cookies y aplicabilidad normativa concreta.
