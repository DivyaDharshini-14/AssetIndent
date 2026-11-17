# Assetindent Project Guide

## What this application does

This repository is a Laravel 12 web application for managing organizational assets and procurement. It provides a browser-based administration interface, a versioned API, role/permission-controlled workflows, and supporting exports and file handling.

The main business areas visible in the routes, controllers, models, and migrations are:

- **Asset management:** asset catalog and records, assignments/transfers, warranties, documents, maintenance, replacements, imports, depreciation, and reports.
- **Procurement:** indents and approvals, purchase requisitions, RFQs and vendor quotations, purchase orders and payments, goods-received notes (GRNs), and quality inspection.
- **Organization and access:** companies, branches, locations, users, employees, departments, designations, roles, and permissions.
- **Other master data:** vendors, brands, asset categories/types/names, and additional fleet/lead-related lookup data.

The database migrations also contain tables for support tickets and recurring tasks. Treat the migrations as evidence of persisted schema; check the corresponding routes and implementation before assuming a feature is currently exposed or fully operational.

## Request lifecycle

### Browser application

1. The web server sends a request to `public/index.php`.
2. Laravel is configured in `bootstrap/app.php`, which registers `routes/web.php`, `routes/api.php`, `routes/console.php`, and the `/up` health endpoint.
3. A browser route usually enters the `auth` group in `routes/web.php`. Individual business routes add permission middleware such as `permission:assets:view`.
4. A controller validates input (often with a Form Request), coordinates the operation, and uses Eloquent models or a domain service.
5. The controller returns a Blade view, redirect, JSON response, or generated document. Blade templates live in `resources/views`.
6. Vite compiles the browser assets from `resources/` and `resources/assets/`.

### API application

Laravel mounts `routes/api.php` under the `/api` prefix. Within that file, the versioned group adds `/v1`, so its login endpoint is `/api/v1/login`. Most `/api/v1` business routes are inside `auth:sanctum` and `throttle:60,1`; they cover asset and organization data, assignments, maintenance, warranties, documents, permissions, and the asset dashboard.

The same route file also declares `/api/login`, `/api/auth/*`, and `/api/users/*` outside that versioned middleware group. Check the relevant controllers and deployment requirements before treating those endpoints as either public or protected.

## Important workflows

### Asset lifecycle

Asset catalog/master data (for example category, name, type, brand, company, branch, and location) supports the asset records. An asset can then be assigned or transferred, with explicit accept/reject/return actions. Its related documents, warranties, maintenance, and replacement history are handled through separate controllers and views. Asset imports, reports, and depreciation are additional entry points.

### Procurement lifecycle

The route/service structure supports this broad path:

1. A requester creates and submits an **indent**.
2. The approval workflow records each action and advances approval levels. Final indent approval creates a **purchase requisition** (`IndentApprovalService` delegates to `PurchaseRequisitionService`).
3. Procurement processes the requisition through an **RFQ** and collects vendor quotations.
4. A selected quotation can produce a **purchase order**. `PurchaseOrderService` creates the PO transactionally and prevents duplicate POs for the same quotation.
5. The received order is recorded as a **GRN**; quality inspection routes can inspect received items and create asset records from eligible items.
6. Payments, approvals, audit/activity logging, and notifications are represented by dedicated services and models.

The exact permitted transitions depend on current status, configured approval steps, permissions, and service-level checks. When changing a workflow, trace both its route/controller and the service that changes state.

## Repository map

| Path | Purpose |
|---|---|
| `app/Http/Controllers/` | Web and API request handlers, grouped partly by domain. Procurement controllers are also at this level. |
| `app/Http/Requests/` | Form Request validation rules for selected create/update operations. |
| `app/Models/` | Eloquent models for users, assets, organization data, procurement records, and workflow history. |
| `app/Services/` | Business operations that need coordination beyond a single controller/model; procurement has its own service subtree. |
| `app/Enums/` | Typed procurement status, action, level, and item/source values. |
| `app/Http/Middleware/` | Permission and role checks used by route middleware. |
| `app/Helpers/` | Shared helpers. `app/Helpers/Start.php` is autoloaded by Composer and defines shared URL/settings helpers. |
| `routes/web.php` | Browser pages, form actions, and web workflow routes. |
| `routes/api.php` | API login and versioned API routes. |
| `database/migrations/` | Incremental database schema history; this is the source of truth for schema changes. |
| `database/seeders/` and `database/factories/` | Seed data and test/factory data. |
| `resources/views/` | Blade templates for the UI, including asset and procurement pages. |
| `resources/js/`, `resources/css/`, `resources/assets/` | Frontend entry points, styles, and template/vendor assets compiled by Vite. |
| `public/` | Public web entry point and static/build assets. |
| `storage/` | Runtime logs, framework data, and application files. |
| `tests/` | PHPUnit unit and feature tests. |
| `config/` | Laravel and integration configuration; environment-specific values belong in the local environment, not source. |

## Authorization and state management

- The main browser business routes are grouped behind Laravel's `auth` middleware.
- Route-specific `permission` checks are registered in `bootstrap/app.php` and implemented by `app/Http/Middleware/CheckPermission.php`. The middleware supports `|`-separated permission alternatives and a Super Admin bypass.
- Some workflow services also enforce who may take the current step. Route access alone does not necessarily authorize a state transition.
- Procurement services use database transactions for important transitions; inspect those services when modifying approval or document-creation behavior.
- `spatie/laravel-permission` provides the role/permission package, while Sanctum handles API token authentication.

## Running and validating locally

Prerequisites declared by the project include PHP 8.2+, Composer, and Node.js/npm. Configure the local environment and database privately; do not copy credentials into source control.

Typical setup from the repository root:

```powershell
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
# Configure the database and other local settings in .env.
php artisan key:generate # only for a new local environment with no APP_KEY
php artisan migrate
npm ci
```

This preserves an existing `.env` and avoids rotating an existing application key. Then run the combined development processes:

```powershell
composer run dev
```

The Composer `dev` script starts the Laravel server, queue listener, log tail, and Vite development server. To run individual pieces, use `php artisan serve` and `npm run dev`. Build frontend assets with `npm run build`; run the test suite with `php artisan test`.

The default test configuration is in `phpunit.xml`. At the time this guide was written, the visible test files were a small set of examples plus a procurement quality-inspection feature test, so the existing suite does not demonstrate coverage for every workflow.

## Where to start when changing behavior

1. Find the route in `routes/web.php` or `routes/api.php`.
2. Follow it to its controller and any Form Request.
3. For state changes, follow the service and enum involved; check transactions, status guards, approval assignment, and side effects.
4. Check the related Eloquent model and migrations before changing persistence or relationships.
5. Update the Blade/API response and any related frontend assets if the output changes.
6. Add or update a focused test, then run that test and the relevant broader suite.

For route inventory, Laravel can generate a current listing with `php artisan route:list`. Use `php artisan migrate:status` to inspect which migrations are applied in a configured local environment.

## Documentation note

The existing `README.md` originated from the Sneat Laravel admin template and contains template-oriented setup/product information. This guide describes the application-specific structure and workflows instead.
