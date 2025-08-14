# Hospital Management System (HMS)

A complete Hospital Management System built with **Laravel**, **MySQL**, **Blade Templates**, **Bootstrap**, **Custom CSS**, and **Vanilla JavaScript**.  
Designed for hospitals, clinics, and medical centers to manage patients, appointments, prescriptions, billing, lab tests, and more — all in one secure and user-friendly platform.

---

## 🚀 Features

The system is modular and role-based, ensuring only authorized users can access specific functionalities.

### 1. **Authentication & Authorization**

-   Secure login/logout system.
-   Role-based access control for:
    -   **Admin**
    -   **Doctor**
    -   **Nurse**
    -   **Receptionist**
    -   **Lab Staff**
    -   **Pharmacist**
    -   **Accountant**
    -   **Patient**
    -   **Staff** (Default users)
-   Password hashing & Laravel’s built-in CSRF protection.

---

### 2. **Dashboard Module**

**Controller:** `DashboardController.php`

-   Dynamic dashboard for each role.
-   Quick statistics (patients count, appointments today, pending lab tests, invoices, etc.).

---

### 3. **Patient Management**

**Controller:** `PatientController.php`

-   Add, edit, and delete patients.
-   Search patients by name, email, or phone.
-   View detailed patient profiles including medical history, prescriptions, and appointments.

---

### 4. **Appointment Management**

**Controller:** `AppointmentController.php`

-   Schedule, update, and cancel appointments.
-   Filter by doctor or date.
-   Appointment status tracking (Pending, Confirmed, Completed, Cancelled).

---

### 5. **Prescription Management**

**Controller:** `PrescriptionController.php`

-   Doctors can create and update prescriptions for their patients.
-   All authenticated roles (including pharmacists & nurses) can view all prescriptions.
-   Doctor-specific filtering for prescriptions.
-   Patient search and doctor filter on the index view.

---

### 6. **Medication Management**

**Controller:** `MedicationController.php`

-   Manage available medications.
-   Track medicine stock levels.
-   Link medicines to prescriptions.

---

### 7. **Lab Tests**

**Controller:** `LabTestController.php`

-   Record lab test requests.
-   Assign lab tests to patients.
-   Update results and mark tests as completed.

---

### 8. **Pharmacy Inventory Management**

**Controllers:** `MedicationController.php` & `Pharmacy` logic in related modules.

-   Track medicine inventory.
-   Update stock after prescriptions are fulfilled.
-   Alerts for low stock.

---

### 9. **Billing & Invoicing**

**Controller:** `InvoiceController.php`

-   Create invoices for treatments, lab tests, and prescriptions.
-   Track payment status (Paid, Unpaid, Partially Paid).
-   Print and download invoices as PDF.

---

### 10. **Accounting**

**Controllers:**

-   `CashAccountController.php`
-   `CashTransactionController.php`
-   `JournalEntryController.php`
-   `TransactionController.php`

**Features:**

-   Maintain multiple cash accounts.
-   Record incoming & outgoing transactions.
-   Generate accounting journal entries.
-   Financial summaries and transaction history.

---

### 11. **User & Role Management**

**Controller:** `Admin/UserController.php`

-   Admin can create, edit, and delete users.
-   Assign roles to users.
-   Prevent admins from demoting or deleting themselves.
-   Search and filter users by role.

---

### 12. **Account Management**

**Controller:** `AccountController.php`

-   Manage system account settings.
-   Handle personal profile updates for users.

---

## 🗂️ Project Structure

```

app/
Http/
Controllers/
AccountController.php
AppointmentController.php
AuthController.php
CashAccountController.php
CashTransactionController.php
Controller.php
DashboardController.php
InvoiceController.php
JournalEntryController.php
LabTestController.php
MedicationController.php
PatientController.php
PrescriptionController.php
TransactionController.php
Admin/
UserController.php
resources/
views/          \# Blade templates for UI
css/            \# Custom styles
js/             \# Vanilla JavaScript scripts
public/
css/
js/
database/
migrations/     \# Laravel migration files
tests/
Unit/           \# Unit tests for controllers & logic

```

---

## 💻 Tech Stack

-   **Backend:** Laravel (PHP)
-   **Frontend:** Blade, Bootstrap 5, Custom CSS, Vanilla JS
-   **Database:** MySQL
-   **Authentication:** Laravel Auth with role-based access
-   **Testing:** PHPUnit (Unit tests)
-   **Version Control:** Git

---

## ⚙️ Installation

1.  **Clone the Repository**
    ```bash
    git clone [https://github.com/smartadukis/hms.git](https://github.com/smartadukis/hms.git)
    cd hms
    ```

### Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### Run Migrations

```bash
php artisan migrate --seed
```

### Serve the Application

```bash
php artisan serve
```

### Access the Application

Visit `http://127.0.0.1:8000` in your browser.

---

## 🧪 Running Tests

-   Run Unit & Feature tests using:
    ```bash
    php artisan test
    ```
    or
    ```bash
    vendor/bin/phpunit
    ```

---

## Default Roles & Access

| Role             | Description                                    |
| :--------------- | :--------------------------------------------- |
| **Admin**        | Full access to all modules.                    |
| **Doctor**       | Manage appointments, patients, prescriptions.  |
| **Nurse**        | View patients, prescriptions, assist doctors.  |
| **Receptionist** | Handle appointments and patient registrations. |
| **Lab Staff**    | Manage lab tests and results.                  |
| **Pharmacist**   | Manage prescriptions and pharmacy inventory.   |
| **Accountant**   | Manage billing, invoicing, and accounting.     |
| **Patient**      | View own records and appointments.             |
| **Staff**        | General internal staff access.                 |

---

## Key Highlights

-   Modular and scalable architecture.
-   Dynamic settings to turn features on/off for roles (Admin control).
-   Clean UI with responsive design.
-   Secure with CSRF, XSS, and SQL injection protection.
-   Supports real-world hospital workflows.

---

## License

This project is licensed under the MIT License.

---

## Author

Smart Aghadueki

GitHub: [github.com/smartadukis](https://github.com/smartadukis/hms)

```

```
