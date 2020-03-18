
# ComposedViews.

Sea usted bienvenido a la documentación en español de ComposedViews, un *framework* PHP para la creación de vistas redistribuíbles y compiladas a partir de objetos.

## Introducción.

Cuando se desarrolla una aplicación web dinámica resulta imprescindible llevar a cabo la implementación de la vista. Por lo general, las vistas se crean a partir de maquetas HTML copiando sus recursos en el directorio público del proyecto *backend* y dinamizando sus códigos HTML. Esto último implica la creación de las plantillas con la respectiva tecnología que en el proyecto se emplee para ello, y garantizando que los recursos sean correctamente referenciados desde las mismas.

Aunque realizar esta tarea no resulta algo complicado, en muchos casos puede resultar trabajoso y por tanto, implicará tiempos y esfuerzos considerables para los desarrolladores. Teniendo en cuenta además, que muchas maquetas se suelen emplear en más de una aplicación, se puede decir entonces que crear determinadas vistas puede resultar también una tarea repetitiva.

Con el objetivo de dar una solución a lo antes mencionado es que fue creado ComposedViews. Entre otras cosas, dicho *framework* propone crear vistas independientes y reutilizables que puedan ser instaladas en cualquier aplicación PHP y que se puedan usar con el menor esfuerzo posible.

Un proyecto ComposedViews es a fin de cuentas un paquete [Composer](https://getcomposer.org) cuyo tipo es `then-package` entre otras personalizaciones y funcionalidades que más adelante comentaremos. De esta forma es que se garantiza que las vistas puedan ser reutilizadas entre diferentes aplicaciones ya que además, por cada vista del proyecto existirá una clase PHP diseñada de tal manera que sus instancias sean capaces de producir fragmentos de código HTML con solo especificarles los datos más relevantes.

Por lo antes mencionado, se puede decir que ComposedViews se usa en dos situaciones principales. Por una parte se empleará a la hora de crear un `then-package`, mientras que la otra será cuando se vaya a usar uno de ellos.

