# Aplicación ToDoList

Esta es una aplicación ToDoList construida con Laravel y Vue.js. La aplicación te permite gestionar tus tareas, crear nuevas, marcarlas como completadas y organizarlas de manera eficiente. La aplicación está completamente containerizada usando Docker para una fácil configuración y despliegue.

## Características

- Gestión de tareas (Agregar, Editar, Eliminar, Cambio de Estado)
- Autenticación y registro de usuarios
- Compartir tareas entre usuarios
- Notifiacion en tiempo real de tareas por vencer
- Construida con Laravel (Backend) y Vue.js (Frontend)

## Requisitos

Antes de comenzar, asegúrate de tener lo siguiente instalado:

- Docker
- Docker Compose

## Instalación y Configuración

### 1. Descargar la Imagen desde Docker Hub

Para ejecutar la aplicación localmente usando Docker, primero necesitas descargar la imagen desde Docker Hub. Abre tu terminal y ejecuta:

docker pull eac1381/todolist

### 2. Ejecutar la Aplicación con Docker Compose

Una vez descargada la imagen, puedes iniciar la aplicación usando Docker Compose. En tu terminal, navega hasta el directorio donde quieres ejecutar el proyecto y crea un archivo `docker-compose.yml` con el siguiente contenido:

version: '3.8'

services:
  app:
    image: eac1381/todolist2:latest
    container_name: todolist_app
    ports:
      - "8000:80"
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - DB_HOST=db
      - DB_PORT=3306
      - DB_DATABASE=todolist
      - DB_USERNAME=root
      - DB_PASSWORD=root
    depends_on:
      - db
    volumes:
      - .:/var/www/html

  db:
    image: mariadb:10.5
    container_name: todolist_db
    environment:
      MYSQL_DATABASE: todolist
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3306:3306"
    volumes:
      - dbdata:/var/lib/mysql
      - ./bd.sql:/docker-entrypoint-initdb.d/bd.sql
volumes:
  dbdata:

  ### 3. Ejecutar la Aplicación

Una vez que tengas listo el archivo `docker-compose.yml`, ejecuta el siguiente comando en el mismo directorio donde se encuentra el archivo:

docker-compose up

### 4. Acceder a la Aplicación

Cuando los contenedores estén en funcionamiento, podrás acceder a la aplicación ToDoList en tu navegador, usando la siguiente URL:

http://localhost:8000

### 5. Detener y Eliminar los Contenedores

Cuando hayas terminado de trabajar con la aplicación y quieras detener los contenedores, ejecuta el siguiente comando:

docker-compose down



## Decisiones Técnicas

### 1. **Control de Roles y Permisos**

Se implementaron las bibliotecas **Spatie** y **Sectum** para gestionar los roles, permisos y actividades de los usuarios. Esta decisión se tomó con el fin de garantizar un control granular sobre qué acciones pueden realizar los usuarios, brindando así un sistema más seguro y flexible.

- **Spatie** fue utilizado para la gestión de permisos y roles, permitiendo definir de manera clara y eficiente qué operaciones puede realizar cada usuario dentro de la aplicación.
- **Sectum** se integró para registrar y monitorear las actividades de los usuarios, lo que permite llevar un control detallado de las acciones realizadas por cada uno.

### 2. **Uso de Prefijos en los Endpoints**

Se decidió aplicar un prefijo a todos los endpoints de la API por razones de seguridad y organización. Esta práctica facilita la gestión de las rutas y proporciona una capa adicional de seguridad al prevenir conflictos y al permitir un mejor control sobre las rutas públicas y privadas.

### 3. **Uso de MariaDB como Base de Datos**

Para la base de datos, se optó por **MariaDB** debido a su estabilidad, rendimiento y compatibilidad con MySQL, lo que la convierte en una opción confiable para aplicaciones que requieren una base de datos relacional robusta.

- **MariaDB** es compatible con todas las funciones que ofrece MySQL, pero con mejoras en el rendimiento y la seguridad. 
- La base de datos se gestiona dentro de un contenedor Docker, lo que permite un entorno aislado y fácilmente reproducible.

### 4. **Arquitectura Modelo-Vista-Controlador (MVC)**

La aplicación está estructurada utilizando el patrón de diseño **Modelo-Vista-Controlador (MVC)**, que separa la lógica de la aplicación en tres componentes principales:

- **Modelo (Model)**: La capa que maneja la lógica de negocio y la interacción con la base de datos. En este caso, el modelo está diseñado para trabajar con MariaDB y proporciona las funciones necesarias para realizar operaciones sobre los datos.
- **Vista (View)**: La capa que se encarga de mostrar la información al usuario. En este proyecto, se utilizó **Vue.js** para crear interfaces de usuario interactivas y dinámicas.
- **Controlador (Controller)**: La capa intermediaria entre el modelo y la vista, que procesa las solicitudes del usuario y responde con la vista adecuada. Los controladores gestionan las peticiones y ejecutan las operaciones necesarias en el modelo, retornando la información correspondiente a la vista.
