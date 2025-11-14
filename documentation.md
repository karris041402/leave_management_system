# Employee Leave Management System: Architecture and Implementation Details

## 1. System Architecture Overview

The Employee Leave Management System is built as a **full-stack web application** following a modern, layered architecture. The design adheres to the client's requirements for a secure, scalable, and maintainable system, separating concerns between the presentation layer (Frontend), the application logic (Backend API), and the data persistence layer (Database).

| Layer | Technology | Architecture | Key Components |
| :--- | :--- | :--- | :--- |
| **Frontend** | HTML5, Tailwind CSS, JavaScript (Vanilla) | Single Page Application (SPA) principles | `index.html`, `js/app.js` |
| **Backend API** | PHP 8.1+, Slim Framework 4 | Object-Oriented Programming (OOP), Model-View-Controller (MVC) | Controllers, Models, Middleware, Composer |
| **Database** | MySQL | Relational Database Management System (RDBMS) | `users`, `leave_types`, `leave_records`, `monthly_summaries` tables |

## 2. Backend API Implementation (PHP/Slim MVC)

The backend is implemented using PHP with the **Slim Framework 4**, providing a lightweight and powerful foundation for building a RESTful API. The structure follows a clean MVC pattern as requested:

*   **`src/models`**: Contains classes (`UserModel`, `LeaveTypeModel`, etc.) responsible for all database interactions using **PHP Data Objects (PDO)** to ensure secure and parameterized queries.
*   **`src/controllers`**: Contains classes (`AuthController`, `LeaveController`, etc.) that handle incoming HTTP requests, process business logic, and prepare the response data.
*   **`src/routes`**: Defines the API endpoints and maps them to the appropriate controller methods.
*   **`src/config`**: Holds configuration files, primarily `dependencies.php`, which uses **PHP-DI** for dependency injection, managing the instantiation of the PDO connection and all Model/Controller classes.

### 2.1. Example API Endpoints

The API exposes the following RESTful endpoints:

| Endpoint | Method | Description | Access Control |
| :--- | :--- | :--- | :--- |
| `/api/setup/seed` | `POST` | Initializes the database with a test admin user and initial leave types. | Public (Should be disabled in production) |
| `/api/auth/login` | `POST` | Authenticates a user and returns a **JSON Web Token (JWT)**. | Public |
| `/api/users` | `GET` | Retrieves a list of all employees. | Authenticated (`admin`, `encoder`) |
| `/api/leave-types` | `GET` | Retrieves all available leave types and their point values. | Authenticated (All roles) |
| `/api/leaves/user/{user_id}/month/{month_year}` | `GET` | Retrieves daily leave records and monthly summary for a specific user and month (e.g., `/api/leaves/user/1/month/2024-09`). | Authenticated (Employee can only view own records) |
| `/api/leaves/save` | `POST` | Saves or updates a batch of daily leave records and the monthly summary. | Authenticated (`admin`, `encoder`) |

## 3. Security Best Practices

Security was a primary consideration in the system's design, focusing on the following key areas:

### 3.1. Secure Communication and Data Handling

*   **HTTPS Enforcement**: The system is designed to run over **HTTPS** (as demonstrated by the exposed public URL) to encrypt all data transmitted between the frontend and the backend, preventing man-in-the-middle attacks.
*   **SQL Injection Prevention**: All database interactions are performed using **PDO with prepared statements**. This ensures that user-supplied data is always treated as data, not executable code, completely mitigating the risk of SQL injection.
*   **Input Validation and Sanitization**: The `App\Utils\Validator` class is used to rigorously validate and sanitize all incoming data (e.g., checking for valid integers, floats, dates, and string lengths) before it is processed or stored in the database.

### 3.2. Authentication and Authorization

*   **JWT Implementation**: Authentication is implemented using **JSON Web Tokens (JWT)**. Upon successful login, the API issues a token containing the user's ID and role.
*   **Secure JWT Storage**: While the backend issues the token, the frontend (`js/app.js`) handles its storage. The best practice is to store the JWT in **`localStorage`** or **`sessionStorage`** and transmit it via the `Authorization: Bearer <token>` header for every API request. **Note**: For maximum security against XSS, a production environment should consider using **HTTP-only cookies** for token storage.
*   **Role-Based Access Control (RBAC)**: The `App\Middleware\AuthMiddleware` verifies the JWT on every protected route. The `App\Middleware\RoleMiddleware` then checks the user's role (`admin`, `encoder`, `employee`) against the required permissions for the specific endpoint, ensuring strict authorization.

### 3.3. Cross-Origin Resource Sharing (CORS)

*   The `App\Middleware\CorsMiddleware` is implemented to allow the frontend (running on a different origin/port) to communicate with the backend API. It is configured to allow all origins (`*`) for development simplicity, but should be restricted to the specific frontend domain in a production environment.

## 4. Interaction Flow (Frontend, Backend, Database)

The system interaction follows a strict client-server model:

1.  **Authentication**: The Frontend sends a username/password to the `/api/auth/login` endpoint. The Backend verifies credentials against the `users` table (using `password_verify` on the stored hash) and returns a JWT.
2.  **API Request**: The Frontend stores the JWT and includes it in the `Authorization` header of all subsequent requests (e.g., fetching leave types).
3.  **Backend Processing**:
    *   The request hits the Slim application.
    *   The `CorsMiddleware` and `AuthMiddleware` execute, validating the request origin and the JWT.
    *   The `RoleMiddleware` checks the user's permissions.
    *   The request is routed to the appropriate Controller (e.g., `LeaveController`).
    *   The Controller uses the appropriate Model (e.g., `LeaveRecordModel`) to interact with the database via the secure PDO connection.
4.  **Data Flow**: The Model retrieves or updates data in the MySQL database. The Controller formats the result as JSON and sends it back to the Frontend.
5.  **Frontend Update**: The Frontend (`js/app.js`) receives the JSON data, updates its internal state (`leaveRecords`), and dynamically re-renders the HTML table using Tailwind CSS for styling.

This separation ensures that the database is never directly exposed to the client, fulfilling the core security requirement.

## 5. Frontend Structure and Functionality

The frontend uses a single `index.html` file with a dedicated `js/app.js` script to manage all interactivity.

*   **Table Replication**: The JavaScript dynamically generates the complex, multi-header table structure based on the provided `table-structure.html` reference.
*   **Leave Encoding**: Dropdown menus are dynamically populated with leave types fetched from the API. An event listener (`handleLeaveChange`) tracks changes in the table cells.
*   **Calculation Logic**: The `calculateAllTotals` function processes the selected leave types and their associated point values to update the summary columns in the table, replicating the client's manual calculation process.
*   **Data Persistence**: The "Save All" button collects all encoded data and sends it to the `/api/leaves/save` endpoint for secure storage.

## 6. Project File Structure

The final project structure is clean and scalable:

```
leave-management-system/
├── backend/
│   ├── .env
│   ├── composer.json
│   ├── composer.lock
│   ├── db_schema.sql
│   ├── public/
│   │   ├── index.php         <-- API Entry Point
│   │   └── .htaccess
│   ├── src/
│   │   ├── config/
│   │   │   └── dependencies.php
│   │   ├── controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── LeaveController.php
│   │   │   ├── SetupController.php
│   │   │   └── UserController.php
│   │   ├── handlers/
│   │   │   └── ErrorHandler.php
│   │   ├── middleware/
│   │   │   ├── AuthMiddleware.php
│   │   │   ├── CorsMiddleware.php
│   │   │   └── RoleMiddleware.php
│   │   ├── models/
│   │   │   ├── BaseModel.php
│   │   │   ├── LeaveRecordModel.php
│   │   │   ├── LeaveTypeModel.php
│   │   │   ├── MonthlySummaryModel.php
│   │   │   └── UserModel.php
│   │   ├── routes/
│   │   │   └── api.php
│   │   └── utils/
│   │       └── Validator.php
│   └── vendor/
└── frontend/
    ├── index.html            <-- Frontend Entry Point
    ├── js/
    │   └── app.js            <-- Frontend Logic
    ├── package.json
    └── package-lock.json
```
