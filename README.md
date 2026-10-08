# Docustream: Corporate Financial Document Management System

## Overview
Docustream is a secure, multi-tenant B2B platform engineered for robust financial audit management and compliance tracking. The system enforces strict Separation of Duties (SoD) and relies on an event-driven architecture to ensure zero-touch provisioning for internally generated reports and rigorous validation for client-uploaded documents.

## 1. System Architecture
The application is built on **Laravel (PHP 8+)** using a scalable Model-View-Controller (MVC) pattern combined with asynchronous job queues and cloud-native storage.

*   **Multi-Tenancy:** Data isolation is enforced at the database level via `company_id` foreign keys. All primary controllers and policies scope queries locally to the authenticated user's tenant context.
*   **Storage Infrastructure:** Integration with AWS S3 (`Storage::disk('s3')`) ensures secure, decoupled object storage. File access is abstracted through temporary, signed URLs, preventing direct static asset exposure.
*   **Asynchronous Processing:** Heavy financial reports are delegated to background workers (`GenerateHeavyReportJob`) using Redis/Database queues, preventing thread blocking and ensuring high availability during high-demand reporting cycles.

## 2. Core Workflows
The platform implements a bidirectional data flow pipeline:

*   **Top-Down (Zero-Touch Provisioning):** 
    Administrators and Managers trigger system-generated financial audits. The payload is offloaded to background jobs which construct the PDF and deposit it directly into S3. The state transitions to `completed`, allowing clients to securely download the file. Manual injection of external files into this pipeline is prohibited by design to maintain data integrity.
*   **Bottom-Up (Client Compliance Uploads):** 
    Clients upload external fiscal or legal documents. The system captures these payloads via strict validation and ACID-compliant database transactions (`DB::beginTransaction`). Files enter a `pending_review` state, requiring explicit cryptographic or visual validation by a Manager before transitioning to an `approved` state.

## 3. Finite State Machine (FSM)
Document lifecycles are strictly controlled via a defined status machine to eliminate ambiguous states:

| Status | Client Permissions | Manager Permissions | System Context |
| :--- | :--- | :--- | :--- |
| `processing` | Blocked (UI loading state) | Blocked (UI loading state) | Background job compiling heavy payload. |
| `completed` | View / Download | View / Download | System-generated audit ready for consumption. |
| `pending_review` | Read-only (Proof of submission) | Approve / Reject | External document awaiting compliance check. |
| `approved` | View / Download | View / Download | Validated external document. |
| `rejected` | Prompted to re-upload | Re-evaluate | Invalid external document; requires client action. |

## 4. Security & Compliance
*   **Role-Based Access Control (RBAC):** Access is governed by Laravel Gates and Policies. A strict trifecta of roles (`super-admin`, `manager`, `client`) dictates route and action authorization.
*   **Immutable Audit Trails:** Built via an Event-Driven Observer pattern (`ReportObserver`). All lifecycle events (creation, approval, rejection, deletion) automatically generate polymorphic records in the `audit_logs` table. This captures actors, IP addresses, exact timestamps, user agents, and JSON payloads of state changes.
*   **Transactional Integrity:** File uploads utilize database rollbacks (`DB::rollBack`) on failure, ensuring that an S3 transfer failure does not leave phantom records in the PostgreSQL database.

## 5. Technical Stack & Dependencies
*   **Backend:** PHP 8.5, Laravel 13 Framework
*   **Database:** PostgreSQL (Relational integrity, JSONB support for metadata)
*   **Storage:** AWS S3 API (via `league/flysystem-aws-s3-v3`)
*   **Queue Driver:** Redis / Database (for background job dispatching)
* **Frontend:** Blade Templating Engine, Tailwind CSS (Styling), Alpine.js (Lightweight reactive UI components), Vite (Asset bundling)

## 6. Local Development & Asset Compilation
The frontend architecture relies on Vite to bundle Tailwind CSS and Alpine.js. To successfully run the application or deploy it, the Node.js environment must be initialized.

**Local Environment:**
To enable Hot Module Replacement (HMR) and compile assets on the fly during development, run the following commands:

    npm install
    npm run dev

**Production Environment:**
Before deploying to a live server, assets must be minified and versioned:

    npm install
    npm run build
