# Design System

## Direction

**Cartel vivo, documento claro.** La portada sigue una estructura «Memoria primero»: Kerman aparece como persona antes de introducir el caso. La siguiente escena adopta la fuerza tipográfica de «Cartel frontal». La captura elegida por el responsable fija el equilibrio de blanco, negro y rojo, pero ninguno de sus textos factuales generados se reutiliza.

La escena física es una pancarta ciudadana extendida sobre una mesa de documentación: a plena luz, rodeada de personas que necesitan entender, comprobar y decidir si apoyan. Esto exige una base clara, contraste frontal y lectura larga calmada.

## Color

Estrategia **Committed**, con rojo concentrado en aproximadamente 10–15 % de la superficie visible. El blanco domina la memoria y la lectura; el negro estructura navegación, evidencia y transiciones.

| Token | Valor | Uso |
|---|---|---|
| `--color-campaign` | `#ff3131` / `oklch(65% 0.24 27)` | Franjas, palabras display y superficies cortas. Texto negro, no blanco pequeño. |
| `--color-action` | `#d71920` / `oklch(53% 0.21 27)` | Botón primario con texto blanco; cumple AA para texto normal. |
| `--color-ink` | `#090909` / `oklch(14% 0 0)` | Texto, cabecera, bandas oscuras. |
| `--color-canvas` | `#ffffff` / `oklch(100% 0 0)` | Fondo principal. |
| `--color-soft` | `#f1f1f1` / `oklch(95.5% 0 0)` | Borradores y separación secundaria. |
| `--color-muted` | `#565656` / `oklch(45% 0 0)` | Metadatos sobre blanco. |
| `--color-line` | `#c9c9c9` / `oklch(82% 0 0)` | Reglas y límites no interactivos. |

No se usan crema, beige, degradados ni colores adicionales. La naturaleza editorial nunca depende solo del rojo.

## Typography

- **Display:** Anton, autoalojada bajo SIL OFL. Solo titulares breves, lemas y franja; mayúsculas cuando el texto lo permita.
- **Lectura/UI:** Public Sans, autoalojada, pesos 400/600/700. Párrafos, navegación, metadatos y acciones.
- Fallback: `Impact, "Arial Narrow", sans-serif` para display; `Arial, sans-serif` para lectura.
- Display fluido: `clamp(3.25rem, 9vw, 6rem)`, interletraje nunca inferior a `-0.035em`.
- Cuerpo: `clamp(1rem, 0.96rem + 0.2vw, 1.125rem)`, interlineado 1.55–1.7, medida máxima 68ch.
- Euskera y castellano comparten escala; los titulares se prueban con las cadenas más largas de ambos idiomas.

## Layout

- Mobile-first, contenido central máximo de 1440 px y márgenes fluidos `clamp(1rem, 4vw, 4rem)`.
- Grandes bandas horizontales y bordes rectos; no hay cuadrículas repetitivas de tarjetas.
- Hero memoria primero: retrato original autorizado y nombre/mensaje en dos campos complementarios.
- Segunda escena: bloque documental con un titular display contundente y cuerpo sobrio; la naturaleza editorial se explica solo cuando evita una confusión real, no mediante una etiqueta repetida sobre cada mensaje propio.
- Tercera escena: apoyo y contacto sobre negro/blanco, con rojo reservado a la acción principal.
- En móvil todo fluye en una columna. En escritorio, hero y bloques documentales pueden usar dos columnas asimétricas.
- Objetivos táctiles mínimos de 44 × 44 px.

## Components

### Header

Cabecera negra, identidad «Egia» en blanco y «Kermanentzat» en rojo, navegación horizontal en escritorio y panel nativo accesible en móvil. El selector ES/EU abre siempre la página equivalente.

### Campaign ticker

Franja roja con texto negro y duplicación semánticamente oculta para el bucle. Es decorativa: el mensaje existe también en contenido estático. Se detiene con reducción de movimiento.

### Hero

El retrato es el recorte limpio autorizado `AST-019`, nunca una recreación generada. «Memoria primero» es la única portada; no existe conmutador de variantes. La composición es mobile first y no activa dos columnas hasta disponer de al menos 1088 px.

### Actions

Botones rectangulares, radio máximo 2 px. Primario rojo oscuro/blanco; secundario blanco/negro con borde negro. Foco de 3 px separado del componente.

### Editorial nature

Hecho documentado, fuente, información periodística y posición asociativa utilizan rótulo textual, emisor y fecha. No se presentan como tarjetas de colores intercambiables.

### Transferencia

Bloque de datos bancarios verificados con titular, IBAN, BIC, concepto recomendado y acciones accesibles para copiar. El certificado de titularidad y cualquier identificador personal permanecen fuera del frontend y del CMS público.

## Motion

- Una entrada inicial de 500–700 ms mediante recorte y desplazamiento corto.
- Franja tipográfica lineal lenta; pausa cuando la pestaña no está visible.
- Máximo cuatro revelados narrativos por página, siempre sobre contenido visible por defecto.
- Interacciones entre 120–220 ms con curva `cubic-bezier(.16, 1, .3, 1)`.
- `prefers-reduced-motion: reduce` elimina desplazamientos, bucles y scroll suave sin perder contenido.
- Sin parallax, rebote, sonido, vibración o movimiento ligado a violencia.

## Responsive behavior

- Comprobaciones objetivo: 320, 390, 768, 1024 y 1440 px.
- En móvil y tablet, el primer viewport útil contiene retrato, mensaje y las dos acciones principales; el retrato adapta su altura al `svh` disponible sin recortarse ni empujar botones fuera de la vista.
- Menú móvil mediante `<details>` o botón/dialog semántico, sin interacción exclusiva de hover.
- El retrato cambia de recorte con `object-position`, no de archivo ni de identidad.
- Los encabezados nunca desbordan; `overflow-wrap` y escala específica protegen palabras largas.
- La franja puede ser estática en dispositivos de menor rendimiento.

## Accessibility

Objetivo WCAG 2.2 AA. El rojo `#ff3131` lleva texto negro; el botón con texto blanco usa `#d71920`. Orden de foco, enlace de salto, landmarks, estados actuales, `aria-live` solo cuando sea necesario y alternativas ES/EU para imágenes. La web conserva significado con CSS, imágenes o JavaScript desactivados.

## Avoid

Sensacionalismo, datos generados, CCTV, recreaciones, caras reinterpretadas, tarjetas redondeadas, sombras grandes, glassmorphism, degradados, iconografía judicial genérica, decoración de startup, texto en rojo sobre negro y párrafos en tipografía condensada.
