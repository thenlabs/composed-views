
# Generación manual del proyecto.

Ejecute los siguientes comandos en el orden mostrado.

>Sustituya <directory> por el nombre que desea darle al directorio de su proyecto.

    $ composer create-project thenlabs/kit-template <directory> dev-master --no-scripts

En determinado momento [Composer][Composer] le preguntará si desea eliminar el repositorio actual. Recomendamos que inique sí ya que no tiene ningún sentido que su proyecto contenga esos *commits*. Este paso será automatizado en futuras versiones.

    $ cd <directory>
    $composer init --type=then-package --stability=dev --require=thenlabs/composed-views:dev-master --require-dev=thenlabs/cli:dev-master

Se le preguntará sobre ciertos datos del proyecto donde podrá especificar los valores que desee **excepto en el tipo y las dependencias donde deberá mantener los valores por defecto**.

>En nuestro caso especificaremos `thenlabs/demo-composed-adminlte` como nombre del proyecto.

    $ composer update -vvv

Por último recomendamos que elimine o edite el archivo `README.md`.

[Composer]: https://getcomposer.org/
