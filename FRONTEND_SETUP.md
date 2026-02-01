# Vue.js Frontend Setup Guide

## Overview
Complete Vue 3 SPA with Tailwind CSS, Vue Router, and Laravel Sanctum authentication.

## Project Structure

```
resources/
├── js/
│   ├── app.js                          # Main entry point with Vue Router
│   ├── bootstrap.js                    # Bootstrap configuration
│   ├── App.vue                         # Root component with router-view
│   ├── components/
│   │   ├── Login.vue                   # Authentication component
│   │   ├── Dashboard.vue               # Main dashboard with tabs
│   │   ├── UserList.vue                # User management list
│   │   └── TeamList.vue                # Team management list
│   └── router/
│       └── index.js                    # Vue Router configuration
└── css/
    └── app.css                         # Tailwind CSS imports
```

## Components

### 1. Login.vue
- Email and password form
- Demo credentials display
- Token storage in localStorage
- Redirect to dashboard after login
- CSRF token handling

**Key Features:**
- Form validation
- Error messages
- Token persistence

### 2. Dashboard.vue
- Tab-based navigation (Profile, Users, Teams)
- Conditional admin sections (only shown for admin/super-admin roles)
- User profile information display
- Role badges and status indicators
- Logout functionality

**Key Features:**
- Role-based access control
- User information display
- Navigation between management screens

### 3. UserList.vue
- Paginated user list (15 per page)
- Search by name or email
- Create user button
- Edit and delete buttons for each user
- Status indicators (Active/Inactive)
- Modal form for create/edit operations

**Key Features:**
- Pagination with prev/next buttons
- Real-time search
- Inline CRUD actions
- Modal-based forms

### 4. TeamList.vue
- Paginated team list
- Search by team name
- Create team button
- Edit and delete buttons for each team
- Member count display
- Modal form for create/edit operations

**Key Features:**
- Pagination
- Team search
- Inline actions
- Status management

### 5. Router Configuration
- `/login` - Public route
- `/profile` - Protected route (requires authentication)
- Route guards check localStorage for token
- Automatic redirect to login if not authenticated

## Installation & Setup

### 1. Install Dependencies
```bash
npm install
```

### 2. Build the Frontend
```bash
npm run build
```

### 3. Development Server
```bash
npm run dev
```

### 4. Verify Files Exist

Backend files that must exist:
- `app/Http/Controllers/AuthController.php` - Login/logout endpoints
- `app/Http/Controllers/Admin/UserController.php` - User management API
- `app/Http/Controllers/Admin/TeamController.php` - Team management API
- `app/Http/Middleware/IsAdmin.php` - Admin role checking
- `routes/api.php` - API routes configuration

Frontend files created:
- `resources/js/app.js` - Vue app setup
- `resources/js/App.vue` - Root component
- `resources/js/components/Login.vue` - Authentication
- `resources/js/components/Dashboard.vue` - Main dashboard
- `resources/js/components/UserList.vue` - User management
- `resources/js/components/TeamList.vue` - Team management
- `resources/js/router/index.js` - Router configuration

## API Endpoints

### Authentication
- `POST /api/login` - Login with email and password
- `POST /api/logout` - Logout (requires token)
- `GET /api/me` - Get current user info (requires token)

### User Management (requires admin role)
- `GET /api/admin/users` - List users with pagination
- `POST /api/admin/users` - Create user
- `GET /api/admin/users/{id}` - Get user details
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Delete user
- `POST /api/admin/users/{id}/activate` - Activate user
- `POST /api/admin/users/{id}/deactivate` - Deactivate user

### Team Management (requires admin role)
- `GET /api/admin/teams` - List teams with pagination
- `POST /api/admin/teams` - Create team
- `GET /api/admin/teams/{id}` - Get team details
- `PUT /api/admin/teams/{id}` - Update team
- `DELETE /api/admin/teams/{id}` - Delete team
- `GET /api/admin/teams/{id}/members` - Get team members
- `POST /api/admin/teams/{id}/members` - Add member to team
- `DELETE /api/admin/teams/{teamId}/members/{userId}` - Remove member from team

## Demo Credentials

For testing login functionality:
- **Email:** demo@example.com
- **Password:** password123

These credentials are displayed in the Login.vue component.

## Browser Storage

The application uses localStorage to persist authentication tokens:
- **Key:** `token` - Sanctum API token
- **Key:** `user` - User information (ID, name, email, roles)

These are set after successful login and cleared on logout.

## Authentication Flow

1. User navigates to `/login`
2. Enters email and password
3. Frontend sends POST request to `/api/login`
4. Backend validates credentials and returns token
5. Token stored in localStorage
6. User redirected to `/profile`
7. All subsequent API requests include `Authorization: Bearer {token}` header
8. Router guards check localStorage for token on route changes

## Authorization

Users with `admin` or `super-admin` roles see the Users and Teams tabs in Dashboard.

Admin middleware (`IsAdmin`) protects all `/api/admin/*` routes.

## Styling

All components use Tailwind CSS utility classes:
- Colors: `bg-blue-600`, `text-gray-900`, etc.
- Spacing: `px-6`, `py-3`, etc.
- Layout: `flex`, `grid`, `hover:bg-gray-50`, etc.
- Responsive: Uses Tailwind breakpoints

## Error Handling

- API errors display in error message boxes
- Form validation happens on the frontend and backend
- Network errors are caught and displayed to user
- All fetch requests include proper error handling

## Security Considerations

1. **CSRF Protection:** Handled by Laravel Sanctum
2. **Token Storage:** Stored in localStorage (adequate for SPAs)
3. **Role-Based Access:** Admin middleware checks user roles
4. **Authorization Policies:** Laravel policies control data access
5. **Input Validation:** Both frontend and backend validation

## Troubleshooting

### Token Not Persisting
- Check browser localStorage is enabled
- Verify `localStorage.setItem()` calls in Login.vue

### API Calls Failing with 401
- Check token is stored in localStorage
- Verify Authorization header is sent: `Authorization: Bearer {token}`
- Token may have expired, user needs to login again

### Admin Routes Returning 403
- User roles must include 'admin' or 'super-admin'
- Check user has proper roles assigned in database

### Components Not Rendering
- Verify all imports in App.vue and Dashboard.vue
- Check Vue Router configuration
- Verify `#app` element exists in HTML template

## Next Steps

1. Configure your HTML template to include `<div id="app"></div>`
2. Update `vite.config.js` if needed for your setup
3. Create demo users with admin roles for testing
4. Test authentication flow in browser
5. Verify API endpoints are accessible
