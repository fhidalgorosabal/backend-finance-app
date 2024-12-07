# BackendFinanceApp

**BackendFinanceApp** es una API RESTful desarrollada en Laravel 9.0.0 que proporciona servicios para integrarse con la aplicación frontend (FinanceApp), facilita la sincronización y el manejo de datos financieros en tiempo real. Ofrece funcionalidades completas para la organización financiera, incluyendo la administración de conceptos, cuentas, monedas, bancos y recibos. Se utiliza **JWT** para la autenticación segura, implementado con los paquetes `laravel-sanctum` y `jwt-auth` y fue diseñada para utilizar PostgreSQL como sistema de base de datos.

---

## Requisitos previos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- [PHP](https://www.php.net/) >= 8.0
- [Composer](https://getcomposer.org/)
- [PostgreSQL](https://www.postgresql.org/)
- [Laravel CLI](https://laravel.com/docs/9.x)

---

## Instalación y configuración

1. Clona este repositorio `git clone https://github.com/tu-usuario/backend-finance-app.git`.
2. Accede a la carpeta del proyecto `cd backend-finance-app`.
3. Crea la BD en PostgreSQL y configura el archivo `.env`:
  - ### DB_CONNECTION=pgsql            
  - ### DB_HOST=127.0.0.1`
  - ### DB_PORT=5432`
  - ### DB_DATABASE=finance_db`
  - ### DB_USERNAME=username`
  - ### DB_PASSWORD=password`
4. Ejecuta `php artisan migrate` para crear las tablas necesarias mediante las migraciones.
5. Para iniciar la aplicación `php artisan serve` y accede en el navedador a `http://localhost:8000/`.
   
---

## Endpoints principales

### Autenticación
- **POST** `/register`: Registrar un nuevo usuario.
- **POST** `/login`: Iniciar sesión.
- **GET** `/profile`: Obtener la información del usuario autenticado.
- **POST** `/refresh`: Actualizar el token de autenticación.
- **POST** `/logout`: Cerrar sesión.

### Gestión de conceptos
- **GET** `/concept`: Listar todos los conceptos.
- **POST** `/concept`: Crear un nuevo concepto.
- **GET** `/concept/{id}`: Obtener los detalles de un concepto específico.
- **PUT** `/concept/{id}`: Actualizar un concepto existente.
- **DELETE** `/concept/{id}`: Eliminar un concepto.
- **POST** `/concept/list`: Obtener una lista filtrada de conceptos.

### Gestión de monedas
- **GET** `/currency`: Listar todas las monedas.
- **POST** `/currency`: Crear una nueva moneda.
- **GET** `/currency/{id}`: Obtener los detalles de una moneda específica.
- **PUT** `/currency/{id}`: Actualizar una moneda existente.
- **DELETE** `/currency/{id}`: Eliminar una moneda.
- **POST** `/currency/list`: Obtener una lista filtrada de monedas.
- **GET** `/currency/default-currency/{company_id}`: Obtener la moneda por defecto para una empresa específica.
- **POST** `/currency/default-currency`: Establecer una moneda como predeterminada.

### Gestión de recibos
- **GET** `/receipt`: Listar todos los recibos.
- **POST** `/receipt`: Crear un nuevo recibo.
- **GET** `/receipt/{id}`: Obtener los detalles de un recibo específico.
- **PUT** `/receipt/{id}`: Actualizar un recibo existente.
- **DELETE** `/receipt/{id}`: Eliminar un recibo.
- **POST** `/receipt/list`: Obtener una lista filtrada de recibos.

### Gestión de cuentas
- **GET** `/account`: Listar todas las cuentas.
- **POST** `/account`: Crear una nueva cuenta.
- **GET** `/account/{id}`: Obtener los detalles de una cuenta específica.
- **PUT** `/account/{id}`: Actualizar una cuenta existente.
- **DELETE** `/account/{id}`: Eliminar una cuenta.
- **POST** `/account/list`: Obtener una lista filtrada de cuentas.

### Gestión de bancos
- **GET** `/bank`: Listar todos los bancos.
- **POST** `/bank`: Crear un nuevo banco.
- **GET** `/bank/{id}`: Obtener los detalles de un banco específico.
- **PUT** `/bank/{id}`: Actualizar un banco existente.
- **DELETE** `/bank/{id}`: Eliminar un banco.

### Configuración
- **POST** `/setting`: Obtener la configuración actual.
- **POST** `/setting/change-month`: Cambiar el mes activo en el sistema.
- **POST** `/setting/close-year`: Cerrar el año financiero.

### Tablero de control (Dashboard)
- **POST** `/dashboard/get-month-total`: Obtener el total de ingresos y gastos del mes actual.
- **POST** `/dashboard/get-month-concepts`: Obtener los conceptos asociados al mes actual.
- **POST** `/dashboard/get-ingress-expenses-month`: Obtener ingresos y gastos por mes.

---

## Autor

Desarrollado por: Fernando Hidalgo Rosabal.

---

## Licencia

Este proyecto está licenciado bajo la [Licencia MIT](https://opensource.org/licenses/MIT).

---
