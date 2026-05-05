# MechFinder — System Documentation

## Overview

MechFinder is a Laravel 12 PWA-based dispatch and messaging platform that connects **motorists** (guests or registered) with **automotive repair shops**. Motorists can find nearby shops via GPS, submit service requests, track job status in real-time, message shops, and leave reviews — all without requiring an account.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS, Vite |
| Database | MySQL |
| Auth | Session + Google OAuth 2.0 (Gmail-only) |
| PWA | Service Worker + Web App Manifest |
| Real-time | AJAX polling |
| Deployment | Railway (production), Herd (local) |

---

## Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email
        string password
        string google_id
        text google_token
        text google_refresh_token
        bigint shop_id
        string role
        timestamp created_at
        timestamp updated_at
    }

    shops {
        bigint id PK
        bigint owner_id FK
        string shop_name
        string address
        string phone
        string email
        string latitude
        string longitude
        string location
        enum status
        timestamp created_at
        timestamp updated_at
    }

    dispatch_requests {
        bigint id PK
        bigint motorist_id FK
        bigint shop_id FK
        string guest_token
        string guest_name
        string owner_name
        string contact_number
        string issue_type
        string status
        text description
        string location
        string latitude
        string longitude
        decimal price
        decimal distance
        enum request_type
        string vehicle_make_model
        string vehicle_variant_color
        string plate_temp_number
        timestamp accepted_at
        timestamp en_route_at
        timestamp arrived_at
        timestamp completed_at
        timestamp created_at
        timestamp updated_at
    }

    reviews {
        bigint id PK
        bigint motorist_id FK
        bigint shop_id FK
        bigint dispatch_id FK
        string guest_token
        string owner_name
        int rating
        text comment
        timestamp created_at
        timestamp updated_at
    }

    messages {
        bigint id PK
        bigint dispatch_id FK
        bigint motorist_id FK
        bigint shop_id FK
        text message
        boolean is_read
        enum sender_type
        timestamp created_at
        timestamp updated_at
    }

    shop_messages {
        bigint id PK
        bigint motorist_id FK
        bigint shop_id FK
        string guest_token
        text message
        boolean is_read
        enum sender_type
        timestamp created_at
        timestamp updated_at
    }

    sessions {
        string id PK
        bigint user_id FK
        string ip_address
        text user_agent
        longtext payload
        int last_activity
    }

    users ||--o{ shops : "owns"
    users ||--o{ dispatch_requests : "requests"
    users ||--o{ reviews : "writes"
    users ||--o{ messages : "sends"
    users ||--o{ shop_messages : "sends"
    users ||--o{ sessions : "has"
    shops ||--o{ dispatch_requests : "receives"
    shops ||--o{ reviews : "receives"
    shops ||--o{ messages : "receives"
    shops ||--o{ shop_messages : "receives"
    dispatch_requests ||--o{ messages : "has"
    dispatch_requests ||--o| reviews : "has"
```

---

## Data Flow Diagram (DFD)

```mermaid
flowchart TD
    subgraph MOTORIST["Motorist (Guest / Auth)"]
        M1([Open PWA])
        M2([Browse Shops Map])
        M3([Select Shop])
        M4([Submit Dispatch Request])
        M5([Track Request Status])
        M6([Message Shop])
        M7([Submit Review])
    end

    subgraph SHOP_OWNER["Shop Owner (Authenticated)"]
        S1([Login via Gmail / Google])
        S2([View Dashboard])
        S3([Accept / Decline Request])
        S4([Update Job Status])
        S5([Message Motorist])
        S6([Manage Settings])
        S7([View Reviews])
    end

    subgraph APP["MechFinder Laravel App"]
        A1[MotoristController]
        A2[ShopController]
        A3[AuthController]
        A4[(MySQL Database)]
    end

    subgraph EXTERNAL["External Services"]
        G1[Google OAuth 2.0]
        G2[Google Maps API]
    end

    M1 --> A1
    M2 --> A1
    M2 --> G2
    M3 --> A1
    M4 --> A1
    M4 --> A4
    M5 --> A1
    M5 --> A4
    M6 --> A1
    M6 --> A4
    M7 --> A1
    M7 --> A4

    S1 --> A3
    A3 --> G1
    G1 --> A3
    A3 --> A4
    S2 --> A2
    S2 --> A4
    S3 --> A2
    S3 --> A4
    S4 --> A2
    S4 --> A4
    S5 --> A2
    S5 --> A4
    S6 --> A2
    S6 --> A4
    S7 --> A2
    S7 --> A4

    A1 --> A4
    A2 --> A4
```

---

## Dispatch Request Lifecycle

```mermaid
stateDiagram-v2
    [*] --> requested : Motorist submits request
    requested --> accepted : Shop accepts
    requested --> declined : Shop declines
    accepted --> en_route : Shop starts traveling
    en_route --> arrived : Shop arrives at location
    arrived --> in_progress : Job started
    in_progress --> completed : Job finished
    declined --> [*]
    completed --> [*]
```

---

## Routes Reference

### Public
| Method | Route | Description |
|---|---|---|
| GET | `/` | Welcome / landing page |
| GET | `/login` | Login form |
| POST | `/login` | Process login |
| GET | `/signup` | Signup form |
| POST | `/signup` | Process registration |
| GET | `/auth/google/login` | Google OAuth login |
| GET | `/auth/google/signup` | Google OAuth signup |
| GET | `/auth/google/callback` | Google OAuth callback |

### Motorist (Public)
| Method | Route | Description |
|---|---|---|
| GET | `/motorist` | PWA home |
| GET | `/motorist/shops` | Shop list view |
| GET | `/motorist/shop/{id}` | Shop detail |
| POST | `/motorist/dispatch` | Submit dispatch request |
| GET | `/motorist/request/{id}` | Track request status |
| POST | `/motorist/review` | Submit review |
| GET | `/api/motorist/shops` | JSON shops with distance/ETA |
| POST | `/api/dispatch-requests` | Create dispatch (API) |
| GET | `/api/chat/{dispatchId}` | Get dispatch messages |
| POST | `/api/messages` | Send message |
| POST | `/api/reviews` | Submit review (API) |
| GET | `/api/motorist/shops-for-messaging` | Shops for messaging UI |
| GET | `/api/motorist/shop-messages/{shopId}` | Get conversation with shop |
| POST | `/api/motorist/shop-messages` | Send message to shop |

### Shop (Protected — requires auth)
| Method | Route | Description |
|---|---|---|
| GET | `/shop/dashboard` | Main dashboard |
| GET | `/shop/requests` | All requests with filters |
| GET | `/shop/jobs` | Active/completed jobs |
| GET | `/shop/messages` | Message conversations |
| GET | `/shop/reviews` | Reviews & analytics |
| GET | `/shop/settings` | Shop profile settings |
| POST | `/shop/accept/{id}` | Accept request |
| POST | `/shop/decline/{id}` | Decline request |
| POST | `/shop/request/{id}/status` | Update job status |
| GET | `/shop/dashboard-data` | AJAX — pending requests HTML |
| GET | `/shop/dashboard-map-data` | AJAX — map data JSON |
| POST | `/shop/settings/update` | Save shop settings |
| POST | `/shop/settings/toggle-status` | Toggle open/closed |
| GET | `/api/shop/status` | Get shop status (JSON) |
| GET | `/api/shop/messages/{dispatchId}` | Get messages for dispatch |
| POST | `/api/shop/messages/send` | Send message to motorist |

---

## Database Tables

| Table | Purpose |
|---|---|
| `users` | Shop owner accounts (Gmail/Google OAuth) |
| `shops` | Shop profiles with GPS coordinates and status |
| `dispatch_requests` | Service requests with full lifecycle tracking |
| `reviews` | Star ratings and comments per shop |
| `messages` | Dispatch-specific chat messages |
| `shop_messages` | Direct motorist-shop conversations (supports guests) |
| `sessions` | Laravel session storage |

---

## PWA Setup

- **Manifest:** `/public/manifest-motorist.json`
- **Service Worker:** `/public/sw-motorist.js`
- **Scope:** `/motorist/`
- **Strategy:** Network-first with cache fallback
- **Installable on:** Android Chrome, iOS Safari (Add to Home Screen)
