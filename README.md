# ⚙️ Backend API – ERP Modular (Laravel)

Este proyecto es un sistema backend desarrollado con **Laravel**, diseñado para integrarse como el núcleo del ERP utilizado en el **Laboratorio de Fabricación Digital de la Universidad de Panamá**. Expone una serie de **APIs RESTful** para gestionar visitas, ventas, compras, inventario y reportes.

---

## 🚀 Tecnologías principales

- ✅ Laravel 10+
- 🔒 Laravel Sanctum
- 🧠 Eloquent ORM
- 🗃️ MySQL 
- 📊 Laravel Excel / Charts.js para reportes
- 🔐 Spatie Laravel-Permission para roles y permisos
- 🌐 API-first development

---

## 🔐 Autenticación

Soporte para autenticación de usuarios:

* Registro e inicio de sesión
* Middleware `auth:sanctum` o `auth:api`
* Protección de rutas sensibles
* Asignación de roles y permisos (admin, operador, supervisor)

---

## 📚 Módulos API disponibles

| Módulo        | Endpoint base    | Funcionalidades                  |
| ------------- | ---------------- | -------------------------------- |
| 🧾 Visitas    | `/api/visits`    | Registro, listado, filtros       |
| 💰 Ventas     | `/api/sales`     | Crear, listar, detalle           |
| 📥 Compras    | `/api/purchases` | Ingreso de pedidos, recepción    |
| 📦 Inventario | `/api/inventory` | Entradas, salidas, ajuste        |
| 📈 Reportes   | `/api/reports`   | Reportes estadísticos por fechas |
| 👤 Usuarios   | `/api/users`     | CRUD, roles, permisos            |

---

## 📅 Cron jobs y tareas programadas

Ejecuta tareas automáticas usando Laravel Scheduler:

```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

---

## 👤 Autor / Colaborador

Desarrollado por jagudo2514@gmail.com.
