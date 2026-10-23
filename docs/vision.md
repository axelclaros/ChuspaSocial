# Visión de ChuspaSocial

## Objetivo
ChuspaSocial es un plugin diseñado para Moodle 4.5 con el objetivo de enriquecer la interacción académica mediante un entorno social integrado. Permite a los estudiantes visualizar un muro social específico para cada materia, facilitando la comunicación y el intercambio de recursos. Además, incorpora un sistema de noticias categorizadas por etiquetas (tags) para mantener a la comunidad informada de manera eficiente. Por último, cuenta con un marketplace de libros para fomentar la compra y venta de textos académicos entre los usuarios de la plataforma.

## Problema que resuelve
Actualmente, Moodle no ofrece de forma nativa un espacio social enfocado por materia ni un canal institucional dedicado para que los estudiantes puedan intercambiar o reutilizar libros y materiales académicos.

Esta limitación genera las siguientes consecuencias en la comunidad educativa:

* **Fragmentación de la comunicación:** Los estudiantes se ven obligados a crear grupos externos no oficiales en plataformas como WhatsApp, Telegram o Facebook para interactuar sobre el contenido de cada asignatura.
* **Información dispersa y no verificada:** Los recursos, dudas y avisos quedan aislados en chats privados, dificultando que el conocimiento sea accesible de manera centralizada para todos los compañeros del curso.
* **Falta de un canal seguro para intercambio de materiales:** No existe una vía transparente para ofrecer, buscar o intercambiar libros e insumos académicos entre estudiantes de una misma institución.

`ChuspaSocial` aborda estas deficiencias integrando un entorno social e institucional dentro del propio Moodle, centralizando la interacción académica y facilitando el acceso equitativo a recursos educativos.

## Usuarios
- **Estudiante:** Accede al muro social de su materia, visualiza noticias por tags y participa en el marketplace de libros.
- **Docente:** Publica anuncios y gestiona el contenido del muro social de las materias que imparte.
- **Administrador:** Configura el plugin, gestiona usuarios y mantiene el orden de la plataforma.

## Alcance

## Fuera de alcance

## Métricas de éxito

Para saber si ChuspaSocial cumple su objetivo, se medirán los siguientes indicadores con datos que ya registran Moodle y las tablas del plugin (`local_chuspasocial_*`):

| Métrica | Cómo se mide | Meta inicial |
| --- | --- | --- |
| Publicaciones respondidas | % de publicaciones de tipo `post` que reciben al menos un comentario dentro de las 24 horas siguientes (tablas `post` y `comment`) | ≥ 50 % |
| Participación docente | % de cursos cuyo docente publica al menos un anuncio (`announcement`) al mes (tabla `post`) | ≥ 50 % |
| Retención de estudiantes | % de estudiantes que publican o comentan en una semana y vuelven a participar en alguna de las 3 semanas siguientes (tablas `post` y `comment`) | ≥ 50 % |
| Oferta de libros | N.º de anuncios nuevos en el marketplace por mes (tabla `listing`) | ≥ 20 por mes |
| Atención de reportes | % de reportes resueltos o descartados dentro de las 48 horas posteriores a su creación (tabla `report`, campos `timecreated` y `timeresolved`) | ≥ 90 % |

Las metas son valores iniciales de referencia. Se revisarán y ajustarán después del primer mes de uso real en Moodle.
