# Complete Laravel + Vue Admin System - Integration Guide

## Project Overview

A complete admin dashboard system with:
- **Backend:** Laravel 11 with Sanctum authentication
- **Frontend:** Vue 3 SPA with Router and Tailwind CSS
- **Features:** User/Team management, role-based access control, authentication

## Step-by-Step Setup

### 1. Database Setup

Create migrations for the following tables:
- `users` - User accounts
- `teams` - Team records
- `roles` - Role definitions (admin, super-admin, user)
- `user_roles` - User to role mapping
- `team_members` - Team membership

### 2. Seed Demo Data

```bash
php artisan db:seed
```

Create demo users:
```php
$user = User::create([
    'name' => 'Demo Admin',
    'email' => 'demo@example.com',
    'password' => bcrypt('password123'),
]);

$admin = Role::where('slug', 'admin')->first();
$user->roles()->attach($admin->id);
```

### 3. Install Frontend Dependencies

```bash
npm install
```

### 4. Build Assets

```bash
npm run build
```

For development with watch:
```bash
npm run dev
```

### 5. Configure Environment

Update `.env`:
```
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173
```

### 6. Run Development Server

Terminal 1 - Laravel:
```bash
php artisan serve
```

Terminal 2 - Vite (for hot reload):
```bash
npm run dev
```

---

## API Endpoint Reference

### Authentication Endpoints

**POST /api/login**
```json
Request: {
  "email": "demo@example.com",
  "password": "password123"
}

Response: {
  "data": {
    "token": "1|abc123xyz...",
    "user": {
      "id": 1,
      "name": "Demo Admin",
      "email": "demo@example.com",
      "roles": ["admin"]
    }
  }
}
```

**POST /api/logout** (requires token)
```json
Response: {
  "message": "Logged out successfully."
}
```

**GET /api/me** (requires token)
```json
Response: {
  "data": {
    "id": 1,
    "name": "Demo Admin",
    "email": "demo@example.com",
    "roles": ["admin"]
  }
}
```

### User Management Endpoints (requires admin role)

**GET /api/admin/users?page=1&search=query**
```json
Response: {
  "data": {
    "data": [
      {
        "id": 1,
        "name": "User Name",
        "email": "user@example.com",
        "team_id": 1,
        "team": { "id": 1, "name": "Team Name" },
        "is_active": true,
        "roles": ["user"]
      }
    ],
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 28
  }
}
```

**POST /api/admin/users**
```json
Request: {
  "name": "New User",
  "email": "new@example.com",
  "password": "password123",
  "team_id": 1,
  "is_active": true
}

Response: { "data": { "id": 2, ... } }
```

**PUT /api/admin/users/{id}**
```json
Request: {
  "name": "Updated Name",
  "email": "updated@example.com",
  "team_id": 1,
  "is_active": true
}

Response: { "data": { "id": 1, ... } }
```

**DELETE /api/admin/users/{id}**
```json
Response: { "message": "User deleted successfully." }
```

### Team Management Endpoints (requires admin role)

**GET /api/admin/teams?page=1&search=query**
```json
Response: {
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Team Name",
        "slug": "team-name",
        "description": "Team description",
        "is_active": true,
        "members_count": 5
      }
    ],
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 3
  }
}
```

**POST /api/admin/teams**
```json
Request: {
  "name": "New Team",
  "slug": "new-team",
  "description": "Team description",
  "is_active": true
}

Response: { "data": { "id": 2, ... } }
```

**PUT /api/admin/teams/{id}**
```json
Request: {
  "name": "Updated Team",
  "slug": "updated-team",
  "description": "Updated description",
  "is_active": true
}

Response: { "data": { "id": 1, ... } }
```

**DELETE /api/admin/teams/{id}**
```json
Response: { "message": "Team deleted successfully." }
```

**GET /api/admin/teams/{id}/members**
```json
Response: {
  "data": [
    {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "role": "member"
    }
  ]
}
```

**POST /api/admin/teams/{id}/members**
```json
Request: {
  "user_id": 2,
  "role": "member"
}

Response: { "message": "Member added to team." }
```

**DELETE /api/admin/teams/{teamId}/members/{userId}**
```json
Response: { "message": "Member removed from team." }
```

---

## Frontend Routing

```
/login         → Login page (public)
/profile       → Dashboard (protected, shows admin tabs if admin user)
/              → Redirects to /profile
```

All routes are handled by Vue Router which checks localStorage for token.

---

## Authentication Flow

1. **User visits app** → Router redirects to `/login` if no token
2. **User enters credentials** → Login.vue sends POST to `/api/login`
3. **Backend validates** → AuthController checks credentials
4. **Token created** → Sanctum generates token for user
5. **Token stored** → Frontend saves to localStorage
6. **Redirect** → Router redirects to `/profile`
7. **Dashboard loads** → Shows user profile and admin tabs (if admin)
8. **API calls** → Include `Authorization: Bearer {token}` header
9. **Logout** → Token deleted, redirected to `/login`

---

## Component Hierarchy

```
App.vue (root)
├── router-view
│   ├── Login.vue
│   │   └── [Form submission to /api/login]
│   └── Dashboard.vue
│       ├── Profile tab
│       │   └── [User profile display]
│       ├── Users tab (admin only)
│       │   └── UserList.vue
│       │       ├── [User list table]
│       │       ├── [Search & pagination]
│       │       └── [Modal: CreateEditUserForm]
│       └── Teams tab (admin only)
│           └── TeamList.vue
│               ├── [Team list table]
│               ├── [Search & pagination]
│               └── [Modal: CreateEditTeamForm]
```

---

## Authorization Strategy

### Role-Based Access Control (RBAC)

**Roles:**
- `admin` - Can manage users and teams
- `super-admin` - Full access, can delete admins
- `user` - Can only view/edit own profile

### Frontend Authorization
- Components check `user.roles` from localStorage
- Admin tabs only show if user has 'admin' or 'super-admin' role
- UserList and TeamList check before rendering

### Backend Authorization
- IsAdmin middleware verifies user has admin role
- Policies control data access (UserPolicy, TeamPolicy)
- Super-admin can delete non-self users/teams
- Admin cannot delete super-admin users

---

## Error Handling

### Frontend Error Handling
- Try-catch blocks on all fetch requests
- Error messages displayed to user
- Validation on form submission
- 401 errors trigger logout

### Backend Error Handling
- Validation exceptions return 422 status
- Authorization failures return 403 status
- NotFound exceptions return 404 status
- General errors return 500 status

---

## Security Checklist

- [x] CSRF protection enabled (Laravel default)
- [x] Sanctum token-based authentication
- [x] Role-based access control
- [x] Input validation (frontend + backend)
- [x] Authorization policies
- [x] Middleware checks on protected routes
- [x] Secure password hashing (bcrypt)
- [x] Token expiration handling
- [x] CORS configuration (if needed)

---

## Development Workflow

### Making Changes

**Backend API Changes:**
1. Update controller method
2. Test with curl or Postman
3. Restart dev server if needed

**Frontend Component Changes:**
1. Update `.vue` file
2. Vite auto-refreshes browser
3. Check browser console for errors

**Adding New Feature:**
1. Create controller method
2. Add route to `routes/api.php`
3. Create Vue component
4. Add route to `router/index.js`
5. Test end-to-end

---

## Troubleshooting

### 404 on API endpoints
- Check route in `routes/api.php`
- Verify namespace in controller imports
- Confirm middleware is not blocking

### CORS errors
- Add frontend URL to CORS in `config/cors.php`
- Or disable CORS for development

### Token expires quickly
- Check Sanctum token lifetime in `config/sanctum.php`
- Default is `expiration: 525600` (1 year)

### Component not rendering
- Check router configuration
- Verify component imports
- Check browser console for errors

### API returns 403 Forbidden
- Verify user has admin role
- Check IsAdmin middleware
- Confirm user role in database

### localStorage not persisting
- Check browser privacy settings
- Verify not in private/incognito mode
- Check for storage quota

---

## Performance Optimization

### Frontend
- Lazy load components with Vue Router
- Paginate large lists (already implemented)
- Cache API responses if needed
- Minimize bundle size with proper imports

### Backend
- Use database indexes on frequently queried columns
- Implement query caching for roles/teams
- Use eager loading with Eloquent
- Rate limit API endpoints

---

## Deployment

### Production Build
```bash
npm run build
php artisan optimize
php artisan config:cache
php artisan route:cache
```

### Server Configuration
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Environment Variables
```
APP_ENV=production
APP_DEBUG=false
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
SESSION_DOMAIN=yourdomain.com
```

---

## Testing

### Manual Testing Checklist
- [ ] Login with demo credentials
- [ ] View profile after login
- [ ] Logout and verify redirect to login
- [ ] Login as admin
- [ ] View Users tab
- [ ] Search users
- [ ] Create new user
- [ ] Edit existing user
- [ ] Delete user
- [ ] View Teams tab
- [ ] Search teams
- [ ] Create new team
- [ ] Edit existing team
- [ ] Delete team

### Browser Testing
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari
- [ ] Mobile Safari
- [ ] Mobile Chrome

---

## Support & Documentation

- **API Documentation:** See `ADMIN_API.md`
- **Frontend Setup:** See `FRONTEND_SETUP.md`
- **Components Reference:** See `FRONTEND_COMPONENTS.md`
- **Laravel Docs:** https://laravel.com/docs
- **Vue Docs:** https://vuejs.org/
- **Tailwind CSS:** https://tailwindcss.com/

---

## Version Information

- Laravel: 11.x
- Vue: 3.x
- Node: 16+ (recommended 18+)
- PHP: 8.2+
- Tailwind CSS: 3.x
- Vue Router: 4.x

