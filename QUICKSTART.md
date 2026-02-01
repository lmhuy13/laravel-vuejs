# Quick Start Guide - Laravel + Vue Admin Dashboard

## 5-Minute Setup

### Prerequisites
- Node.js 16+
- PHP 8.2+
- MySQL/PostgreSQL
- Composer

---

## Option 1: Using Docker (Recommended)

### Prerequisites
- Docker
- Docker Compose

### Setup with Docker
```bash
# Build and start containers
docker-compose up -d

# Install dependencies
docker-compose exec app composer install
docker-compose exec app npm install

# Generate app key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Access application
http://localhost:8000
```

**What's Running:**
- App container: Laravel API
- Node container: Vite development server
- MySQL: Database
- Redis: Cache and queue

**Useful Commands:**
```bash
# View logs
docker-compose logs -f app

# Enter container shell
docker-compose exec app bash

# Stop containers
docker-compose down

# Rebuild containers
docker-compose up -d --build
```

---

## Option 2: Local Development

### Step 1: Install Dependencies
```bash
# Backend
composer install

# Frontend
npm install
```

### Step 2: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_vue
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:8000,127.0.0.1:8000
```

### Step 3: Database Setup
```bash
# Create tables
php artisan migrate

# Seed demo data
php artisan db:seed

# Or create specific demo user:
php artisan tinker
> User::create(['name' => 'Demo', 'email' => 'demo@websitecuatoi.com', 'password' => bcrypt('password123')])
> $user = User::first(); $admin = Role::where('slug', 'admin')->first(); $user->roles()->attach($admin)
```

### Step 4: Build Frontend
```bash
# Production build
npm run build

# Or watch for development
npm run dev
```

### Step 5: Run Application
```bash
# Terminal 1 - Laravel
php artisan serve

# Terminal 2 - Vite (only if using npm run dev)
npm run dev
```

### Step 6: Access Application
- Navigate to `http://localhost:8000`
- Login with:
  - Email: `demo@websitecuatoi.com`
  - Password: `password123`

---

## What's Included

✅ **Authentication System**
- Email/password login
- Token-based with Sanctum
- Logout functionality
- Automatic session management

✅ **Admin Dashboard**
- User management (CRUD operations)
- Team management (CRUD operations)
- Role-based access control
- Pagination and search

✅ **Frontend Components**
- Vue 3 with Composition API
- Vue Router for navigation
- Tailwind CSS styling
- Responsive design

✅ **Backend API**
- RESTful endpoints
- Authorization policies
- Admin middleware
- Error handling

---

## Key Credentials

**Demo User (Admin)**
- Email: `demo@websitecuatoi.com`
- Password: `password123`

**API Token Example:**
- Generated after login
- Stored in localStorage
- Used for all API requests

---

## Project Structure

```
resources/
├── views/app.blade.php          ← Main SPA template
├── js/
│   ├── app.js                   ← Vue setup
│   ├── App.vue                  ← Root component
│   ├── components/
│   │   ├── Login.vue            ← Login page
│   │   ├── Dashboard.vue        ← Main dashboard
│   │   ├── UserList.vue         ← User management
│   │   └── TeamList.vue         ← Team management
│   └── router/
│       └── index.js             ← Router config
└── css/app.css                  ← Tailwind CSS

app/Http/Controllers/
├── AuthController.php           ← Login/logout
└── Admin/
    ├── UserController.php       ← User API
    └── TeamController.php       ← Team API

routes/
├── api.php                      ← API routes
└── web.php                      ← Web routes (SPA)
```

---

## Common Tasks

### Create New Admin User
```bash
php artisan tinker
> $user = User::create(['name' => 'Admin', 'email' => 'admin@websitecuatoi.com', 'password' => bcrypt('admin123')])
> $admin = Role::where('slug', 'admin')->first()
> $user->roles()->attach($admin)
```

### Rebuild Frontend Assets
```bash
npm run build
```

### View Database
```bash
# Using SQLite browser or
php artisan tinker
> DB::table('users')->get()
```

### Check API Endpoints
```bash
# List all routes
php artisan route:list

# Or view routes/api.php for API routes
```

---

## API Endpoints Quick Reference

### Authentication
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires token)
- `GET /api/me` - Get current user (requires token)

### Users (Admin Only)
- `GET /api/admin/users` - List users
- `POST /api/admin/users` - Create user
- `GET /api/admin/users/{id}` - Get user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Delete user

### Teams (Admin Only)
- `GET /api/admin/teams` - List teams
- `POST /api/admin/teams` - Create team
- `GET /api/admin/teams/{id}` - Get team
- `PUT /api/admin/teams/{id}` - Update team
- `DELETE /api/admin/teams/{id}` - Delete team

---

## Frontend Routes

- `/login` - Login page
- `/profile` - Dashboard (shows admin tabs if admin user)
- `/` - Redirects to /profile

---

## First Login Test

1. Open browser to `http://localhost:8000`
2. You'll be redirected to `/login`
3. Enter demo credentials
4. You'll be logged in and see the dashboard
5. If you're an admin, you'll see Users and Teams tabs
6. Click on tabs to manage users and teams
7. Click "Create User" or "Create Team" to add new items
8. Use search to filter by name/email
9. Click Edit or Delete to modify items
10. Click "Logout" to sign out

---

## Development Tips

### Hot Reload Frontend
```bash
npm run dev
```
This watches for changes and automatically reloads the browser.

### Debug Frontend
1. Open browser DevTools (F12)
2. Check Console tab for errors
3. Use Network tab to inspect API calls
4. Use Storage tab to view localStorage

### Debug Backend
```bash
php artisan tinker
> DB::table('users')->first()
> User::with('roles')->first()
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

---

## Troubleshooting

**Can't login?**
- Check database has users table with demo user
- Verify password is correct (demo@websitecuatoi.com / password123)
- Check `/api/login` endpoint in API routes

**404 on API?**
- Verify routes are in `routes/api.php`
- Check middleware is correct
- Restart Laravel server

**Components not showing?**
- Check Vue components are imported
- Verify router configuration
- Check browser console for errors

**Token not persisting?**
- Check localStorage is enabled
- Check Application tab in DevTools
- Verify login response includes token

---

## Next Steps

1. **Customize Colors** - Edit Tailwind config
2. **Add More Fields** - Extend user/team models
3. **Set Up Notifications** - Add toast messages
4. **Add Validations** - Enhance form validation
5. **Create More Pages** - Add admin reports, settings
6. **Set Up Tests** - Add PHPUnit and Vue tests
7. **Deploy** - Set up production server

---

## Documentation Files

Read these for more detailed information:

- **INTEGRATION_GUIDE.md** - Complete setup and configuration
- **ADMIN_API.md** - All API endpoints with examples
- **FRONTEND_SETUP.md** - Frontend installation guide
- **FRONTEND_COMPONENTS.md** - Component reference

---

## Support

If you encounter issues:

1. Check the troubleshooting section above
2. Review detailed documentation files
3. Check browser console (F12) for errors
4. Check Laravel logs: `storage/logs/laravel.log`
5. Verify all files are created (see INTEGRATION_GUIDE.md)

---

## Summary

You now have a complete admin dashboard with:
- User authentication
- User management
- Team management
- Role-based access control
- Modern Vue 3 frontend
- RESTful Laravel API

Happy coding! 🚀
