## Purpose

Ofrecer una lista de avisos por correo gratuita y automatizada con consentimiento demostrable, bajas efectivas y control editorial sobre cada envío.

## ADDED Requirements

### Requirement: Alta mínima y confirmada
La suscripción pública MUST solicitar únicamente el email, mostrar información de privacidad y consentimiento no premarcado y mantener la dirección inactiva hasta completar double opt-in.

#### Scenario: Alta desde el sitio
- **WHEN** una persona acepta la información y envía un email válido
- **THEN** recibe una solicitud de confirmación y no entra en campañas hasta confirmarla

### Requirement: Lista externa y minimización local
La lista de direcciones MUST mantenerse en el proveedor de email y WordPress MUST NOT almacenar direcciones de suscriptores ni escribirlas en logs públicos o administrativos.

#### Scenario: Envío del formulario
- **WHEN** una persona remite su dirección
- **THEN** el proveedor procesa el alta y WordPress conserva como máximo estado técnico sin la dirección

### Requirement: Importación con evidencia y supresión
Una importación MUST aceptar únicamente contactos con evidencia individualizable de consentimiento, MUST deduplicar direcciones y MUST excluir bajas, reclamaciones y registros sin alcance suficiente.

#### Scenario: Fila sin prueba de consentimiento
- **WHEN** una fila del Excel carece de fecha, procedencia o alcance verificable
- **THEN** la simulación la rechaza y no se incorpora a la lista activa

### Requirement: Envío elegido por publicación
Cada novedad MUST ofrecer una opción desmarcada por defecto para solicitar un aviso al publicarse; un borrador, una actualización posterior o una publicación sin esa opción MUST NOT crear una campaña.

#### Scenario: Noticia marcada
- **WHEN** una noticia pasa por primera vez a publicada con el aviso solicitado
- **THEN** el sistema encola una campaña para la lista confirmada

### Requirement: Campaña idempotente por contenido traducido
El sistema MUST crear como máximo una campaña por identidad editorial, incluso ante guardados repetidos, reintentos o versiones traducidas vinculadas.

#### Scenario: Traducciones disponibles
- **WHEN** una publicación marcada dispone de versiones EU y ES publicadas
- **THEN** una única campaña presenta ambos enlaces y los guardados posteriores no generan otra

#### Scenario: Traducción posterior
- **WHEN** la primera versión ya generó su campaña y después se publica la traducción
- **THEN** la traducción queda enlazada sin reenviar automáticamente el aviso

### Requirement: Mensaje identificable y revocable
Cada campaña MUST identificar a la asociación, describir el contenido sin inducir a error, ofrecer un enlace funcional de baja y evitar adjuntos o seguimiento adicional no aprobado.

#### Scenario: Baja desde un aviso
- **WHEN** una persona utiliza el enlace de baja
- **THEN** deja de recibir campañas futuras sin necesidad de iniciar sesión o contactar manualmente

### Requirement: Estado, reintentos y fallo seguro
El sistema MUST exponer al personal autorizado los estados no solicitado, en cola, enviando, enviado, fallido y cancelado, MUST evitar reintentos ilimitados y MUST conservar el identificador externo sin secretos ni direcciones.

#### Scenario: Proveedor no disponible
- **WHEN** el proveedor falla tres veces para la misma campaña
- **THEN** el envío queda fallido, se detienen los reintentos y aparece un aviso recuperable en WordPress

### Requirement: Capacidad gratuita observable
La operación MUST documentar los límites vigentes del servicio y MUST alertar antes de que el volumen proyectado deje de caber en el plan aprobado.

#### Scenario: Umbral preventivo
- **WHEN** el uso o la proyección alcanza el ochenta por ciento del límite mensual o de suscriptores
- **THEN** el escritorio advierte a la administradora sin cambiar silenciosamente de proveedor o contratar un plan
