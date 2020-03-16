
# ComposedViews.

Sea usted bienvenido a la documentación en español de ComposedViews, un *framework* PHP para la creación de vistas redistribuíbles y compiladas a partir de objetos.

## Introducción.

Cuando se desarrolla una aplicación web dinámica resulta imprescindible llevar a cabo la implementación de las vistas. Por lo general, estas vistas se crean a partir de maquetas HTML copiando sus recursos en el directorio público del proyecto *backend* y dinamizando sus códigos HTML. Esto último implica la creación de las plantillas con la respectiva tecnología que en el proyecto se emplee para ello, y garantizando que los recursos sean correctamente referenciados desde las mismas.

Aunque realizar esta tarea no resulta algo complejo, en muchos casos puede resultar trabajoso y por tanto, implicará tiempos y esfuerzos considerables para los desarrolladores. Teniendo en cuenta además, que muchas maquetas se suelen emplear en más de una aplicación, se puede decir entonces que crear determinadas vistas puede resultar también una tarea repetitiva.

Con el objetivo de dar una solución a lo antes mencionado es que fue creado ComposedViews. Entre otras cosas, dicho framework propone crear proyectos de vistas que puedan ser reutilizadas en cualquier aplicación PHP con el menor esfuerzo posible.

Por lo antes mencionado, podemos decir que a la hora de trabajar con ComposedViews existirán dos roles principales. Por una parte, existirán
