# ModaExpress — Sistema de Gestión de Inventarios (Trabajo Final)

Sistema web de control de inventario centralizado para una cadena de retail
(5 tiendas físicas + canal online), desarrollado como trabajo final del curso
de Ingeniería de Software (SENATI). Resuelve el caso práctico ModaExpress:
elimina el desfase de 48h entre stock real y registrado, genera alertas
automáticas por umbrales de stock y centraliza ventas de tienda física +
canal online en una sola base de datos.

## Stack

- **PHP 8 (MVC puro, sin framework)** — front controller en `public/index.php`
- **MySQL 8 / MariaDB** vía PDO con consultas preparadas
- **Chart.js** para reportes analíticos
- Patrón **"smart polling"** para simular tiempo real sin recargar todo el
  dashboard cada segundo (ver sección *Tiempo real* abajo)

## Estructura

```
app/
  core/         Router, Database (PDO), Auth, View
  models/       Usuario, Tienda, Producto, Movimiento, Alerta, Reposicion, Reporte
  controllers/  Un controller por recurso (Auth, Dashboard, Producto, Movimiento, Alerta, Reposicion, Reporte)
  views/        Vistas PHP + layout principal (sidebar/topbar)
public/
  index.php     Front controller (toda petición pasa por aquí: index.php?r=controller/accion)
  api/          Endpoints JSON livianos (version, dashboard_resumen, venta_online)
  assets/       CSS
database/
  schema.sql    DDL completo
  seed.sql      Datos de ejemplo (tiendas, productos, stock, movimientos)
  seed.php      Crea usuarios de prueba con password_hash() real
docs/
  diagramas/    UML y BPMN del proyecto
  capturas/     Capturas de pantalla del sistema funcionando
```

## Cómo correrlo en local

1. Levanta MySQL (XAMPP, Docker, o local) y crea la base:
   ```
   mysql -u root -p < database/schema.sql
   mysql -u root -p moda_express < database/seed.sql
   ```
2. Configura las variables de entorno (o copia `.env.example` y expórtalas):
   `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.
3. Crea los usuarios de prueba (usa `password_hash()` real de PHP):
   ```
   php database/seed.php
   ```
4. Levanta el servidor embebido de PHP:
   ```
   php -S localhost:8000 -t public
   ```
5. Abre `http://localhost:8000` y entra con:
   - `admin@modaexpress.com` / `123456` (rol admin)
   - `gerente.sanisidro@modaexpress.com` / `123456` (rol gerente)
   - `vendedor.sanisidro@modaexpress.com` / `123456` (rol vendedor)

## Simular una venta del canal online

```
curl -X POST http://localhost:8000/api/venta_online.php \
     -d "sku=7750001000011&cantidad=2&referencia=WEB-00099"
```

El dashboard la refleja automáticamente (sin recargar la página) en los
siguientes 5 segundos.

## "Tiempo real" sin gastar recursos (smart polling)

Hacer que 5 tiendas + canal online consulten el estado del stock cada
segundo sería costoso y no se justifica para esta escala. En vez de eso:

1. El navegador pregunta cada **5 segundos** solo un número: el ID del
   último movimiento (`api/version.php`, respuesta de pocos bytes).
2. Solo si ese número cambió, se pide el detalle completo
   (`api/dashboard_resumen.php`).
3. Si la pestaña no está visible (`document.visibilityState`), deja de
   preguntar por completo.

Esto da una sensación de actualización instantánea con un costo marginal.
La ruta de evolución natural si la empresa duplica tiendas es migrar este
polling a **Server-Sent Events o WebSockets** (push en vez de pull) — se
documenta como mejora futura en la propuesta.

## Despliegue (Render)

La app se despliega como **un solo Web Service** (no hay separación
frontend/backend: en PHP MVC clásico el servidor PHP genera el HTML). Se usa
el `Dockerfile` de la raíz. La base de datos MySQL va en un proveedor externo
gratuito (Aiven, TiDB Serverless, etc.) y sus credenciales se configuran como
variables de entorno en Render (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`,
`DB_PASS`).

## Capturas

Ver `docs/capturas/`.
