# Project Documentation

## Database Schema Diagram
The database uses a relational model with MariaDB.

```mermaid
erDiagram
    USERS {
        int id PK
        int shop_id FK
        enum role
        string username
        string email
        string password_hash
    }
    SHOPS {
        int id PK
        string name
        enum status
        int subscription_plan_id FK
    }
    SUBSCRIPTION_PLANS {
        int id PK
        string name
        decimal price
    }
    PRODUCTS {
        int id PK
        int shop_id FK
        string name
        decimal buy_price
        decimal sell_price
        int stock_qty
        int category_id FK
        int brand_id FK
    }
    CATEGORIES {
        int id PK
        string name
    }
    BRANDS {
        int id PK
        string name
    }
    ORDERS {
        int id PK
        int shop_id FK
        int cashier_id FK
        decimal grand_total
        timestamp created_at
    }
    ORDER_ITEMS {
        int id PK
        int order_id FK
        int product_id FK
        int quantity
    }

    SHOPS ||--o{ USERS : "has employees"
    SHOPS }|--|| SUBSCRIPTION_PLANS : "subscribes to"
    SHOPS ||--o{ PRODUCTS : "owns"
    SHOPS ||--o{ CATEGORIES : "defines"
    SHOPS ||--o{ ORDERS : "processes"
    ORDERS ||--o{ ORDER_ITEMS : "contains"
    PRODUCTS ||--o{ ORDER_ITEMS : "listed in"
    CATEGORIES ||--o{ PRODUCTS : "classifies"
    BRANDS ||--o{ PRODUCTS : "manufactures"
```

## Class Diagram (Simplified PHP Structure)

Since we are using "Raw PHP" with an MVC-ish approach, there aren't many strict OOP service classes, but here is the logical structure:

```mermaid
classDiagram
    class Database {
        +mysqli conn
        +query(sql, params, types)
        +getLastId()
    }

    class AuthController {
        +login()
        +registerShop()
    }

    class User {
        +id
        +username
        +role
        +isAdmin()
    }

    class Product {
        +id
        +name
        +price
        +stock
        +save()
    }

    class Order {
        +id
        +total
        +items[]
        +processPayment()
    }

    Database <|-- AuthController : uses
    Database <|-- Product : uses
    Database <|-- Order : uses
```

## Setup Instructions
1. Import `database.sql` into MariaDB/MySQL.
2. Edit `.env` with your DB credentials.
3. Serve the project files via Apache/Nginx.
4. Login with `admin@pos.com` / `admin123`.
