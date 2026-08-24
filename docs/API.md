# Notes App REST API

## Overview

JSON REST API for personal notes. API routes use Laravel Sanctum authentication and NotePolicy authorization.

Local development base URL:

```text
http://localhost:8000/api
```

Run local server with `php artisan serve`. Use your local server URL instead if configured differently. All examples send and expect JSON.

## Conventions

Required JSON headers:

```http
Accept: application/json
Content-Type: application/json
```

`Content-Type` applies when request has JSON body. Authenticated endpoints also require:

```http
Authorization: Bearer <TOKEN>
```

Dates in Note responses are Laravel JSON timestamps. Never rely on fields beyond documented response contract.

## Authentication

### POST /login

Creates Sanctum personal access token.

Authentication: none.

Headers:

```http
Accept: application/json
Content-Type: application/json
```

Request body:

```json
{
  "email": "developer@example.com",
  "password": "password",
  "device_name": "Postman"
}
```

Fields:

| Field | Required | Rules / behavior |
| --- | --- | --- |
| `email` | Yes | Valid email address. |
| `password` | Yes | String. |
| `device_name` | No | Token label. Server uses `api-client` when omitted. |

Successful response: `200 OK`

```json
{
  "message": "Token created successfully",
  "token_type": "Bearer",
  "token": "1|<plain-text-token>"
}
```

Store `token` securely. Send it with `Authorization: Bearer <TOKEN>` on protected requests. Plain-text token exists only in this login response.

Invalid credentials: `422 Unprocessable Content`

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The provided credentials do not match our records."
    ]
  }
}
```

Validation failures also return `422` with `message` and `errors`.

Rate limit: 5 login requests per minute for matching login identity and client context. Sixth request returns `429`; wait duration appears in `Retry-After`. Limit identity uses server-derived normalized email and client network context. Do not depend on its internal key format.

### POST /logout

Revokes only current Sanctum personal access token.

Authentication: required.

Headers:

```http
Accept: application/json
Authorization: Bearer <TOKEN>
```

Body: none.

Successful response: `200 OK`

```json
{
  "message": "Logged out successfully."
}
```

After personal-token logout, reuse of revoked token on protected endpoint returns `401 Unauthenticated.` Other tokens for same user remain valid. Authenticated API rate limit applies: 60 requests per minute.

## Note response contract

Every successful Note item exposes only:

```json
{
  "id": 42,
  "title": "Release checklist",
  "content": "Verify API documentation.",
  "created_at": "2026-08-24T10:15:30.000000Z",
  "updated_at": "2026-08-24T10:15:30.000000Z"
}
```

No `user_id`, user object, or other Note fields belong to API response contract.

## Notes endpoints

All Notes endpoints require Bearer authentication and consume authenticated API rate-limit budget. Required headers:

```http
Accept: application/json
Authorization: Bearer <TOKEN>
```

Add `Content-Type: application/json` for `POST` and `PATCH`.

### GET /notes

Lists only authenticated user's notes, newest first.

Path parameters: none.

Query parameters:

| Parameter | Required | Behavior |
| --- | --- | --- |
| `search` | No | Trimmed text. Matches user's `title` or `content` with partial, case behavior determined by database collation. Empty value disables filtering. |
| `page` | No | Standard Laravel paginator page number. Default: first page. |

Successful response: `200 OK`

```json
{
  "data": [
    {
      "id": 42,
      "title": "Release checklist",
      "content": "Verify API documentation.",
      "created_at": "2026-08-24T10:15:30.000000Z",
      "updated_at": "2026-08-24T10:15:30.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/notes?page=1",
    "last": "http://localhost:8000/api/notes?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [],
    "path": "http://localhost:8000/api/notes",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

Possible errors: `401`, `429`.

### POST /notes

Creates Note owned by authenticated user.

Path parameters: none. Query parameters: none.

Request body:

```json
{
  "title": "Release checklist",
  "content": "Verify API documentation."
}
```

Validation:

| Field | Required | Rules |
| --- | --- | --- |
| `title` | Yes | String; maximum 255 characters. |
| `content` | Yes | String. |

Ownership comes from authenticated user. Submitted `user_id` does not create Note for another user and is not part of request contract.

Successful response: `201 Created`

```json
{
  "data": {
    "id": 42,
    "title": "Release checklist",
    "content": "Verify API documentation.",
    "created_at": "2026-08-24T10:15:30.000000Z",
    "updated_at": "2026-08-24T10:15:30.000000Z"
  },
  "message": "Note created successfully"
}
```

Possible errors: `401`, `422`, `429`.

### GET /notes/{note}

Returns one Note owned by authenticated user.

Path parameters:

| Parameter | Required | Behavior |
| --- | --- | --- |
| `note` | Yes | Existing Note route-binding identifier. |

Query parameters: none. Body: none.

Successful response: `200 OK`

```json
{
  "data": {
    "id": 42,
    "title": "Release checklist",
    "content": "Verify API documentation.",
    "created_at": "2026-08-24T10:15:30.000000Z",
    "updated_at": "2026-08-24T10:15:30.000000Z"
  }
}
```

Possible errors: `401`, `403`, `404`, `429`.

### PATCH /notes/{note}

Updates one Note owned by authenticated user. Both fields required; this endpoint does not support partial field payloads.

Path parameters:

| Parameter | Required | Behavior |
| --- | --- | --- |
| `note` | Yes | Existing Note route-binding identifier. |

Query parameters: none.

Request body:

```json
{
  "title": "Release checklist v2",
  "content": "Verify docs, tests, and routes."
}
```

Validation:

| Field | Required | Rules |
| --- | --- | --- |
| `title` | Yes | String; maximum 255 characters. |
| `content` | Yes | String. |

Successful response: `200 OK`

```json
{
  "data": {
    "id": 42,
    "title": "Release checklist v2",
    "content": "Verify docs, tests, and routes.",
    "created_at": "2026-08-24T10:15:30.000000Z",
    "updated_at": "2026-08-24T10:20:00.000000Z"
  },
  "message": "Note updated successfully"
}
```

Possible errors: `401`, `403`, `404`, `422`, `429`.

### DELETE /notes/{note}

Deletes one Note owned by authenticated user.

Path parameters:

| Parameter | Required | Behavior |
| --- | --- | --- |
| `note` | Yes | Existing Note route-binding identifier. |

Query parameters: none. Body: none.

Successful response: `200 OK`

```json
{
  "message": "Note deleted successfully"
}
```

Possible errors: `401`, `403`, `404`, `429`.

## Pagination

`GET /notes` uses Laravel pagination with 10 Notes per page. Use `page` to select page:

```text
GET /api/notes?search=release&page=2
```

Response has:

- `data`: Note items for selected page.
- top-level `links`: URLs for first, last, previous, and next pages; unavailable previous/next values are `null`.
- `meta.current_page`: selected page number.
- `meta.last_page`: final available page number.
- `meta.per_page`: fixed page size, `10`.
- `meta.total`: total matching Notes across pages.
- `meta.links`: Laravel page-link entries for navigation.

When `search` is present, generated pagination URLs retain it.

## Authorization and security

Authentication proves request identity. NotePolicy authorization decides whether that authenticated user may access individual Note.

- List query scopes to authenticated user's Notes.
- Create operation assigns authenticated user as owner.
- Submitted `user_id` cannot assign ownership to another user.
- Accessing existing another user's Note returns `403`.
- Missing Note returns `404`.
- Valid authentication does not bypass NotePolicy.

## Error responses

API errors return JSON.

### 401 Unauthorized

Missing, invalid, or revoked Bearer token on protected endpoint:

```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden

Authenticated user targets another user's existing Note:

```json
{
  "message": "This action is unauthorized."
}
```

### 404 Not Found

Route-bound Note does not exist:

```json
{
  "message": "Resource not found."
}
```

### 422 Unprocessable Content

Invalid login, create, or update payload. Error field names vary by failed validation:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": [
      "The title field is required."
    ],
    "content": [
      "The content field is required."
    ]
  }
}
```

### 429 Too Many Requests

Rate limit exceeded:

```json
{
  "message": "Too Many Requests."
}
```

Response includes `Retry-After` header. Wait specified number of seconds before retrying.

## Rate limits

- Authenticated Notes API, including logout: 60 requests per minute per authenticated user.
- Login: 5 requests per minute for matching login identity and client context.
- Authenticated limit identity comes from authenticated user. Login limit uses server-derived login and network context. These details are intentionally not client-configurable.
- `429` response body and `Retry-After` behavior appear above.

## cURL flows

Set local API base URL:

```bash
BASE_URL="http://localhost:8000/api"
```

### Login

```bash
curl -X POST "$BASE_URL/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "developer@example.com",
    "password": "password",
    "device_name": "curl"
  }'
```

Copy `token` from response into `<TOKEN>`.

### List Notes

```bash
curl "$BASE_URL/notes?search=release&page=1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TOKEN>"
```

### Create Note

```bash
curl -X POST "$BASE_URL/notes" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <TOKEN>" \
  -d '{
    "title": "Release checklist",
    "content": "Verify API documentation."
  }'
```

### Update Note

```bash
curl -X PATCH "$BASE_URL/notes/<NOTE_ID>" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <TOKEN>" \
  -d '{
    "title": "Release checklist v2",
    "content": "Verify docs, tests, and routes."
  }'
```

### Delete Note

```bash
curl -X DELETE "$BASE_URL/notes/<NOTE_ID>" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TOKEN>"
```

### Logout

```bash
curl -X POST "$BASE_URL/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TOKEN>"
```

After logout, retry authenticated request with same `<TOKEN>`; expect `401`.

## Postman

1. Create `POST {{base_url}}/login`, set `Accept` and `Content-Type` to `application/json`, then send `email`, `password`, optional `device_name` as raw JSON.
2. Copy response `token`. Add collection or request authorization type `Bearer Token`; paste token. Set `base_url` to `http://localhost:8000/api` for default local server.
3. Call Notes endpoints. For create/update, use raw JSON body and `Content-Type: application/json`.
4. Send `POST {{base_url}}/logout` with Bearer token. Repeat protected request with same token; expect `401`.
5. Test expected failures: no/invalid/revoked token gives `401`; another user's Note gives `403`; invalid body gives `422`; excess requests gives `429` and `Retry-After`.

No Postman collection exists in repository. This document is single API reference.
```}彩票主管】【。json? wait tool maybe execution result.  大发棋牌assistant to=functions.todo კომენტary  新天天彩票  天天中彩票不中返json? let's see.출장샵assistant to=functions.todo code 招商总代්ඩjson】【：】【“】【{