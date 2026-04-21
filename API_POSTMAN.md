# API Documentation

## Base URL
`http://127.0.0.1:8000/api`

## Headers en Postman
Para todas las peticiones usa:
```http
Accept: application/json
Content-Type: application/json
```

Para las rutas protegidas agrega además:
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

Respuesta exitosa:
```json
{
  "message": "Usuario registrado correctamente.",
  "data": {
    "id": 1,
    "name": "Juan Perez",
    "email": "juan@example.com",
    "created_at": "2026-04-20T10:15:00.000000Z",
    "updated_at": "2026-04-20T10:15:00.000000Z"
  }
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

Respuesta exitosa:
```json
{
  "message": "Inicio de sesión exitoso.",
  "data": {
    "id": 1,
    "name": "Juan Perez",
    "email": "juan@example.com",
    "token_type": "Bearer",
    "access_token": "TU_ACCESS_TOKEN",
    "refresh_token": "TU_REFRESH_TOKEN"
  }
}
```

### Refresh
`POST /auth/refresh`

Body JSON:
```json
{
  "refresh_token": "TU_REFRESH_TOKEN"
}
```

### Logout
`POST /auth/logout`

Headers:
```http
Authorization: Bearer TU_ACCESS_TOKEN
```

Body JSON:
```json
{
  "refresh_token": "TU_REFRESH_TOKEN"
}
```

## Products > Categories
Todas estas rutas requieren `Authorization: Bearer TU_ACCESS_TOKEN`.

### Listar categorias
`GET /products/categories?page=1&page_size=10&search=ropa`

Query params:
- `page`: número de página, por ejemplo `1`, `2`, `3`
- `page_size`: `10`, `20` o `50`
- `search`: busca por nombre

Respuesta exitosa:
```json
{
  "message": "Listado de categorias obtenido correctamente.",
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "page_size": 10,
    "total": 0,
    "from": null,
    "to": null
  }
}
```

### Crear categoria
`POST /products/categories`

Body JSON:
```json
{
  "name": "Tecnologia",
  "description": "Productos electronicos"
}
```

### Ver una categoria
`GET /products/categories/{id}`

### Actualizar categoria
`PUT /products/categories/{id}`

Body JSON:
```json
{
  "name": "Tecnologia",
  "description": "Productos electronicos y accesorios"
}
```

### Eliminar categoria
`DELETE /products/categories/{id}`

## Products > Brands
Todas las rutas usan `Authorization: Bearer TU_ACCESS_TOKEN`.

### Listar marcas
`GET /products/brands?page=1&page_size=10&search=nike`

### Crear marca
`POST /products/brands`

Body JSON:
```json
{
  "name": "Nike",
  "description": "Marca deportiva"
}
```

### Ver una marca
`GET /products/brands/{id}`

### Actualizar marca
`PUT /products/brands/{id}`

Body JSON:
```json
{
  "name": "Nike",
  "description": "Marca deportiva oficial"
}
```

### Eliminar marca
`DELETE /products/brands/{id}`

## Products > Suppliers
Todas las rutas usan `Authorization: Bearer TU_ACCESS_TOKEN`.

### Listar proveedores
`GET /products/suppliers?page=1&page_size=10&search=proveedor`

### Crear proveedor
`POST /products/suppliers`

Body JSON:
```json
{
  "name": "Proveedor Central",
  "email": "proveedor@example.com",
  "phone": "999999999",
  "address": "Av. Principal 123"
}
```

### Ver un proveedor
`GET /products/suppliers/{id}`

### Actualizar proveedor
`PUT /products/suppliers/{id}`

Body JSON:
```json
{
  "name": "Proveedor Central",
  "email": "proveedor@example.com",
  "phone": "999999998",
  "address": "Av. Principal 456"
}
```

### Eliminar proveedor
`DELETE /products/suppliers/{id}`

## Flujo recomendado en Postman
1. Ejecuta `register` o `login`.
2. Copia el `access_token` que devuelve `login`.
3. En las rutas protegidas, agrega el header `Authorization: Bearer TU_ACCESS_TOKEN`.
4. Si el access expira, usa `POST /auth/refresh` con el `refresh_token`.
5. Para cerrar sesión, usa `POST /auth/logout` con ambos tokens.

## Nota
- `register` solo devuelve datos del usuario creado.
- `login` es el único endpoint que devuelve tokens.
- La API responde en JSON aunque falle la validación.