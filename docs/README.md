Prueba para app.alegra.com
===================

Como parte de la evaluación técnica solicitada por el equipo de selección del software contable y de facturación para pymes Alegra, se desarrolló una funcionalidad que permite a traves de una interfaz web realizar distintas acciones sobre el módulo de contactos del software permitiendo consultar los contactos, listarlos, modificarlos o crearlos.


----------

Documentación
-------------

El proyecto fue realizado utilizando en el backend el framework PHP Zend en su versión 1.12 y en el frontend el framewrok JavaScript ExtJS en su versión 6.2. La estructura del proyecto es la siguiente:

 - **application:**  Aplicación Backend
  - **config:** Archivos de configuración
  - **controllers** Controladores
  - **layouts** Layout genérico para la aplicación
  - **models** Modelos y Mappers para conectarse con el servicio web de Alegra
  - **views** Vistas
 - **docs:** Documentación
 - **public:** Aplicación Frontend
  - **app:** Logica del negocio
  - **controller**
  - **model**
  - **store**
  - **view**
  - **css** archivos css de la app
  - **ext** Libreria Extjs
  - **sass** archivos sass para estilos de Extjs

----------


Puesta en marcha
-------------

 - Clonar el proyecto.
 - Instalar composer https://getcomposer.org/
 - Ejecutar composer composer install
 - Instalar Ext CMD
 - Ejecutar sencha app build
