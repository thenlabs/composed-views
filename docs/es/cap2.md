
# Capítulo 2. Creando un proyecto ComposedViews.

En el presente capítulo abordaremos de forma práctica la forma de crear un proyecto ComposedViews a partir de la popular plantilla de administración [AdminLTE](https://adminlte.io/). Hemos decidido escoger dicha plantilla dado que entre otras bondades la misma ofrece una [página básica](https://adminlte.io/themes/AdminLTE/starter.html) que nos servirá para mostrar de forma clara los conceptos que se deben tener en cuenta a la hora de trabajar con ComposedViews.

Una vez finalizado dicho capítulo habremos construido un paquete Composer capaz de ser instalado en cualquier aplicación PHP.

>En [este enlace](#) se puede encontrar el proyecto realizado en este capítulo.

## 1. Creando el proyecto.

Para crear un proyecto se debe ejecutar el siguiente comando:

    $ composer create-project thenlabs/kit-template composed-admin-lte dev-master

>Debe sustituir `composed-admin-lte` por el nombre del directorio donde creará su proyecto.

>Para finalizar la instalación, Composer le preguntará si desea eliminar el repositorio actual. Recomendamos que inique sí ya que no tiene ningún objetivo que su proyecto contenga esos *commits*. En futuras versiones, este paso será automatizado.

## 2. Conociendo la estructura del proyecto.

Una vez que la instalación ha finalizado podremos encontrar la siguiente estructura de archivos dentro del directorio del proyecto. Seguidamente explicaremos los elementos principales.

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

Primeramente comenzaremos comentando el significado del archivo `then-package.json` ya que el mismo contendrá información importante
