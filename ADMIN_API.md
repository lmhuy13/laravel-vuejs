# Admin API Documentation

## Authentication

All admin endpoints require authentication via Laravel Sanctum. Include the bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

## Authorization

All admin endpoints require the user to have either `admin` or `super-admin` role.

---

## User Management Endpoints

### List Users

**GET** `/api/admin/users`

Query parameters:
- `per_page` - Items per page (default: 15)
- `search` - Search by name or email

Response:
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "name": "Super Admin",
                "email": "superadmin@example.com",
                "team_id": null,
                "is_active": true,
                "created_at": "2025-01-21T...",
                "team": {...},
                "roles": [...],
                "profile": {...}
            }
        ],
        "current_page": 1,
        "total": 5,
        "per_page": 15
    }
}
```

### Get Single User

**GET** `/api/admin/users/{id}`

Response:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Super Admin",
        "email": "superadmin@example.com",
        "team": {...},
        "roles": [...],
        "profile": {...},
        "user_roles": [...]
    }
}
```

### Create User

**POST** `/api/admin/users`

Request body:
```json
{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "team_id": 1,
    "is_active": true,
    "profile": {
        "phone": "+1-555-0100",
        "bio": "Bio text",
        "city": "New York",
        "country": "USA"
    },
    "roles": [3, 4]
}
```

Response: (201 Created)
```json
{
    "success": true,
    "message": "User created successfully",
    "data": {...}
}
```

### Update User

**PUT** `/api/admin/users/{id}`

Request body (all fields optional):
```json
{
    "name": "Updated Name",
    "email": "newemail@example.com",
    "team_id": 2,
    "is_active": false,
    "password": "newpassword123",
    "profile": {
        "phone": "+1-555-0101",
        "bio": "Updated bio"
    },
    "roles": [3]
}
```

Response:
```json
{
    "success": true,
    "message": "User updated successfully",
    "data": {...}
}
```

### Soft Delete User

**DELETE** `/api/admin/users/{id}`

Response:
```json
{
    "success": true,
    "message": "User deleted successfully"
}
```

### Restore Deleted User

**POST** `/api/admin/users/{id}/restore`

Response:
```json
{
    "success": true,
    "message": "User restored successfully",
    "data": {...}
}
```

### Force Delete User

**DELETE** `/api/admin/users/{id}/force`

Response:
```json
{
    "success": true,
    "message": "User permanently deleted"
}
```

### Activate User

**POST** `/api/admin/users/{id}/activate`

Response:
```json
{
    "success": true,
    "message": "User activated",
    "data": {...}
}
```

### Deactivate User

**POST** `/api/admin/users/{id}/deactivate`

Response:
```json
{
    "success": true,
    "message": "User deactivated",
    "data": {...}
}
```

---

## Team Management Endpoints

### List Teams

**GET** `/api/admin/teams`

Query parameters:
- `per_page` - Items per page (default: 15)
- `search` - Search by name or slug

Response:
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "name": "Default Team",
                "slug": "default-team",
                "description": "Default team",
                "logo": null,
                "is_active": true,
                "created_at": "2025-01-21T...",
                "users_count": 5
            }
        ],
        "current_page": 1,
        "total": 1,
        "per_page": 15
    }
}
```

### Get Single Team

**GET** `/api/admin/teams/{id}`

Response:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Default Team",
        "slug": "default-team",
        "description": "Default team",
        "logo": null,
        "is_active": true,
        "users": [...],
        "roles": [...]
    }
}
```

### Create Team

**POST** `/api/admin/teams`

Request body:
```json
{
    "name": "New Team",
    "slug": "new-team",
    "description": "Team description",
    "logo": "https://example.com/logo.png",
    "is_active": true
}
```

Response: (201 Created)
```json
{
    "success": true,
    "message": "Team created successfully",
    "data": {...}
}
```

### Update Team

**PUT** `/api/admin/teams/{id}`

Request body (all fields optional):
```json
{
    "name": "Updated Team Name",
    "slug": "updated-team",
    "description": "Updated description",
    "logo": "https://example.com/new-logo.png",
    "is_active": false
}
```

Response:
```json
{
    "success": true,
    "message": "Team updated successfully",
    "data": {...}
}
```

### Soft Delete Team

**DELETE** `/api/admin/teams/{id}`

Note: Cannot delete team with active users.

Response:
```json
{
    "success": true,
    "message": "Team deleted successfully"
}
```

### Restore Deleted Team

**DELETE** `/api/admin/teams/{id}/restore`

Response:
```json
{
    "success": true,
    "message": "Team restored successfully",
    "data": {...}
}
```

### Force Delete Team

**DELETE** `/api/admin/teams/{id}/force`

Response:
```json
{
    "success": true,
    "message": "Team permanently deleted"
}
```

### Get Team Members

**GET** `/api/admin/teams/{id}/members`

Response:
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "name": "Admin User",
                "email": "admin@example.com",
                "profile": {...},
                "roles": [...]
            }
        ],
        "current_page": 1,
        "total": 5,
        "per_page": 15
    }
}
```

### Add Member to Team

**POST** `/api/admin/teams/{id}/members`

Request body:
```json
{
    "user_id": 2,
    "roles": [3, 4]
}
```

Response:
```json
{
    "success": true,
    "message": "Member added to team"
}
```

### Remove Member from Team

**DELETE** `/api/admin/teams/{teamId}/members/{userId}`

Response:
```json
{
    "success": true,
    "message": "Member removed from team"
}
```

### Activate Team

**POST** `/api/admin/teams/{id}/activate`

Response:
```json
{
    "success": true,
    "message": "Team activated",
    "data": {...}
}
```

### Deactivate Team

**POST** `/api/admin/teams/{id}/deactivate`

Response:
```json
{
    "success": true,
    "message": "Team deactivated",
    "data": {...}
}
```

---

## Error Responses

### Unauthorized (401)
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### Forbidden (403)
```json
{
    "success": false,
    "message": "Forbidden: Admin access required"
}
```

### Validation Error (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email must be a valid email address."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Not Found"
}
```

---

## Usage Examples

### Login and Get Token

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@example.com",
    "password": "password"
  }'
```

### List Users

```bash
curl -X GET http://localhost:8000/api/admin/users \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Team

```bash
curl -X POST http://localhost:8000/api/admin/teams \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Marketing Team",
    "slug": "marketing-team",
    "description": "Marketing department"
  }'
```

### Update User

```bash
curl -X PUT http://localhost:8000/api/admin/users/5 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Name",
    "is_active": true
  }'
```

---

## Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid request
- `401 Unauthorized` - Missing or invalid token
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed
- `500 Internal Server Error` - Server error
