# Database Schema Documentation

## Overview

User management system with roles, teams, and profiles.

## Database Tables

### 1. **users** (Users Management)

```sql
id              - Primary Key
team_id         - Foreign Key (teams)
name            - User name
email           - User email (unique)
password        - Hashed password
email_verified_at - Email verification timestamp
is_active       - Status flag
remember_token  - Remember me token
created_at      - Created timestamp
updated_at      - Updated timestamp
deleted_at      - Soft delete timestamp
```

**Relationships:**
- Belongs to 1 Team
- Has 1 Profile
- Has many Roles (via user_roles)
- Has many UserRoles

---

### 2. **teams** (Teams/Organizations)

```sql
id              - Primary Key
name            - Team name (unique)
slug            - URL-friendly identifier (unique)
description     - Team description
logo            - Team logo URL
is_active       - Status flag
created_at      - Created timestamp
updated_at      - Updated timestamp
deleted_at      - Soft delete timestamp
```

**Relationships:**
- Has many Users
- Has many Roles
- Has many UserRoles

---

### 3. **roles** (Roles/Permissions)

```sql
id              - Primary Key
name            - Role name (unique)
slug            - URL-friendly identifier (unique)
description     - Role description
is_active       - Status flag
created_at      - Created timestamp
updated_at      - Updated timestamp
deleted_at      - Soft delete timestamp
```

**Relationships:**
- Has many Users (via user_roles)
- Has many UserRoles

**Default Roles:**
- `super-admin` - Super Administrator
- `admin` - Administrator
- `manager` - Manager
- `team-lead` - Team Lead
- `developer` - Developer
- `user` - Standard User
- `viewer` - Viewer (read-only)

---

### 4. **user_roles** (User-Role Assignment)

```sql
id              - Primary Key
user_id         - Foreign Key (users) - cascade delete
role_id         - Foreign Key (roles) - cascade delete
team_id         - Foreign Key (teams) - nullable, cascade delete
created_at      - Created timestamp
updated_at      - Updated timestamp

UNIQUE(user_id, role_id, team_id)
```

**Purpose:**
- Assign roles to users
- Support team-specific roles
- If team_id is NULL: global role (super-admin)
- If team_id has value: role specific to that team

**Examples:**
- User 1 has 'super-admin' role globally (team_id = NULL)
- User 2 has 'admin' role in Team 1 (team_id = 1)
- User 2 has 'developer' role in Team 2 (team_id = 2)

---

### 5. **profiles** (User Profiles)

```sql
id              - Primary Key
user_id         - Foreign Key (users) - unique, cascade delete
phone           - Phone number
avatar          - Avatar image URL
bio             - User biography
address         - Street address
city            - City
country         - Country
postal_code     - Postal code
date_of_birth   - Date of birth
gender          - Gender (male/female/other)
website         - Personal website URL
created_at      - Created timestamp
updated_at      - Updated timestamp
deleted_at      - Soft delete timestamp

UNIQUE(user_id)
```

**Relationships:**
- Belongs to 1 User (one-to-one)

---

## Relationships (ER Diagram)

```
User (1) ---< (M) Team
User (1) --M-- (M) Role (via user_roles)
User (1) ---> (1) Profile
User (1) ---< (M) UserRole

UserRole (M) --> (1) User
UserRole (M) --> (1) Role
UserRole (M) --> (1) Team

Team (1) ---< (M) User
Team (1) ---< (M) UserRole

Role (1) ---< (M) UserRole
Role (M) --M-- (M) User (via user_roles)
```

---

## Default Test Users

| Email | Password | Role | Team |
|-------|----------|------|------|
| superadmin@example.com | password | Super Admin | - |
| admin@example.com | password | Admin | Default Team |
| manager@example.com | password | Manager | Default Team |
| developer@example.com | password | Developer | Default Team |
| user@example.com | password | User | Default Team |

---

## Migration Files

1. `0001_01_01_000000_create_users_table.php` - Users (modified)
2. `2025_01_21_000001_create_teams_table.php` - Teams
3. `2025_01_21_000002_create_roles_table.php` - Roles
4. `2025_01_21_000003_create_profiles_table.php` - Profiles
5. `2025_01_21_000004_create_user_roles_table.php` - User Roles
6. `2025_01_21_000005_add_team_and_status_to_users_table.php` - Add columns to users

---

## Model Files

- `app/Models/User.php` - User Model
- `app/Models/Team.php` - Team Model
- `app/Models/Role.php` - Role Model
- `app/Models/Profile.php` - Profile Model
- `app/Models/UserRole.php` - UserRole Model

---

## Seeder Files

- `database/seeders/RoleSeeder.php` - Seed default roles
- `database/seeders/TeamSeeder.php` - Seed default teams
- `database/seeders/UserSeeder.php` - Seed default users
- `database/seeders/DatabaseSeeder.php` - Main seeder

---

## Usage

### Get User Information

```php
// Get user with profile
$user = User::with('profile')->find(1);
echo $user->profile->phone;

// Get user with roles
$user = User::with('roles')->find(1);
foreach ($user->roles as $role) {
    echo $role->name;
}

// Get user with team
$user = User::with('team')->find(1);
echo $user->team->name;
```

### Assign Role to User

```php
$user = User::find(1);
$role = Role::where('slug', 'developer')->first();
$team = Team::find(1);

// Assign role to specific team
UserRole::create([
    'user_id' => $user->id,
    'role_id' => $role->id,
    'team_id' => $team->id,
]);
```

### Check User Role

```php
$user = User::find(1);

// Check roles
if ($user->roles()->where('slug', 'admin')->exists()) {
    echo "User is admin";
}

// Or use Spatie/Laravel Permission (requires additional package)
```

### Create Profile for User

```php
$user = User::find(1);
$user->profile()->create([
    'phone' => '+1-555-0100',
    'bio' => 'My bio',
    'city' => 'New York',
    'country' => 'USA',
]);
```

---

## Soft Delete

All tables (except user_roles) support soft delete:

```php
// Soft delete
$user->delete();

// Restore
$user->restore();

// Force delete
$user->forceDelete();

// Include soft deleted records
User::withTrashed()->find(1);

// Only soft deleted records
User::onlyTrashed()->get();
```

---

## Running Migrations & Seeds

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Run seeders
docker-compose exec app php artisan db:seed

# Run specific seeder
docker-compose exec app php artisan db:seed --class=RoleSeeder

# Reset & re-run migrations (development only!)
docker-compose exec app php artisan migrate:refresh --seed
```

---

## Next Steps

1. Install [Spatie/Laravel Permission](https://github.com/spatie/laravel-permission) for permission management (optional)
2. Create Controllers/APIs for user, role, and team management
3. Create middleware to check user roles/permissions
4. Create Vue components to display user data
