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
- Composer
- Node.js y npm

## Instalación y Configuración

### 1. Descargar el Proyecto desde GitHub

Para comenzar, clona el repositorio desde GitHub. Abre tu terminal y ejecuta el siguiente comando:


git clone https://github.com/earaos1381/ToDoList.git
cd ToDoList

### 2. Instalar Dependencias

Para instalar las dependencias necesarias para el proyecto, primero debes usar Composer para PHP y npm para Vue. Ejecuta los siguientes comandos en tu terminal, dentro del directorio del proyecto:

**Instalar dependencias de PHP:**

composer install

**Instalar dependencias de NODE:**

npm install

### 4. Configurar el Archivo `.env`

Una vez que hayas instalado todas las dependencias, debes configurar el archivo `.env` para que el proyecto funcione correctamente. Si no tienes un archivo `.env`, copia el archivo de ejemplo `.env.example`:

A continuación, abre el archivo `.env` y realiza las siguientes modificaciones:

5. **Configuración de la base de datos**:
   Asegúrate de que la base de datos esté configurada correctamente. Modifica los siguientes valores:

DB_CONNECTION=mysql DB_HOST=127.0.0.1 # O usa 'db' si usas Docker 
DB_PORT=3306 
DB_DATABASE=todolist 
DB_USERNAME=root 
DB_PASSWORD=root

- **DB_HOST**: Si estás ejecutando la base de datos localmente, usa `127.0.0.1`. Si estás usando Docker, asegúrate de usar el nombre del servicio del contenedor de la base de datos (en este caso, `db`).

6. **Configurar el Driver de Sesiones**:
Asegúrate de que el sistema de sesiones esté configurado para usar la base de datos. Cambia el valor de `SESSION_DRIVER` a `database`:

Esto permite que Laravel maneje las sesiones a través de la base de datos, lo que es útil si estás usando Docker para la gestión de sesiones en lugar de archivos locales.

Con estos cambios, tu archivo `.env` estará listo para usarse con la base de datos y el sistema de gestión de sesiones.

### 7. Ejecutar el Proyecto

Con el archivo `.env` configurado correctamente, ya puedes ejecutar el proyecto. Para hacerlo, sigue estos pasos:

usa los comandos de npm run dev, y posterior en otra terminal el php artisan serve para emular la plataforma en linea y poder entrar 
con la direccionde 127.0.0.1


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
