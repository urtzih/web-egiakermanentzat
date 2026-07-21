# 07 — Opciones de donación

Fecha de revisión: 2026-07-21. Análisis conceptual, no asesoramiento jurídico/fiscal ni integración.

Leyenda: `CONFIRMADO` · `HIPÓTESIS` · `PENDIENTE DE VERIFICACIÓN` · `DECISIÓN NECESARIA` · `RIESGO` · `FUENTE NECESARIA`.

## Necesidades

Donación puntual y recurrente; móvil; cantidades pequeñas; tarjeta; Bizum; transferencia; confirmación/justificante; devoluciones; conciliación; privacidad; accesibilidad; transparencia del receptor y destino.

## Datos ya confirmados para transferencia

- `CONFIRMADO` Titular: **Egia Kermanentzat Elkartea**.
- `CONFIRMADO` NIF: `G93797744`.
- `CONFIRMADO` IBAN de donaciones: `ES08 3035 0079 2707 9006 2136`; su estructura y dígitos de control se han validado.
- `CONFIRMADO` BIC/SWIFT: `CLPEES2MXXX`.
- `CONFIRMADO` El certificado de Laboral Kutxa del 2026-07-18 identifica a la asociación como titular. El original `DOC-002` permanece restringido porque contiene identificadores personales de apoderados.

`DECISIÓN NECESARIA` Antes de publicar la transferencia: confirmar el concepto recomendado, destino concreto de los fondos, canal para incidencias, política de devoluciones, conciliación y si se solicitarán datos para certificados. La existencia de cuenta no acredita por sí sola derecho a deducción fiscal.

## Borrador del destino de las aportaciones

### Versión breve — castellano

`HIPÓTESIS` **Tu aportación ayuda a sostener la búsqueda de verdad, justicia y reparación, así como las acciones jurídicas, informativas, sociales y preventivas impulsadas por Egia Kermanentzat Elkartea.**

### Versión ampliada — castellano

`HIPÓTESIS` Tu aportación ayudará a sostener el trabajo de Egia Kermanentzat Elkartea: documentar y difundir el caso de Kerman; impulsar las acciones jurídicas y de reparación que resulten necesarias; desarrollar campañas de comunicación, sensibilización y prevención; organizar actos y movilizaciones; elaborar informes y propuestas; y cubrir los gastos necesarios para el funcionamiento de la asociación.

Los fondos serán gestionados por la asociación, sin ánimo de lucro, y se destinarán a sus fines estatutarios de verdad, justicia, reparación y prevención. Antes de publicarse, la asociación revisará este texto y concretará cómo informará sobre la utilización de las aportaciones.

### Bertsio laburra — euskara

`HIPÓTESIS` **Zure ekarpenak egiaren, justiziaren eta erreparazioaren aldeko lana sostengatzen lagunduko du, baita Egia Kermanentzat Elkarteak bultzatutako ekintza juridikoak, komunikazio-ekintzak, gizarte-ekintzak eta prebentzio-ekintzak ere.**

### Bertsio hedatua — euskara

`HIPÓTESIS` Zure ekarpenak Egia Kermanentzat Elkartearen lana sostengatzen lagunduko du: Kermanen kasua dokumentatu eta gizarteratzea; beharrezko ekintza juridikoak eta erreparazio-neurriak bultzatzea; komunikazio-, sentsibilizazio- eta prebentzio-kanpainak garatzea; ekitaldiak eta mobilizazioak antolatzea; txostenak eta proposamenak prestatzea; eta elkartearen jarduerarako beharrezko gastuei aurre egitea.

Funtsak irabazi-asmorik gabeko elkarteak kudeatuko ditu, eta estatutuetan jasotako helburuetara bideratuko dira: egia argitzera, justizia eta erreparazioa sustatzera, eta antzeko egoerak prebenitzera. Argitaratu aurretik, elkarteak testu hau berrikusiko du eta ekarpenen erabilerari buruzko informazioa nola emango duen zehaztuko du.

`PENDIENTE DE VERIFICACIÓN` La versión en euskera requiere revisión humana antes de publicación. Ambos textos son borradores editoriales derivados de los estatutos, no una asignación presupuestaria cerrada.

## Comparativa

| Solución | Encaje | Ventajas | Límites/condiciones |
|---|---|---|---|
| Stripe Checkout/Payment Links | Tarjeta puntual y recurrente de importes medios. | UX móvil, recurrencias, recibos y menor superficie propia de tarjeta. | Comisión fija perjudica 1 €; Billing y métodos añaden coste; conciliación, devoluciones, privacidad y cuenta legal deben configurarse. |
| Teaming u otra microdonación | 1 € mensual. | Producto diseñado para microaportación; Teaming declara 1 €/mes sin comisión en su FAQ. | Dependencia de plataforma, reglas, datos y disponibilidad; verificar contrato, retirada, fiscalidad y exportación. |
| Plataforma especializada para asociaciones | Campañas, recurrentes, CRM/certificados según proveedor. | Puede reducir operación y aportar herramientas sectoriales. | Precio, lock-in, tratamiento de datos y funciones varían; comparar proveedores concretos. |
| Crowdfunding | Objetivo y campaña temporal. | Narrativa de campaña y difusión. | No siempre adecuada para apoyo continuo; comisiones, objetivos, reembolsos y reputación. |
| Transferencia | Aportación directa y importes altos. | Simple y generalmente bajo coste. | Fricción móvil, conciliación manual, exposición de IBAN, concepto y datos del donante. |
| Bizum para donaciones | Aportación móvil inmediata en España. | Familiar y rápida; código de cinco dígitos para entidades según Bizum. | Requiere acuerdo con banco/código y condiciones; recurrencia no equivalente a domiciliación. |
| Híbrida | Transferencia + Bizum + microdonación + tarjeta. | Cada importe usa el canal más eficiente. | Más conciliación, textos, proveedores y soporte; hay que evitar confundir opciones. |

## Economía de micropagos

`CONFIRMADO` En la consulta del 2026-07-20, Stripe España publicaba para tarjeta estándar del EEE **1,5 % + 0,25 €** y Billing por uso **0,7 %**; son cifras cambiantes y deben verificarse antes de contratar. En una aportación de 1 €, la parte fija hace ineficiente la tarjeta recurrente.

`HIPÓTESIS` Solución provisional a evaluar: **Teaming o equivalente para 1 €/mes**, **Bizum/transferencia** para apoyo directo y **Stripe Checkout/Payment Links** para tarjeta de importes mayores. No desarrollar recurrencias a medida.

## Requisitos funcionales previos

- Mostrar nombre legal y titular receptor antes de confirmar.
- Explicar destino, libre disposición o afectación y posibles cambios.
- No prometer deducción fiscal ni certificado sin confirmar elegibilidad.
- Consentimiento separado para comunicaciones; donar no implica suscripción.
- Recibo de operación y canal de incidencias/devolución.
- Referencia para conciliación, acceso por roles y exportación contable.
- Flujo accesible y usable sin depender de un CAPTCHA inaccesible.
- Registro de fecha de verificación de comisiones y condiciones.

## Preguntas administrativas y legales — bloqueantes

1. `PARCIALMENTE CONFIRMADO`: nombre legal, NIF, registro, domicilio y cargos recibidos. Falta confirmar facultad formal de contratación y si el domicilio será público.
2. `PARCIALMENTE CONFIRMADO`: cuenta y titularidad confirmadas. Falta designar quién opera, concilia y sustituye a la persona responsable.
3. ¿Está la entidad habilitada para recibir donaciones y expedir certificados con beneficio fiscal? ¿Qué confirma asesoría?
4. `PARCIALMENTE CONFIRMADO`: existe un borrador basado en fines estatutarios. Falta aprobarlo y concretar cómo se informará sobre el uso de los fondos.
5. ¿Qué datos necesita tesorería y durante cuánto tiempo se conservarán?
6. ¿Quién atiende devoluciones, errores, chargebacks, fraude y certificados?
7. ¿Qué banco/proveedor acepta a la entidad y con qué contrato/comisiones?
8. ¿Se presentará Modelo 182 u otra obligación? ¿Quién lo hará?
9. ¿Puede aceptarse donación anónima y hasta qué límites/procedimiento?
10. ¿Qué controles de prevención de fraude y suplantación exige la entidad?

## Preguntas importantes

- Importe puntual y recurrente por defecto; ¿1 € es promesa pública o ejemplo?
- Métodos prioritarios por público y dispositivo.
- Periodicidad de informes de destino y formato de transparencia.
- Reembolsos, cancelación recurrente y tiempos de respuesta.
- Acceso/exportación si cambia el proveedor.
- Textos ES/EU y revisor legal/lingüístico.

`RIESGO` Presentar una deducción inexistente, un receptor ambiguo o un destino impreciso puede causar perjuicio y pérdida de confianza.

## Fuentes

- [Stripe España — precios](https://stripe.com/es/pricing), consulta 2026-07-20.
- [Bizum — donar](https://www.bizum.com/es/donar/) e [integración para entidades](https://bizum.com/es/integrar-bizum-para-donaciones/).
- [Teaming — cuánto cuesta](https://faqs.teaming.net/es/cuanto-cuesta-teaming/).
- [AEAT — Modelo 182](https://sede.agenciatributaria.gob.es/Sede/procedimientos/GI02.shtml).
