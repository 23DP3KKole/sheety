# Guitar Tabs Web App

Full-stack guitar tab sharing app with Vue 3 frontend and Laravel 13 API.

## Stack

- **Frontend:** Vue 3, Vite, Tailwind CSS, Pinia, Vue Router
- **Backend:** Laravel 13, Sanctum, SQLite
- **Data access:** SQL `JOIN` queries (see `backend/app/Queries/`) combine related tables for list/detail pages

## Quick start

### Backend

```bash
cd backend
php artisan serve
```

API runs at `http://127.0.0.1:8000`.

### Frontend

```bash
cd frontend
npm run dev
```

App runs at `http://localhost:5173` (proxies `/api` to Laravel).

## Demo accounts

| Role  | Email                    | Password   |
|-------|--------------------------|------------|
| Admin | admin@guitartabs.test    | password   |
| User  | demo@guitartabs.test     | password   |

## API

| Method | Endpoint              | Auth     | Description        |
|--------|-----------------------|----------|--------------------|
| POST   | /api/login            | —        | Login              |
| POST   | /api/register         | —        | Register           |
| GET    | /api/tabs             | —        | List/search tabs   |
| GET    | /api/tabs/:id         | —        | Tab details        |
| POST   | /api/tabs             | User     | Upload tab         |
| PUT    | /api/tabs/:id         | Owner    | Edit tab           |
| DELETE | /api/tabs/:id         | Owner/Admin | Delete tab    |
| GET    | /api/favorites        | User     | List favorites     |
| POST   | /api/favorites/:id    | User     | Save favorite      |
| DELETE | /api/favorites/:id    | User     | Remove favorite    |
| GET    | /api/tabs/:id/comments | —       | List tab comments  |
| POST   | /api/tabs/:id/comments | User    | Add comment        |
| DELETE | /api/comments/:id     | Owner/Admin | Delete comment |
| POST   | /api/comments/:id/vote | User    | Upvote/downvote    |
| GET    | /api/admin/users      | Admin    | List users         |
| DELETE | /api/admin/users/:id  | Admin    | Delete user        |
| GET    | /api/admin/tabs       | Admin    | List all tabs      |

## Roles

- **Guest:** search and view tabs, read comments
- **Registered:** upload, edit own tabs, save favorites, comment and vote
- **Admin:** delete any tab or user
