# 08 — Dirección visual, identidad y tono

Fecha de revisión: 2026-07-21. Se ha aplicado una lectura de marca y accesibilidad; no es diseño final.

Leyenda: `CONFIRMADO` · `HIPÓTESIS` · `PENDIENTE DE VERIFICACIÓN` · `DECISIÓN NECESARIA` · `RIESGO` · `FUENTE NECESARIA`.

## Señales observadas

`CONFIRMADO` En las 33 publicaciones revisadas aparecen de forma consistente:

- rojo vivo, negro, blanco y grises;
- silueta/retrato gráfico de Kerman;
- tipografía de campaña, condensada, mayúscula y contundente;
- alto contraste y composición directa;
- fotografías documentales de comunidad, pancartas, homenajes y actos;
- mensajes en euskera y castellano, juntos o en piezas paralelas;
- tono de memoria, denuncia, justicia, verdad, continuidad y movilización.

`PENDIENTE DE VERIFICACIÓN` No se dispone de manual, archivos vectoriales, tipografías licenciadas, valores cromáticos, originales fotográficos ni autorización de Kerman/familia.

`CONFIRMADO` La familia/asociación ha entregado `AST-017` y `AST-018`. La versión PNG mide 943 × 2000 px y utiliza como colores dominantes blanco, negro y rojo aproximado `#FF3131`. La imagen combina tipografía display irregular, un retrato tramado reconocible de Kerman y el lema «Egia eta justizia Kermanentzat».

## Dirección inicial

- **Humana**: abrir espacio a la vida y memoria de Kerman con contenido familiarmente aprobado.
- **Digna**: fotografía real, edición sobria, sin fetichizar dolor o violencia.
- **Clara**: tipografía de lectura generosa; la condensada solo en titulares breves.
- **Reconocible**: rojo/negro/silueta como firma, con fondos claros para lectura larga.
- **Movilizadora**: llamadas directas, verbos concretos y jerarquía; sin urgencia artificial.
- **Verificable**: lenguaje visual distinto para fuente oficial, comunicado, medio y posición de la asociación.
- **Accesible**: contraste comprobado, no comunicar estados solo por color, zoom/reflow y alt text contextual.

## Sistema visual provisional

| Capa | Propuesta | Condición |
|---|---|---|
| Marca | Rojo principal + negro/blanco; silueta en momentos identitarios. | Obtener original y permisos; validar contraste y usos. |
| Lectura | Sans humanista o grotesca legible para cuerpo; ancho de línea controlado. | Licencia y soporte completo euskera/castellano. |
| Campaña | Condensada/display para lemas, carteles y llamadas breves. | No usar en párrafos ni todo en mayúsculas. |
| Fotografía | Memoria, comunidad y movilización con pies, fecha y crédito. | Consentimiento, derechos, sensibilidad y original. |
| Evidencia | Tratamiento sobrio, metadatos visibles, iconografía no judicializante. | Accesibilidad de documentos y explicación de estado. |
| Movimiento | Expresivo en cabecera y transiciones narrativas; sobrio en lectura larga. | No dramatizar, recrear violencia ni ocultar contenido; respetar reducción de movimiento. |

## Referencias aportadas y lectura de diseño

`CONFIRMADO` El responsable del proyecto aporta `SRC-045`–`SRC-054` como referencias de intención visual. No se copiará ninguna identidad completa; se combinan principios compatibles con la voz existente de Kermanentzat.

| Referencias | Qué se incorpora | Qué se descarta o modera |
|---|---|---|
| Escape Velocity | Estructura clásica por grandes bandas y recorrido fácil de entender. | Apariencia de plantilla, ornamento o bloques genéricos. |
| Wispr Flow | Cabecera tipográfica viva y alternancia marcada de escenas. | Paleta crema/lavanda, serif dominante y redondeo excesivo. |
| Augen Pro | Aparición limpia del texto, espacio y pausa. | Frialdad tecnológica y controles flotantes propios de producto. |
| Hyer / Dayos | Escala tipográfica agresiva, titulares que ocupan el plano y contraste radical. | Tono comercial, objetos 3D y tamaños que desborden móvil. |
| Ventriloc / Ada | Ritmo al hacer scroll, secciones con respiración y entrada progresiva del contenido. | Animar todos los bloques del mismo modo o depender de contenido inicialmente oculto. |
| Relace / Replit | Sencillez estructural y un color intenso concentrado en momentos clave. | Estética de herramienta, tarjetas blandas, píldoras y lenguaje SaaS. |
| Reducto | Orden editorial, lectura documental, líneas y superficies sin sombras decorativas. | Revista de lujo, serif ornamental o distanciamiento emocional. |

## Dirección visual propuesta para el prototipo

Nombre de trabajo: **«Cartel vivo, documento claro»**.

La web debe sentirse como las pancartas de Kermanentzat trasladadas al espacio digital: directa, pública, humana y colectiva. La intensidad se concentra en titulares, transiciones y llamadas; el relato del caso se convierte en una superficie calmada y verificable.

### Composición

- Base clásica y fácil de seguir, organizada en grandes bandas verticales.
- Cabecera de alto impacto con texto protagonista, retrato/silueta aprobada y dos acciones claras.
- Cambios de escena blanco → negro → rojo, sin encerrar cada contenido en una tarjeta.
- Bordes rectos o radios mínimos; líneas gruesas y cortes tipográficos inspirados en pancartas.
- Texto largo en una columna de 65–75 caracteres, con fuentes y metadatos en una columna auxiliar solo cuando haya espacio.
- En móvil, la fuerza procede de escala, recorte y ritmo; nunca de hacer el titular ilegible o horizontalmente desplazable.

### Color

- Rojo de trabajo `#FF3131` como golpe de campaña y acción, sujeto a ajuste de contraste.
- Negro casi puro para estructura y escenas de máxima intensidad.
- Blanco o gris neutro muy claro para lectura larga; no crema, beige ni estética de papel de lujo.
- El rojo no se utilizará para distinguir por sí solo hechos, opiniones o alertas.

### Tipografía

- Display condensada, pesada e irregular en titulares muy breves, lemas y franjas en movimiento.
- Sans legible y menos agresiva para párrafos, navegación, fuentes y textos legales.
- Mayúsculas reservadas a mensajes cortos; el manifiesto y el resumen nunca se compondrán enteros en mayúsculas.
- Tamaño fluido con techo aproximado de 96 px en escritorio y ajustes específicos para palabras largas en euskera.
- La selección final de familias dependerá de licencia, rendimiento y cobertura completa ES/EU.

### Movimiento

1. **Franja tipográfica de cabecera**: lema o identidad en desplazamiento lento y continuo, sin contener la única versión del mensaje. Se pausa cuando corresponda y se sustituye por una composición estática con `prefers-reduced-motion`.
2. **Entrada inicial orquestada**: identidad, titular, retrato y acciones aparecen en una única secuencia breve; no se repite en cada bloque.
3. **Revelados narrativos**: títulos o líneas clave pueden entrar mediante recorte/máscara al alcanzar 2–4 hitos de la página. El contenido ya debe existir y ser legible si JavaScript falla.
4. **Transiciones de sección**: cambios de color y posición acompañan el paso de memoria → hechos → posición → apoyo, sin parallax agresivo.
5. **Interacciones**: enlaces, botones y selector de idioma responden con desplazamientos o inversiones pequeñas; sin rebotes, sonido, vibración ni efectos que trivialicen el caso.

`CONFIRMADO` La agresividad visual se aplicará a la jerarquía, no a aumentar la dureza de las afirmaciones. Un titular grande sigue sujeto a la misma atribución, fuente y revisión jurídica que cualquier otro texto.

`HIPÓTESIS` La portada puede abrir con una franja móvil «JUSTIZIA KERMANENTZAT · JUSTICIA PARA KERMAN» y un titular familiar adaptado en una escena de negro, blanco y rojo. El contenido exacto, velocidad y tratamiento del retrato se validarán en el prototipo.

## Variantes que debe probar el prototipo

- **Decisión tras la comparación**: se conserva únicamente B — «Memoria primero», con el recorte limpio `AST-019`, entrada humana y transición posterior hacia la denuncia mediante tipografía de campaña. A — «Cartel frontal» queda retirada como portada independiente.

La estructura y el contenido serán iguales. La familia podrá compararlas en móvil y escritorio antes de fijar la portada; no se producirán dos sistemas completos.

## Criterios visuales de aceptación

- En cinco segundos se identifica Kermanentzat y se distinguen conocer/apoyar.
- La portada impacta sin utilizar CCTV, recreaciones o acusaciones no atribuidas.
- La animación no bloquea lectura, navegación, traducción ni carga inicial.
- La experiencia con reducción de movimiento conserva jerarquía y significado.
- Ningún titular se corta o desborda en 320 px, tablet o escritorio.
- El resumen mantiene lectura cómoda aunque la portada sea agresiva.
- ES y EU reciben la misma calidad de composición, sin tratar una traducción como secundaria.

## Qué evitar

- estética comercial/startup o “producto”;
- plantilla genérica de tarjetas repetidas;
- aspecto de medio sensacionalista;
- institucionalidad fría que borre la comunidad;
- imágenes de cámaras de seguridad o últimos momentos;
- rojo en superficies extensas que reduzca legibilidad;
- collage de logos/medios sin jerarquía ni permisos;
- IA generativa para representar a Kerman o recrear hechos.

## Reutilización y derechos

Los candidatos propios están en [assets/instagram/manifest.csv](./assets/instagram/manifest.csv). Instagram entrega derivados comprimidos (100–640 px en la muestra); deben pedirse originales.

El manifiesto bilingüe facilitado directamente por la familia está registrado en [assets/family-provided/manifest.csv](./assets/family-provided/manifest.csv). Confirma el uso de tipografía condensada, blanco/negro, silueta y énfasis tipográfico, pero su imagen vertical no será el formato de lectura principal: se convertirá en contenido HTML accesible y podrá conservarse como documento histórico.

Las pancartas `AST-017` y `AST-018` son referencias propias de mayor calidad para la identidad. `AST-018` es candidato principal para explorar una cabecera o bloque de campaña mediante un derivado horizontal, sin alterar el sentido del retrato ni convertir el texto vertical en el único acceso al lema. La segunda página de `AST-017` es una convocatoria histórica y no debe reutilizarse como llamada vigente.

- Potencialmente reutilizables tras permiso: silueta, piezas de lanzamiento, carteles propios, conmemoración y fotografía comunitaria.
- Requieren además revisar marcas/créditos: Korrika, fotografías atribuidas a medios y carteles colaborativos.
- Solo referencia/enlace: Hala Bedi, Araski, Gasteizko AEK y Arabako Alea.
- Excluidos: CCTV, piezas narrativas sensibles, capturas de prensa/TV como contenido gráfico habitual.

`DECISIÓN NECESARIA` Nombrar responsable de marca y familiar con autoridad para aprobar representación, fotografías y tono.

`RIESGO` Confundir publicación en la cuenta con titularidad de derechos puede producir reclamaciones y obligar a retirar piezas centrales.
