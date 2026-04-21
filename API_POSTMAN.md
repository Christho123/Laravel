# API Documentation

## Base URL
`http://127.0.0.1:8000/api`

## Headers en Postman
Para todas las peticiones usa:
```http
Accept: application/json
Content-Type: application/json
```

Para las rutas protegidas agrega ademas:
```http
Authorization: Bearer TU_ACCESS_TOKEN
```

## Auth

### Register
`POST /auth/register`

Body JSON:
```json
{
  "name": "Juan Perez",
  "email": "juan@example.com",
  "password": "123456",
  "password_confirmation": "123456"
}
```

### Login
`POST /auth/login`

Body JSON:
```json
{
  "email": "juan@example.com",
  "password": "123456"
}
```

## Products > Categories
### Listar categorias
`GET /products/categories?page=1&page_size=10&search=ropa`

### Crear categoria
`POST /products/categories`

### Ver una categoria
`GET /products/categories/{id}`

### Actualizar categoria
`PUT /products/categories/{id}`

### Eliminar categoria
`DELETE /products/categories/{id}`

## Products > Brands
### Listar marcas
`GET /products/brands?page=1&page_size=10&search=nike`

### Crear marca
`POST /products/brands`

### Ver una marca
`GET /products/brands/{id}`

### Actualizar marca
`PUT /products/brands/{id}`

### Eliminar marca
`DELETE /products/brands/{id}`

## Products > Suppliers
### Listar proveedores
`GET /products/suppliers?page=1&page_size=10&search=proveedor`

### Crear proveedor
`POST /products/suppliers`

### Ver un proveedor
`GET /products/suppliers/{id}`

### Actualizar proveedor
`PUT /products/suppliers/{id}`

### Eliminar proveedor
`DELETE /products/suppliers/{id}`

## Products > Products
### Listar productos
`GET /products/products?page=1&page_size=10&search=camisa`

Respuesta exitosa:
```json
{
  "message": "Listado de productos obtenido correctamente.",
  "data": [
    {
      "id": 1,
      "name": "Camisa Classic",
      "description": "Camisa de algodon",
      "brand_id": 1,
      "category_id": 2,
      "price_purchase": "20.00",
      "price_sale": "30.68",
      "stock": 25,
      "created_at": "2026-04-20T10:15:00.000000Z",
      "updated_at": "2026-04-20T10:15:00.000000Z",
      "brand": {
        "id": 1,
        "name": "Nike"
      },
      "category": {
        "id": 2,
        "name": "Ropa"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "page_size": 10,
    "total": 1,
    "from": 1,
    "to": 1
  }
}
```

### Crear producto
`POST /products/products`

Body JSON:
```json
{
  "name": "Camisa Classic",
  "description": "Camisa de algodon",
  "brand_id": 1,
  "category_id": 2,
  "price_purchase": 20,
  "price_sale": 30.68,
  "stock": 0
}
```

### Ver un producto
`GET /products/products/{id}`

### Actualizar producto
`PUT /products/products/{id}`

### Eliminar producto
`DELETE /products/products/{id}`

## Inventory > Purchases
### Listar compras
`GET /inventory/purchases?page=1&page_size=10&search=proveedor`

### Registrar compra
`POST /inventory/purchases`

Body JSON:
```json
{
  "supplier_id": 1,
  "date": "2026-04-20",
  "items": [
    {
      "product_id": 1,
      "quantity": 10,
      "unit_price": 20
    }
  ]
}
```

### Ver compra
`GET /inventory/purchases/{id}`

## Inventory > Sales
### Listar ventas
`GET /inventory/sales?page=1&page_size=10&search=boleta`

### Registrar venta
`POST /inventory/sales`

Body JSON:
```json
{
  "date": "2026-04-20",
  "tipo_comprobante": "boleta",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "unit_price": 30.68
    }
  ]
}
```

### Ver venta
`GET /inventory/sales/{id}`

## Inventory > Stock Movements
### Listar movimientos de stock
`GET /inventory/stock-movements?page=1&page_size=10&search=entrada`

Nota:
- Los movimientos de stock se crean automaticamente al registrar compras y ventas.
- Esta tabla es solo de lectura desde la API.

## Flujo recomendado en Postman
1. Ejecuta `register` o `login`.
2. Copia el `access_token` que devuelve `login`.
3. En las rutas protegidas, agrega el header `Authorization: Bearer TU_ACCESS_TOKEN`.
4. Si el access expira, usa `POST /auth/refresh` con el `refresh_token`.
5. Para cerrar sesion, usa `POST /auth/logout` con ambos tokens.

## Nota
- `products` ya no usa `supplier_id`.
- Las compras y ventas no exponen CRUD completo para preservar el historial de stock.
- La API responde en JSON aunque falle la validacion.
