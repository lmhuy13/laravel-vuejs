# Laravel Vue Admin Dashboard

Complete admin dashboard system with modern architecture and best practices.

## ✨ Features

You now have a complete admin dashboard with:

- User authentication
- User management
- Team management
- Role-based access control
- Modern Vue 3 frontend
- RESTful Laravel API
- Repository & Service pattern
- Swagger/OpenAPI documentation
- Queue job send mail
- Docker containerized with multiple services

## 🐳 Docker Services

- **app** - Laravel API server (port 8010)
- **vite** - Vue/Vite dev server (port 5183)
- **postgres** - PostgreSQL database (port 5442)
- **redis** - Cache & queue (port 6389)
- **supervisor** - Job queue processor
- **pgadmin** - PostgreSQL admin UI (port 5050)

## 🎓 Technology Stack

**Backend:**
- Laravel 11
- Sanctum
- PHP 8.2+

**Frontend:**
- Vue 3
- Vue Router 4
- Tailwind CSS 3
- Vite

**Database:**
- PostgreSQL

## 🚀 Visit from port

Visit `http://localhost:8010`
`http://localhost:8010/api/docs`
`http://localhost:8010/admin-theme/`

## 🚀 Queue & Email System

- ✅ Asynchronous email delivery via Redis Queue
- ✅ Automatic retry (3 attempts with 30s delay on failure)
- ✅ 60-second timeout per job
- ✅ Gmail SMTP integration
- ✅ Comprehensive logging
- ✅ Supervisor auto-processes jobs

## 📚 Documentation

- [QUICKSTART.md](QUICKSTART.md) - 5-minute setup guide
- [INTEGRATION_GUIDE.md](INTEGRATION_GUIDE.md) - Complete setup and deployment
- [API_DOCUMENTATION.md](API_DOCUMENTATION.md) - API reference with Swagger
- [FRONTEND_SETUP.md](FRONTEND_SETUP.md) - Frontend installation and setup
- [FRONTEND_COMPONENTS.md](FRONTEND_COMPONENTS.md) - Component reference

All endpoints documented with request/response examples.
