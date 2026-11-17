# Roles and Permissions Guide

This guide describes how role access is represented in the application and what the permission modules cover. It is based on the current routes, role editor, menu configuration, and database seeders. The live database is the final authority for which permissions a user currently has.

## How access is determined

1. **Authentication:** Web routes are inside the `auth` middleware group, so the user must be signed in.
2. **Role assignment:** A user can have one or more roles. Roles receive permissions, and users may also receive permissions directly.
3. **Menu visibility:** `resources/menu/verticalMenu.json` uses permission names to decide whether to show some menu entries. A hidden menu item is not, by itself, proof that the URL is protected.
4. **Route authorization:** Routes using `permission:...` middleware reject requests when the signed-in user lacks the named permission. This is the server-side access check when present.
5. **Data scope:** Some queries further restrict which records the user can see. For example, non-superadmin users are limited to companies assigned through `company_user`; branch visibility can also depend on company and branch assignments.

Grant the least access needed. Assign a role to the correct **user account**, and check both the role's permissions and the user's company/branch assignments where applicable.

## Permission naming and actions

Permissions generally follow `module:action`, for example `asset:create` or `companies:view`. Some older and workflow permissions use dot notation, for example `purchase-order.create` or `quotation.compare`.

Common actions include:

| Action | Typical meaning |
|---|---|
| `view` | Open or read a module's records |
| `create` | Add a record |
| `edit` | Update a record |
| `delete` | Delete a record |
| `approve`, `reject`, `submit`, `send`, `inspect`, `pay`, `compare`, `select-vendor`, etc. | Perform a specific business workflow action |

Having `view` does not automatically imply create, edit, delete, approve, or pay access. Workflow actions should be granted separately where they exist.

The Role editor groups permissions by module and action. Where colon and dot spellings represent aliases for the same action, the editor may display one checkbox and expand the selected permission to its aliases when saving. Do not treat the displayed checkbox count as a simple count of all permission rows.

## Module permission catalogue

The table below summarizes the permission families registered by the current seeders. Exact permission names and available actions can change as modules evolve.

| Module area | Permission families | Typical access controlled |
|---|---|---|
| Dashboard | `dashboard:view`, `dashboard.asset:view`, `procurement.dashboard:view` | General, asset, or procurement dashboards |
| Organization masters | `companies:*`, `branch:*`, `location:*`, `brands:*`, `department:*`, `designation:*`, `vendor:*` | Company, branch, area, brand, department, designation, and vendor records |
| Asset setup | `asset-category:*`, `asset-types:*`, asset-name permissions | Asset categories, types, and name suggestions/records |
| Asset register | `asset:view`, `asset:create`, `asset:edit`, `asset:delete` | Asset records; asset import routes also exist |
| Asset submodules | `asset.assignment:*`, `asset.maintenance:*`, `asset.document:*`, `asset.warranty:*`, `asset.replacement:*`, `asset.assign.bulk:view`, `asset.depreciation:view` | Assignment, maintenance, documents, warranty, replacement, bulk assignment, and depreciation |
| Asset reports | `reports.companywiseassets:view`, `reports.branchwiseassets:view`, `reports.locationwiseassets:view`, `reports.brandwiseassets:view`, `reports.vendorwiseassets:view`, `reports.categorywiseassets:view`, `reports.typewiseassets:view` | Asset reports by organization, area, category, type, or supplier |
| Indents | `indents:*`, `indent.approve.*` | Request creation, editing, submission, approval, rejection, and approval levels |
| Purchase requisitions | `purchase-requisitions:*`, `purchase-requisition.*` | Requisition review, processing, transfer, closing, and printing |
| RFQs | `rfqs:*`, `rfq.*` | RFQ creation, editing, sending, closing, and deletion |
| Quotations | `quotations:*`, `quotation.*` | Enter, view, negotiate, compare, and select vendor quotations |
| Purchase orders | `purchase-orders:*`, `purchase-order.*` | Purchase order viewing, creation, changes, approval, printing, cancellation, and payment actions |
| Goods receipts | `grns:*`, `grn.*` | GRN viewing, creation, editing, approval, and inspection |
| Quality inspections | `quality-inspection.*`, `quality-inspections:*` | View or perform quality inspections |
| Payments | `payment.*`, `payments:*` | Payment view, creation, approval, and payment execution |
| Approvals | `approval-workflows:manage`, `approval-templates:*` | Manage workflows and approval templates |
| Administration | `roles:*`, `permissions:*`, `users:*`, `employees:*` | Roles, permissions, user accounts, and employee accounts |

`*` means the permissions available for that family; it does not mean every action exists for every module. Some additional modules—such as leads, vehicle/dealer setup, and segments—may also have permissions registered separately.

## Seeder-defined role examples

Seeders provide starting role configurations; they are not a guarantee of current production access. The current code includes examples such as:

| Role | Seeder-defined intent |
|---|---|
| `superadmin` / `Super Admin` | Full access to registered permissions in the relevant seeder configuration |
| `admin` / `Admin` | Admin-level permissions; exact breadth can differ by case, database history, and which seeders have run |
| `Purchase Officer` | Procurement work such as requisitions, RFQs, quotations, purchase orders, and GRN review; the seeder explicitly removes GRN creation and payment permissions |
| `Accounts Department`, `Accounts`, `Finance & Accounts` | Payment and procurement reporting access |
| `RFQ Creator` | Create and send RFQs |
| `Quotation Negotiator` | Enter and negotiate quotations |
| `Vendor Selector` | Compare quotations and select the vendor |
| `Quality Inspector`, `Quality Control`, store roles | Quality inspection permissions, where those roles already exist |

### Important seeder caveat

Role seeders can create, synchronize, add, or revoke permissions. For example, `AssetPermissionSeeder` synchronizes a broad permission set to the lowercase `employee` role and only explicitly revokes `approval-workflows:manage`; other seeders subsequently change some roles again. Therefore, do not assume that a role name such as “employee” or “admin” automatically means limited access. Review the saved permissions in the live Role editor or database before assigning it.

## Assigning and checking access

1. Open **Setting → Roles**.
2. Create or edit the role, select only the required modules and actions, and save.
3. Open **Setting → Employee** and assign that role to the intended sign-in account. The separate **Employee Staff** records are not necessarily user login accounts and do not assign a role through the same flow.
4. For organization-scoped dropdowns and records, also assign the required company and branch to the user. A company existing and being active does not necessarily make it visible to every user.
5. Sign in as that user and test the menu and the relevant actions. Test the URL directly as well; menu visibility alone does not establish server-side protection.

## Known authorization checks to verify

- The asset create button is conditionally shown using `asset:create`, but the `Route::resource('asset', ...)` declaration in `routes/web.php` is inside the authenticated route group without a visible per-route `permission:` middleware. Treat the button as a UI visibility rule, not proof that direct asset URLs are permission-protected.
- Several master and asset resource routes are likewise declared in the authenticated group without explicit `permission:` middleware next to the resource declaration. Before relying on role permissions as a security boundary, verify the specific route and controller/service checks for that operation.
- The company dropdown on asset create uses the `Company::visibleToUser(...)->active()` scope. In the current model, only a user with the exact `superadmin` role bypasses the company assignment filter; other users need matching company assignments. `Super Admin` with a space does not match that exact role check.

## Source files

- Role CRUD and permission grouping/aliases: `app/Http/Controllers/RoleController.php`
- Permission CRUD: `app/Http/Controllers/PermissionController.php`
- Route middleware: `routes/web.php`
- Menu visibility configuration: `resources/menu/verticalMenu.json`
- Base role setup: `database/seeders/RolesPermissionSeeder.php`
- Asset and master permissions: `database/seeders/AssetPermissionSeeder.php`
- Procurement permissions: `database/seeders/ProcurementPermissionSeeder.php`
- Additional role grants: `database/seeders/AdminProcurementPermissionSeeder.php`, `PurchaseOfficerSeeder.php`, `AccountsDepartmentSeeder.php`, `QualityInspectionPermissionSeeder.php`, `PaymentModulePermissionSeeder.php`, and `RfqWorkflowRoleSeeder.php`
- Company data visibility: `app/Models/Company.php`
- User role/company assignment flows: `app/Http/Controllers/Employee/EmployeesController.php`
