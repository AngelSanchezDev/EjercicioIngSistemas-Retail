# Diagramas UML/BPMN en PlantUML

Fuente editable (`.puml`) de todos los diagramas del proyecto, con su PNG ya
renderizado junto a cada archivo. Estos son los diagramas "fuente de verdad"
en notación UML estándar; `docs/diagramas/` contiene una versión equivalente
en Mermaid, usada para la inserción rápida en el documento Word.

| Archivo | Diagrama |
|---|---|
| `01-casos-de-uso.puml` | Casos de uso (actores: Vendedor, Gerente, Admin, Canal online, Sistema) |
| `02-clases.puml` | Clases del dominio (Usuario, Tienda, Producto, Stock, Movimiento, Reposición, Alerta) |
| `03-secuencia-registrar-venta.puml` | Secuencia: registrar una venta a través del patrón MVC |
| `04-actividad-venta.puml` | Actividades: flujo completo de una venta y su efecto en el stock |
| `05-actividad-bpmn-reposicion.puml` | Proceso de negocio (BPMN) de aprobación de reposición, con carriles por rol |
| `06-estados-reposicion.puml` | Estados de una solicitud de reposición (pendiente → aprobada/rechazada → recibida) |
| `07-componentes-arquitectura.puml` | Componentes de la arquitectura MVC + endpoints de API |
| `08-despliegue.puml` | Despliegue en producción (tiendas, canal online, Render, base de datos) |
| `09-entidad-relacion.puml` | Entidad-relación de la base de datos `moda_express` |
| `10-secuencia-reposicion.puml` | Secuencia: flujo completo de aprobación y recepción de reposición |

## Cómo volver a renderizarlos

Requiere Java (JRE 11+). Se usó [PlantUML](https://plantuml.com/) 1.2024.8:

```bash
# Descargar plantuml.jar una vez (o usar tu instalación local / el plugin de VS Code)
java -jar plantuml.jar -tpng *.puml
```

También puedes pegar cualquier `.puml` en el editor online
[plantuml.com/plantuml](https://www.plantuml.com/plantuml/uml/) o usar la
extensión "PlantUML" de VS Code para previsualizarlos mientras los editas.
