# Darul Ifta Portal — Mobile API Documentation

This documentation outlines the RESTful API endpoints for the Darul Ifta mobile application. The API follows standard HTTP protocols and returns JSON responses.

---

## 🚀 Base Configuration

| Aspect | Value |
| :--- | :--- |
| **Base URL** | `https://daruliftakauzariyya.com//api` |
| **Response Format** | `application/json` |
| **Authentication** | Bearer Token (Laravel Sanctum) |

---

## 🔐 Authentication

All protected routes require an `Authorization` header with a valid user token.

`Authorization: Bearer <your_token>`

### 1. Register Account
`POST /auth/register`

Create a new questioner account.

**Request Body:**
```json
{
  "name": "Full Name",
  "email": "user@example.com",
  "password": "strong_password_min_8"
}
```

**Success Response (201):**
```json
{
  "status": "success",
  "token": "1|abc123token...",
  "user": { "id": 12, "name": "Full Name", ... }
}
```

### 2. Login
`POST /auth/login`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "your_password"
}
```

**Success Response (200):**
```json
{
  "status": "success",
  "token": "2|xyz456token...",
  "user": { "id": 12, "name": "Full Name", ... }
}
```

---

## 📚 Fatwa Archive (Public)

### 1. List Fatwas
`GET /fatwas`

Fetch paginated published fatwas.

**Parameters:**
- `page` (integer): Page number for pagination.

**Success Response:**
```json
{
  "status": "success",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 5,
        "title": "Ruling on XYZ",
        "published_at": "2026-04-07 10:00:00",
        "question": { "body": "..." },
        "mufti_answer": { "answer_body": "..." }
      }
    ],
    "total": 150
  }
}
```

### 2. Get Single Fatwa
`GET /fatwas/{id}`

**Success Response:**
```json
{
  "status": "success",
  "data": {
    "id": 5,
    "title": "...",
    "mufti_answer": { "answer_body": "...", "supporting_references": [] }
  }
}
```

### 3. List Categories
`GET /categories`

Get the organizational tree for inquiries.

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Finance",
      "children": [ { "id": 10, "name": "Zakat" } ]
    }
  ]
}
```

---

## ✍️ Inquiries (Authenticated)

### 1. Submit Question
`POST /questions`

**Request Body:**
```json
{
  "title": "Brief title",
  "body": "Long description of the inquiry...",
  "madhab": "Hanafi",
  "category_id": 10
}
```

**Valid Madhab Values:** `Hanafi`, `Shafii`, `Maliki`, `Hanbali`, `Other`

**Success Response (201):**
```json
{
  "status": "success",
  "question_id": "IFTA-20260408-0001"
}
```

### 2. My Question History
`GET /questions`

Fetch the user's submitted questions.

**Success Response:**
```json
{
  "status": "success",
  "data": {
    "data": [
      {
        "question_id": "IFTA-20260408-0001",
        "status": "pending",
        "created_at": "2026-04-08 14:00:00"
      }
    ]
  }
}
```

### 3. Current Profile
`GET /user`

Get authenticated user data.

**Success Response:**
```json
{
  "id": 12,
  "name": "Full Name",
  "email": "user@example.com",
  "roles": ["questioner"]
}
```

---

## ⚠️ Error Responses

| Code | Meaning | Description |
| :--- | :--- | :--- |
| **401** | Unauthorized | Token is missing or invalid. |
| **403** | Forbidden | User does not have the required role. |
| **422** | Validation Error | One or more fields in the request body are invalid. |
| **404** | Not Found | The requested resource (Fatwa/User) does not exist. |
| **500** | Server Error | An internal error occurred. |

---

**Darul Ifta Development Team** | *Last Updated: April 8, 2026*
