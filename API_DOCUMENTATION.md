# API Documentation Guide

## Swagger UI

Tài liệu API của bạn hiện đã có sẵn tại: `http://localhost:8000/api/docs`

### Cách truy cập

1. **Chạy Laravel dev server:**
   ```bash
   php artisan serve
   ```

2. **Truy cập Swagger UI:**
   ```
   http://localhost:8000/api/docs
   ```

### Tính năng

- ✅ **Interactive API Testing** - Test trực tiếp từ giao diện
- ✅ **Authentication** - Hỗ trợ Bearer token (Sanctum)
- ✅ **Request/Response Examples** - Ví dụ đầu vào/ra
- ✅ **Parameter Documentation** - Tài liệu các tham số
- ✅ **Error Handling** - Tài liệu các lỗi có thể xảy ra

## API Endpoints

### Authentication

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/login` | Đăng nhập | ❌ |
| GET | `/api/me` | Lấy thông tin user hiện tại | ✅ |
| POST | `/api/logout` | Đăng xuất | ✅ |

### Users (Admin)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/admin/users` | Danh sách users (phân trang, tìm kiếm, lọc) | ✅ |
| POST | `/api/admin/users` | Tạo user mới | ✅ |
| GET | `/api/admin/users/{id}` | Chi tiết user | ✅ |
| PUT | `/api/admin/users/{id}` | Cập nhật user | ✅ |
| DELETE | `/api/admin/users/{id}` | Xoá mềm user | ✅ |
| POST | `/api/admin/users/{id}/activate` | Kích hoạt user | ✅ |
| POST | `/api/admin/users/{id}/deactivate` | Vô hiệu hóa user | ✅ |
| POST | `/api/admin/users/{id}/restore` | Khôi phục user bị xoá | ✅ |
| DELETE | `/api/admin/users/{id}/force` | Xoá vĩnh viễn user | ✅ |

### Teams (Admin)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/admin/teams` | Danh sách teams | ✅ |
| POST | `/api/admin/teams` | Tạo team mới | ✅ |
| GET | `/api/admin/teams/{id}` | Chi tiết team | ✅ |
| PUT | `/api/admin/teams/{id}` | Cập nhật team | ✅ |
| DELETE | `/api/admin/teams/{id}` | Xoá mềm team | ✅ |
| GET | `/api/admin/teams/{id}/members` | Danh sách thành viên | ✅ |
| POST | `/api/admin/teams/{id}/members` | Thêm thành viên | ✅ |
| DELETE | `/api/admin/teams/{teamId}/members/{userId}` | Xóa thành viên | ✅ |
| POST | `/api/admin/teams/{id}/activate` | Kích hoạt team | ✅ |
| POST | `/api/admin/teams/{id}/deactivate` | Vô hiệu hóa team | ✅ |

### Roles (Admin)

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/api/admin/roles` | Danh sách roles | ✅ |
| POST | `/api/admin/roles` | Tạo role mới | ✅ |
| GET | `/api/admin/roles/{id}` | Chi tiết role | ✅ |
| PUT | `/api/admin/roles/{id}` | Cập nhật role | ✅ |
| DELETE | `/api/admin/roles/{id}` | Xoá mềm role | ✅ |
| DELETE | `/api/admin/roles/{id}/force` | Xoá vĩnh viễn role | ✅ |

## Authentication

### Lấy Token

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

Response:
```json
{
  "message": "Login successful",
  "token": "your_sanctum_token",
  "user": { ... }
}
```

### Sử dụng Token

Thêm header `Authorization: Bearer {token}` vào request:

```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer your_sanctum_token" \
  -H "Accept: application/json"
```

## Query Parameters

### Pagination
- `page` - Trang (default: 1)
- `per_page` - Số bản ghi/trang (max: 100, default: 15)

### Search & Filter
- `search` - Tìm kiếm theo tên, email, slug (tuỳ endpoint)
- `sort_by` - Sắp xếp theo trường (name, email, created_at, v.v.)
- `sort_dir` - Hướng sắp xếp (asc, desc, default: asc)

### User Filters
- `team_id` - Lọc theo team
- `role_ids` - Lọc theo roles (cách nhau bằng dấu phẩy)

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "message": "Unauthorized action"
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

### 422 Unprocessable Entity
```json
{
  "message": "The given data was invalid",
  "errors": {
    "email": ["The email field is required"]
  }
}
```

## Request Examples

### Create User
```bash
curl -X POST http://localhost:8000/api/admin/users \
  -H "Authorization: Bearer token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "team_id": 1,
    "status": "active"
  }'
```

### Get Users with Filters
```bash
curl -X GET "http://localhost:8000/api/admin/users?page=1&search=john&sort_by=name&sort_dir=asc" \
  -H "Authorization: Bearer token"
```

### Create Role
```bash
curl -X POST http://localhost:8000/api/admin/roles \
  -H "Authorization: Bearer token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Editor",
    "slug": "editor",
    "description": "Editor role",
    "is_active": true
  }'
```

## Testing

### Postman Collection

1. Mở Swagger UI
2. Scroll xuống dưới cùng
3. Click "Download" để tải Postman collection

### Thunder Client (VS Code Extension)

Import URL: `http://localhost:8000/api/openapi.json`

## Troubleshooting

### Token Invalid
- Đảm bảo token được lấy sau khi đăng nhập
- Token có hạn, nếu hết hạn hãy đăng nhập lại

### CORS Issues
- Kiểm tra `.env` xem `APP_URL` có đúng không
- Xem `config/cors.php` để cấu hình CORS

### Permission Denied
- Đảm bảo user có role `admin`
- Kiểm tra middleware `admin` trong `app/Http/Middleware`

## Cập nhật Documentation

Khi thêm API mới:

1. Thêm endpoint vào `routes/api.php`
2. Thêm OpenAPI annotation vào controller hoặc tạo documentation class
3. Cập nhật `SwaggerController::getPaths()` nếu dùng dynamic
4. Refresh Swagger UI

## Resources

- [OpenAPI Specification](https://spec.openapis.org/oas/v3.0.0)
- [Swagger UI Docs](https://github.com/swagger-api/swagger-ui)
- [Laravel API Best Practices](https://laravel.com/docs/11.x/eloquent-api-resources)
