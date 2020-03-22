
# Capítulo 1. Creando un nuevo proyecto.

En el presente capítulo abordaremos de forma práctica la manera de crear un proyecto ComposedViews basado de la popular plantilla de administración [AdminLTE](https://adminlte.io/). Hemos decidido escoger dicha plantilla dado que entre otras bondades la misma ofrece una [página básica](https://adminlte.io/themes/AdminLTE/starter.html) que nos servirá para mostrar de forma clara los conceptos que se deben tener en cuenta sobre ComposedViews.

Una vez finalizado dicho capítulo habremos construido un proyecto PHP instalable por [Composer][Composer] el cual contendrá clases cuyas instancias serán capaces de generar los códigos HTML de la página y sus componentes. Además de ello, el proyecto contendrá los recursos de la maqueta los cuales estarán correctamente referenciados desde el HTML generado y además podrán ser instalados en cualquier aplicación PHP con solo ejecutar un comando.

>En [este enlace](#) se puede encontrar el proyecto realizado en el capítulo.

## 1. Consideraciones sobre los proyectos.

Un proyecto ComposedViews es un paquete [Composer][Composer] de tipo `then-package`. A los paquetes de este tipo se les define como *then packages* y se gestionan además con la herramienta [ThenLabs CLI](https://github.com/thenlabs/cli).

Es muy recomendable que lea la documentación de esta herramienta dado que en la misma se define más profundamente lo que es un *then package* entre otros conceptos relacionados los cuales necesitará conocer para la comprensión de esta guía.

## 2. Creando el nuevo proyecto.

Para crear un nuevo proyecto se debe ejecutar el siguiente comando:

    $ composer create-project thenlabs/kit-template composed-admin-lte dev-master

>Debe sustituir `composed-admin-lte` por el nombre del directorio donde desea crear su proyecto.

>Para finalizar la instalación, [Composer][Composer] le preguntará si desea eliminar el repositorio actual. Recomendamos que inique sí ya que no tiene ningún objetivo que su proyecto contenga esos *commits*. En futuras versiones, este paso será automatizado.

## 3. Conociendo la estructura del proyecto.

Una vez que la instalación ha finalizado podremos encontrar la siguiente estructura de archivos dentro del directorio del proyecto.

```
├── assets/
├── composer.json
├── composer.lock
├── examples/
│   ├── index.php
│   ├── pages/
│   │   └── main.php
│   └── then.json
├── README.md
├── serve
├── src/
├── tests/
└── then-package.json
```

[Composer]: https://getcomposer.org/
