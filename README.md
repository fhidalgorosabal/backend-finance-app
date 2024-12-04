<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# Backend Finance App

**Backend Finance App** es una API RESTful desarrollada en Laravel 9.0.0. Proporciona servicios para la gestión de ingresos y gastos personales o empresariales, soportando autenticación basada en **JWT** mediante `laravel-sanctum` y `jwt-auth`.

---

## Características principales

- **Autenticación segura**: Implementación de JWT para proteger los endpoints.
- **Gestión financiera**:
  - CRUD de conceptos.
  - CRUD de ingresos y gastos.
  - CRUD de bancos y cuentas bancarias.
  - CRUD de monedas y categorías.
- **Endpoints RESTful**: Organización y acceso claro a través de recursos API.
- **Migraciones**: Base de datos fácil de gestionar mediante migraciones.
- **Protección de rutas**: Rutas seguras usando middleware de autenticación.

---

## Requisitos previos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- [PHP](https://www.php.net/) >= 8.0
- [Composer](https://getcomposer.org/)
- [PostgreSQL](https://www.postgresql.org/)
- [Laravel CLI](https://laravel.com/docs/9.x)

---

## Instalación y configuración

1. Clona este repositorio:
   ```bash
   git clone https://github.com/tu-usuario/backend-finance-app.git
   cd backend-finance-app
   php artisan migrate
   php artisan serve
   http://localhost:8000/
   
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