# Project Phases — Workshop CRM

> **Status Legend:** [ ] Pending | [x] Completed
>
> Each task includes the automated **Pest feature tests** to be generated as acceptance criteria.

---

## Phase 1: Foundation & Infrastructure

Everything needed before building any features: design system, database, models, and multi-tenancy.

---

### Phase 1.1: Tailwind Theme & Color Palette

Configure the Tailwind CSS theme with the project's design tokens (see `docs/design/padrao_de_cores.png`).

- [ ] Define custom color palette in `resources/css/app.css` using `@theme`:
  - Primary: `#5E81F4`
  - Primary dark: `#1C1D21`
  - Primary grey: `#8181A5`
  - Primary outline: `#5E81F4`
  - Outline: `#F0F0F3`
  - Background light: `#F5F5FA`
  - Background: `#F6F6F6`
  - Background white: `#FFFFFF`
  - Button hover: `#1C1D21` at 10% opacity
  - Secondary yellow: `#F4BE5E`
  - Secondary green: `#7CE7AC`
  - Secondary red: `#FF808B`
  - Secondary purple: `#9698D6`
- [ ] Import the design system font (Inter or Instrument Sans as base) via `@theme`
- [ ] Run `npm run build` to verify compilation

**Tests:**
- None (visual/CSS — verified by build success).

---

### Phase 1.2: Blade UI Components

Create reusable anonymous Blade components matching `docs/design/botoes.png`, `docs/design/formularios.png`, and `docs/design/checkbox.png`.

- [ ] **Button component** (`resources/views/components/button.blade.php`)
  - Variants: `primary`, `outline`, `danger`, `success`
  - States: active, hover, resting, disabled
  - Support for icon slot (left icon)
  - Size prop: `sm`, `md`, `lg`
- [ ] **Input component** (`resources/views/components/input.blade.php`)
  - Text fields with floating/top label style
  - States: default, focus (primary underline), error (red underline + icon), success (green underline + checkmark)
  - Support for left icon slot
  - Integration with `@error` directive
- [ ] **Textarea component** (`resources/views/components/textarea.blade.php`)
- [ ] **Select component** (`resources/views/components/select.blade.php`)
  - Dropdown with item list styling per design
  - Support for icon items variant
- [ ] **Checkbox component** (`resources/views/components/checkbox.blade.php`)
  - States: unchecked, checked, disabled unchecked, disabled checked, error, success
- [ ] **Radio component** (`resources/views/components/radio.blade.php`)
  - States: unchecked, checked, disabled unchecked, disabled checked, error, success
- [ ] **Toggle component** (`resources/views/components/toggle.blade.php`)
  - States: on, off, disabled on, disabled off, error, success
- [ ] **Modal component** (`resources/views/components/modal.blade.php`)
  - Overlay backdrop, centered content, close button
  - Livewire-compatible (open/close via `wire:click`)
  - Title slot + body slot + footer slot
- [ ] **Tag component** (`resources/views/components/tag.blade.php`)
  - Color variants matching design: primary, yellow, green, red, purple, outline
  - Optional close/remove button

**Tests:**
- `tests/Feature/Components/ButtonComponentTest.php` — renders each variant, renders disabled state, renders with icon slot.
- `tests/Feature/Components/InputComponentTest.php` — renders with label, displays error message from validation, renders with icon.
- `tests/Feature/Components/SelectComponentTest.php` — renders options, renders with placeholder.
- `tests/Feature/Components/ModalComponentTest.php` — renders with title and body slots, renders close button.

---

### Phase 1.3: Layouts

Create the two base layouts derived from `docs/design/layout_base_login.png` (guest) and `docs/design/dashboard.png` (authenticated).

- [ ] **Guest layout** (`resources/views/layouts/guest.blade.php`)
  - Two-column split: left form area (white) + right illustration area (blue/primary)
  - Used for: login, registration, password reset, invitation registration
  - Includes `@vite` and `@livewireStyles` / `@livewireScripts`
- [ ] **App layout** (`resources/views/layouts/app.blade.php`)
  - Left sidebar with:
    - Company logo/avatar at top
    - Welcome message with user name
    - Icon-based navigation links (Dashboard, Kanban, Team, Settings)
    - Bottom section for secondary nav
  - Main content area with top bar (page title, search, notifications)
  - Flash message display area using `@session` directive
  - Responsive: sidebar collapses to hamburger menu on mobile
  - Includes `@vite` and `@livewireStyles` / `@livewireScripts`

**Tests:**
- `tests/Feature/Layouts/GuestLayoutTest.php` — guest layout renders without auth, contains Vite assets.
- `tests/Feature/Layouts/AppLayoutTest.php` — app layout requires authentication (redirects to login if guest), renders sidebar navigation, displays authenticated user name.

---

### Phase 1.4: Database Migrations — Lookup Tables

Create migrations for all lookup/auxiliary tables and their seeders. These are global (not tenant-scoped).

- [ ] Migration: `create_roles_table` — id, name (varchar 50, unique), timestamps
- [ ] Migration: `create_user_statuses_table` — id, name (varchar 50, unique), timestamps
- [ ] Migration: `create_pipeline_stages_table` — id, name (varchar 100, unique), sort_order (integer, unique), is_terminal (boolean, default false), timestamps
- [ ] Migration: `create_invitation_statuses_table` — id, name (varchar 50, unique), timestamps
- [ ] Migration: `create_whatsapp_connection_statuses_table` — id, name (varchar 50, unique), timestamps
- [ ] Seeder: `RoleSeeder` — Business Owner, Salesperson
- [ ] Seeder: `UserStatusSeeder` — Active, Inactive
- [ ] Seeder: `PipelineStageSeeder` — New Lead (1), Contacted (2), Qualified (3), Proposal Sent (4), Negotiation (5), Won (6, terminal), Lost (7, terminal)
- [ ] Seeder: `InvitationStatusSeeder` — Pending, Accepted, Revoked, Expired
- [ ] Seeder: `WhatsappConnectionStatusSeeder` — Connected, Disconnected
- [ ] Update `DatabaseSeeder` to call all lookup seeders
- [ ] Run migrations and seeders successfully

**Tests:**
- `tests/Feature/Database/LookupTableSeederTest.php` — after seeding: roles table has 2 records, user_statuses has 2, pipeline_stages has 7 in correct sort order with correct is_terminal flags, invitation_statuses has 4, whatsapp_connection_statuses has 2. Seeders are idempotent (running twice does not duplicate).

---

### Phase 1.5: Database Migrations — Core Tables

Create migrations for the tenant and all tenant-scoped domain tables.

- [ ] Migration: `create_tenants_table` — id, name (varchar 255), timestamps
- [ ] Migration: `modify_users_table` — add tenant_id (FK → tenants, cascade), role_id (FK → roles, restrict), user_status_id (FK → user_statuses, restrict); add index on tenant_id
- [ ] Migration: `create_leads_table` — id, tenant_id (FK → tenants, cascade), user_id (FK → users, restrict), name (varchar 255), email (varchar 255), phone (varchar 50, nullable), timestamps; unique index on (tenant_id, email); index on user_id
- [ ] Migration: `create_deals_table` — id, tenant_id (FK → tenants, cascade), lead_id (FK → leads, cascade), user_id (FK → users, restrict), pipeline_stage_id (FK → pipeline_stages, restrict), title (varchar 255), value (decimal 12,2, default 0), loss_reason (text, nullable), sort_order (integer, default 0), timestamps; indexes on tenant_id, lead_id, user_id, pipeline_stage_id; composite index on (tenant_id, pipeline_stage_id, sort_order)
- [ ] Migration: `create_deal_notes_table` — id, tenant_id (FK → tenants, cascade), deal_id (FK → deals, cascade), user_id (FK → users, restrict), body (text), timestamps; indexes on deal_id, tenant_id
- [ ] Migration: `create_invitations_table` — id, tenant_id (FK → tenants, cascade), invited_by_user_id (FK → users, restrict), invitation_status_id (FK → invitation_statuses, restrict), email (varchar 255), token (varchar 255, unique), expires_at (timestamp), timestamps; indexes on tenant_id, (tenant_id, email)
- [ ] Migration: `create_whatsapp_connections_table` — id, tenant_id (FK → tenants, unique + cascade), whatsapp_connection_status_id (FK → whatsapp_connection_statuses, restrict), instance_name (varchar 255, nullable), instance_id (varchar 255, nullable), phone_number (varchar 50, nullable), timestamps
- [ ] Run all migrations successfully

**Tests:**
- `tests/Feature/Database/CoreTableMigrationTest.php` — all tables exist with correct columns, foreign keys enforce referential integrity (e.g., creating a lead without a valid tenant_id fails), unique constraints enforced (duplicate tenant_id+email on leads fails), cascade deletes work (deleting tenant removes its users/leads/deals).

---

### Phase 1.6: Eloquent Models, Relationships & Factories

Create all domain models with their relationships, casts, fillable attributes, and test factories.

- [ ] **Tenant model** — fillable: name; relationships: `users()` hasMany, `leads()` hasMany, `deals()` hasMany, `invitations()` hasMany, `whatsappConnection()` hasOne
- [ ] **Role model** — fillable: name; relationships: `users()` hasMany
- [ ] **UserStatus model** — fillable: name; relationships: `users()` hasMany
- [ ] **PipelineStage model** — fillable: name, sort_order, is_terminal; casts: is_terminal → boolean; relationships: `deals()` hasMany
- [ ] **InvitationStatus model** — fillable: name; relationships: `invitations()` hasMany
- [ ] **WhatsappConnectionStatus model** — fillable: name; relationships: `whatsappConnections()` hasMany
- [ ] **User model** (update existing) — add to fillable: tenant_id, role_id, user_status_id; relationships: `tenant()` belongsTo, `role()` belongsTo, `userStatus()` belongsTo, `leads()` hasMany, `deals()` hasMany, `dealNotes()` hasMany, `invitationsSent()` hasMany
- [ ] **Lead model** — fillable: tenant_id, user_id, name, email, phone; relationships: `tenant()` belongsTo, `owner()` belongsTo(User), `deals()` hasMany
- [ ] **Deal model** — fillable: tenant_id, lead_id, user_id, pipeline_stage_id, title, value, loss_reason, sort_order; casts: value → decimal:2; relationships: `tenant()` belongsTo, `lead()` belongsTo, `owner()` belongsTo(User), `pipelineStage()` belongsTo, `notes()` hasMany(DealNote)
- [ ] **DealNote model** — fillable: tenant_id, deal_id, user_id, body; relationships: `tenant()` belongsTo, `deal()` belongsTo, `author()` belongsTo(User)
- [ ] **Invitation model** — fillable: tenant_id, invited_by_user_id, invitation_status_id, email, token, expires_at; casts: expires_at → datetime; relationships: `tenant()` belongsTo, `invitedBy()` belongsTo(User), `invitationStatus()` belongsTo
- [ ] **WhatsappConnection model** — fillable: tenant_id, whatsapp_connection_status_id, instance_name, instance_id, phone_number; relationships: `tenant()` belongsTo, `whatsappConnectionStatus()` belongsTo
- [ ] **Factories** for all models: TenantFactory, UserFactory (update), RoleFactory, UserStatusFactory, PipelineStageFactory, InvitationStatusFactory, WhatsappConnectionStatusFactory, LeadFactory, DealFactory, DealNoteFactory, InvitationFactory, WhatsappConnectionFactory

**Tests:**
- `tests/Feature/Models/TenantModelTest.php` — can create tenant, has users/leads/deals/invitations/whatsappConnection relationships.
- `tests/Feature/Models/UserModelTest.php` — belongs to tenant/role/userStatus, has leads/deals relationships, factory works with all states.
- `tests/Feature/Models/LeadModelTest.php` — belongs to tenant/owner, has deals relationship, unique email per tenant enforced.
- `tests/Feature/Models/DealModelTest.php` — belongs to tenant/lead/owner/pipelineStage, has notes relationship, value cast to decimal.
- `tests/Feature/Models/DealNoteModelTest.php` — belongs to deal/author/tenant.
- `tests/Feature/Models/InvitationModelTest.php` — belongs to tenant/invitedBy/invitationStatus, expires_at cast to datetime.
- `tests/Feature/Models/WhatsappConnectionModelTest.php` — belongs to tenant/status, one per tenant enforced.

---

### Phase 1.7: Multi-Tenancy Trait & Scoping

Implement the `BelongsToTenant` trait that auto-scopes all queries and auto-fills `tenant_id` on creation.

- [ ] Create `app/Traits/BelongsToTenant.php`:
  - Add a global scope that filters by `auth()->user()->tenant_id`
  - Boot method: auto-fill `tenant_id` on creating event from authenticated user
  - Prevent cross-tenant access at query level
- [ ] Apply trait to: Lead, Deal, DealNote, Invitation, WhatsappConnection
- [ ] Verify User model has tenant scoping via relationship (not the trait, since User itself needs to be queried across tenants for login)

**Tests:**
- `tests/Feature/Tenancy/TenantScopingTest.php` — authenticated user can only query records from their own tenant; creating a model auto-fills tenant_id; user from Tenant A cannot see Tenant B's leads/deals/invitations; unauthenticated requests do not leak data.
- `tests/Feature/Tenancy/TenantAutoFillTest.php` — creating a Lead while authenticated automatically sets tenant_id to current user's tenant.

---

## Phase 2: Authentication

Login, registration, password reset, and logout. Uses the guest layout.

> **Ref:** US-1.1, US-1.2, US-1.3, US-1.4

---

### Phase 2.1: Company & Owner Registration

> **Ref:** US-1.1

- [ ] Livewire page component: `pages::auth.register`
- [ ] Registration form fields: company name, user name, email, password, password confirmation
- [ ] Livewire Form Object: `App\Livewire\Forms\RegisterForm`
- [ ] Service: `App\Services\RegistrationService` — creates Tenant, creates User with Business Owner role + Active status, in a database transaction
- [ ] Route: `Route::livewire('/register', 'pages::auth.register')->name('register')`
- [ ] On success: authenticate user and redirect to Kanban dashboard
- [ ] Uses guest layout with split design (form left, illustration right)
- [ ] Validation: company name required, user name required, email required + unique, password required + min:8 + confirmed

**Tests:**
- `tests/Feature/Auth/RegistrationTest.php`:
  - can view registration page (GET /register returns 200)
  - can register with valid data (creates Tenant + User with Business Owner role + Active status)
  - user is authenticated after registration
  - user is redirected to kanban after registration
  - registration fails with duplicate email
  - registration fails with short password
  - registration fails without company name
  - registration fails without password confirmation
  - tenant is created with the provided company name

---

### Phase 2.2: Login

> **Ref:** US-1.2

- [ ] Livewire page component: `pages::auth.login`
- [ ] Login form fields: email, password
- [ ] Rate limiting: max 5 attempts per minute (Laravel's built-in throttle)
- [ ] CSRF protection (automatic with Livewire)
- [ ] Route: `Route::livewire('/login', 'pages::auth.login')->name('login')`
- [ ] On success: redirect to Kanban dashboard
- [ ] On failure: display error message in pt-BR
- [ ] Inactive users (user_status = Inactive) cannot log in
- [ ] Uses guest layout

**Tests:**
- `tests/Feature/Auth/LoginTest.php`:
  - can view login page (GET /login returns 200)
  - can log in with valid credentials
  - redirected to kanban after login
  - login fails with invalid credentials and shows error
  - login fails for inactive user
  - login is rate limited after 5 failed attempts
  - authenticated user is redirected away from login page

---

### Phase 2.3: Password Reset

> **Ref:** US-1.3

- [ ] Livewire page component: `pages::auth.forgot-password` — email input, sends reset link
- [ ] Livewire page component: `pages::auth.reset-password` — new password + confirmation
- [ ] Routes: `forgot-password`, `reset-password`
- [ ] Uses Laravel's built-in password reset (Password broker)
- [ ] Reset link valid for 60 minutes
- [ ] On success: redirect to login page with success flash message (pt-BR)
- [ ] Uses guest layout

**Tests:**
- `tests/Feature/Auth/PasswordResetTest.php`:
  - can view forgot password page
  - can request password reset link (email sent)
  - can reset password with valid token
  - reset fails with invalid token
  - reset fails with expired token
  - user is redirected to login after successful reset

---

### Phase 2.4: Logout

> **Ref:** US-1.4

- [ ] Logout action in the app layout sidebar/nav
- [ ] POST route: `/logout` — invalidates session, redirects to login
- [ ] Route: `Route::post('/logout', ...)->name('logout')`

**Tests:**
- `tests/Feature/Auth/LogoutTest.php`:
  - authenticated user can log out
  - session is invalidated after logout
  - user is redirected to login page after logout
  - guest cannot access logout endpoint

---

### Phase 2.5: Auth Middleware & Route Protection

- [ ] Apply `auth` middleware to all app routes (Kanban, Team, Dashboard, Settings)
- [ ] Apply `guest` middleware to login, register, forgot-password routes
- [ ] Configure redirect paths in `bootstrap/app.php`
- [ ] Create middleware to check user is Active (not deactivated) — redirect to login with error if inactive

**Tests:**
- `tests/Feature/Auth/RouteProtectionTest.php`:
  - unauthenticated user is redirected to login when accessing protected routes
  - authenticated user cannot access guest-only routes (login, register)
  - inactive user is logged out and redirected to login

---

## Phase 3: Authorization & Role-Based Access Control

Server-side permission enforcement for Business Owner vs Salesperson.

> **Ref:** US-9.2

---

### Phase 3.1: Authorization Policies

- [ ] **LeadPolicy** — viewAny: Owner sees all, Salesperson sees own; view/update: Owner always, Salesperson only if assigned; create: any authenticated user; assign: Owner only
- [ ] **DealPolicy** — viewAny: Owner sees all, Salesperson sees own; view/update/move: Owner always, Salesperson only if assigned; create: any authenticated user; assign: Owner only
- [ ] **DealNotePolicy** — create: Owner on any deal, Salesperson only on own deals; view: same as deal visibility
- [ ] **InvitationPolicy** — viewAny/create/revoke: Owner only
- [ ] **UserPolicy** — viewAny/deactivate: Owner only; Owner cannot deactivate themselves
- [ ] **WhatsappConnectionPolicy** — manage: Owner only
- [ ] Register policies in `AppServiceProvider` or via auto-discovery

**Tests:**
- `tests/Feature/Authorization/LeadPolicyTest.php` — Business Owner can view all leads, Salesperson can only view assigned leads, Salesperson cannot assign leads.
- `tests/Feature/Authorization/DealPolicyTest.php` — Business Owner can view/edit/move all deals, Salesperson can only view/edit/move own deals, Salesperson cannot reassign deals.
- `tests/Feature/Authorization/DealNotePolicyTest.php` — Business Owner can add notes to any deal, Salesperson can only add notes to own deals.
- `tests/Feature/Authorization/InvitationPolicyTest.php` — Business Owner can manage invitations, Salesperson cannot.
- `tests/Feature/Authorization/UserPolicyTest.php` — Business Owner can view team and deactivate members, Salesperson cannot, Owner cannot deactivate self.
- `tests/Feature/Authorization/WhatsappConnectionPolicyTest.php` — Business Owner can manage WhatsApp, Salesperson cannot.

---

## Phase 4: Team Management

Invite, view, and deactivate team members.

> **Ref:** US-2.1, US-2.2, US-2.3

---

### Phase 4.1: Invite Salesperson

> **Ref:** US-2.1, US-8.1

- [ ] Livewire page component: `pages::team.index` — lists team members and pending invitations
- [ ] Invitation form (modal or inline): email field
- [ ] Service: `App\Services\InvitationService` — creates Invitation with secure token, sets status to Pending, sets expires_at (e.g., 72 hours), dispatches invitation email notification
- [ ] Notification: `App\Notifications\InvitationSentNotification` — email in pt-BR with company name and registration link
- [ ] Business Owner can revoke pending invitations (sets status to Revoked)
- [ ] Authorization: only Business Owner can invite

**Tests:**
- `tests/Feature/Team/InviteTest.php`:
  - Business Owner can view invite form
  - Business Owner can send invitation (creates Invitation record with Pending status, unique token, expires_at)
  - invitation email notification is sent
  - Salesperson cannot send invitations (403)
  - cannot invite already registered email
  - Business Owner can revoke pending invitation
  - revoked invitation link no longer works

---

### Phase 4.2: Invitation Registration

> **Ref:** US-2.1 (invited user flow)

- [ ] Livewire page component: `pages::auth.register-invited` — accepts token via route parameter
- [ ] Route: `Route::livewire('/register/invite/{token}', 'pages::auth.register-invited')->name('register.invited')`
- [ ] Form fields: name, password, password confirmation (email pre-filled from invitation, readonly)
- [ ] Service: `App\Services\InvitationService::acceptInvitation()` — validates token, checks not expired/revoked, creates User with Salesperson role + Active status on the invitation's tenant, updates invitation status to Accepted
- [ ] On success: authenticate and redirect to Kanban

**Tests:**
- `tests/Feature/Team/InvitationRegistrationTest.php`:
  - can view invitation registration page with valid token
  - can register via invitation (creates User with Salesperson role on correct tenant)
  - invitation status updated to Accepted after registration
  - expired invitation returns error
  - revoked invitation returns error
  - already-used invitation returns error
  - user is authenticated and redirected to kanban after registration

---

### Phase 4.3: View Team Members

> **Ref:** US-2.2

- [ ] Team list within `pages::team.index` (same page as invitations)
- [ ] Displays: name, email, role name, status (Active/Inactive)
- [ ] Only users from the same tenant are shown (tenant scoping)
- [ ] Authorization: only Business Owner can access

**Tests:**
- `tests/Feature/Team/ViewTeamTest.php`:
  - Business Owner can view team list
  - team list shows all users from same tenant
  - team list does not show users from other tenants
  - Salesperson cannot access team page (403)

---

### Phase 4.4: Deactivate Team Member

> **Ref:** US-2.3

- [ ] Deactivation action on team list (per user row)
- [ ] Confirmation modal before deactivation
- [ ] Service: `App\Services\UserService::deactivate()` — sets user_status to Inactive
- [ ] Deactivated user cannot log in (enforced by login middleware from Phase 2.5)
- [ ] Owner cannot deactivate themselves
- [ ] Authorization: only Business Owner can deactivate

**Tests:**
- `tests/Feature/Team/DeactivateTest.php`:
  - Business Owner can deactivate a Salesperson
  - deactivated user status is set to Inactive
  - deactivated user cannot log in
  - Business Owner cannot deactivate themselves
  - Salesperson cannot deactivate anyone (403)
  - leads/deals remain in system after deactivation

---

## Phase 5: Kanban Pipeline

The operational core of the CRM — Kanban board with drag-and-drop using Livewire 4's `wire:sort`.

> **Ref:** US-3.1, US-3.2, US-3.3, US-3.4, US-3.5

---

### Phase 5.1: Kanban Board — View

> **Ref:** US-3.1, US-3.2

- [ ] Livewire page component: `pages::kanban.index`
- [ ] Route: `Route::livewire('/kanban', 'pages::kanban.index')->name('kanban.index')`
- [ ] Displays columns for each pipeline stage (loaded from `pipeline_stages` table, ordered by `sort_order`)
- [ ] Each column renders Deal cards showing: title, monetary value (formatted BRL), Lead name, owner (Salesperson name)
- [ ] **Business Owner** sees all Deals within the tenant
- [ ] **Salesperson** sees only Deals assigned to them
- [ ] Eager loading: deals with lead, owner, pipelineStage to prevent N+1
- [ ] Responsive: columns horizontally scrollable on mobile
- [ ] "New Lead" button visible on the board (opens modal — Phase 5.3)
- [ ] Uses app layout

**Tests:**
- `tests/Feature/Kanban/ViewBoardTest.php`:
  - Business Owner can view kanban board with all tenant deals
  - Salesperson can only see their assigned deals
  - Salesperson cannot see deals from other users
  - deals are grouped by pipeline stage columns
  - deal card displays title, value, lead name, owner name
  - pipeline stages are rendered in correct sort order
  - empty board renders all columns with no cards
  - kanban page requires authentication

---

### Phase 5.2: Kanban Drag & Drop (wire:sort)

> **Ref:** US-3.3

- [ ] Implement `wire:sort` with `wire:sort:group="deals"` on each stage column
- [ ] Each column uses `wire:sort:group-id="{{ $stage->id }}"` to identify the target stage
- [ ] Each Deal card uses `wire:sort:item="{{ $deal->id }}"`
- [ ] Handler method: `handleSort($dealId, $position, $stageId)` — updates deal's `pipeline_stage_id` and `sort_order`
- [ ] Service: `App\Services\DealService::moveToStage()` — updates stage + recalculates sort order for affected columns
- [ ] If moved to "Lost" stage: trigger a modal prompting for required loss reason before completing the move
- [ ] Salesperson can only move their own deals (authorization check in handler)
- [ ] `wire:sort:handle` on a drag handle element within the card

**Tests:**
- `tests/Feature/Kanban/DragDropTest.php`:
  - calling handleSort updates deal pipeline_stage_id and sort_order
  - moving deal to Lost stage requires loss_reason (validation error without it)
  - Salesperson can move their own deals
  - Salesperson cannot move another user's deals
  - Business Owner can move any deal
  - sort order is recalculated correctly within a column
  - moving between columns updates the group-id (stage) correctly

---

### Phase 5.3: Create Lead from Kanban

> **Ref:** US-3.4

- [ ] Modal component triggered by "New Lead" button on Kanban board
- [ ] Livewire Form Object: `App\Livewire\Forms\CreateLeadForm`
- [ ] Step 1: Search by email field — checks if Lead already exists in tenant
- [ ] Step 2a (existing Lead found): display Lead info, show Deal creation form (title, value)
- [ ] Step 2b (new Lead): expand form to collect name, email, phone + Deal title and value
- [ ] Service: `App\Services\LeadService::createWithDeal()` — creates Lead (if new) and Deal in transaction
- [ ] New Deal defaults: pipeline_stage = "New Lead", owner = current user, sort_order = 0 (top of column)
- [ ] Lead uniqueness: tenant_id + email enforced
- [ ] After creation: Deal card appears on Kanban in "New Lead" column (UI refreshes)

**Tests:**
- `tests/Feature/Kanban/CreateLeadTest.php`:
  - can search for existing lead by email
  - creating a new lead creates both Lead and Deal records
  - new deal appears in "New Lead" pipeline stage
  - new deal is assigned to the current user
  - lead email is unique per tenant (duplicate fails with validation error)
  - same email in different tenants is allowed
  - lead fields validated (name required, email required + valid format)
  - deal fields validated (title required, value positive number)

---

### Phase 5.4: Create Deal for Existing Lead

> **Ref:** US-3.5

- [ ] When email search finds an existing Lead in the modal (Phase 5.3), user can create a new Deal
- [ ] Deal form: title, monetary value
- [ ] New Deal linked to the existing Lead
- [ ] Lead can have multiple active Deals

**Tests:**
- `tests/Feature/Kanban/CreateDealExistingLeadTest.php`:
  - can create a new deal for an existing lead
  - existing lead can have multiple deals
  - new deal is in "New Lead" stage
  - new deal is assigned to current user
  - lead data is not duplicated

---

## Phase 6: Deal Management

Detail view, editing, status changes, and notes.

> **Ref:** US-4.1, US-4.2, US-4.3, US-4.4, US-4.5

---

### Phase 6.1: View Deal Details

> **Ref:** US-4.1

- [ ] Clicking a Deal card on the Kanban opens a slide-over or modal with full details
- [ ] Livewire component: `App\Livewire\DealDetail` (nested component, not a page)
- [ ] Displays: Deal title, monetary value (BRL), current stage, Lead info (name, email, phone), Deal owner name
- [ ] Tabs: Details, Notes, WhatsApp (WhatsApp tab only visible if WhatsApp is connected for the tenant)
- [ ] Salesperson can only view their own deals (authorization)
- [ ] Business Owner can view any deal

**Tests:**
- `tests/Feature/Deal/ViewDetailTest.php`:
  - Business Owner can view any deal's detail
  - Salesperson can view their own deal's detail
  - Salesperson cannot view another user's deal (403)
  - deal detail displays all required fields (title, value, stage, lead info, owner)
  - WhatsApp tab only visible when tenant has active WhatsApp connection

---

### Phase 6.2: Edit Deal

> **Ref:** US-4.2

- [ ] Editable fields in the deal detail view: title, monetary value
- [ ] Livewire Form Object: `App\Livewire\Forms\EditDealForm`
- [ ] Validation: title required, value must be a positive number
- [ ] Changes reflected on Kanban card after save
- [ ] Authorization: Salesperson can only edit their own deals

**Tests:**
- `tests/Feature/Deal/EditDealTest.php`:
  - can update deal title and value
  - validation fails for empty title
  - validation fails for negative or zero value
  - Salesperson can edit their own deal
  - Salesperson cannot edit another user's deal (403)
  - Business Owner can edit any deal

---

### Phase 6.3: Mark Deal as Won / Lost

> **Ref:** US-4.3, US-4.4

- [ ] "Mark as Won" button in deal detail — moves to Won stage, shows confirmation
- [ ] "Mark as Lost" button in deal detail — opens modal with required loss reason text field, then moves to Lost stage
- [ ] Service: `App\Services\DealService::markAsWon()` and `markAsLost()`
- [ ] Authorization: Salesperson can only mark their own deals

**Tests:**
- `tests/Feature/Deal/MarkWonTest.php`:
  - can mark deal as Won (pipeline_stage changes to Won)
  - Salesperson can mark their own deal as Won
  - Business Owner can mark any deal as Won
  - Salesperson cannot mark another user's deal (403)
- `tests/Feature/Deal/MarkLostTest.php`:
  - can mark deal as Lost with loss reason
  - loss reason is required when marking as Lost (validation error without it)
  - loss_reason is stored in database
  - Salesperson can mark their own deal as Lost
  - Salesperson cannot mark another user's deal (403)

---

### Phase 6.4: Add Note to Deal

> **Ref:** US-4.5

- [ ] Notes tab in deal detail view
- [ ] Text input + "Add" button to create a note
- [ ] Notes displayed in reverse chronological order with author name, date, and time
- [ ] Service: `App\Services\DealNoteService::create()`
- [ ] Authorization: Business Owner can add notes to any deal; Salesperson can only add notes to their own deals

**Tests:**
- `tests/Feature/Deal/AddNoteTest.php`:
  - can add a note to a deal
  - note is associated with current user as author
  - notes are displayed in reverse chronological order
  - note body is required (validation)
  - Business Owner can add notes to any deal
  - Salesperson can add notes to their own deals
  - Salesperson cannot add notes to another user's deals (403)
  - note tenant_id is auto-filled

---

## Phase 7: Lead & Deal Assignment

Reassigning leads and deals between salespersons.

> **Ref:** US-5.1, US-5.2

---

### Phase 7.1: Assign Lead to Salesperson

> **Ref:** US-5.1

- [ ] In the deal detail view: dropdown to select a Salesperson (Business Owner only)
- [ ] Only active Salespersons from the same tenant are listed
- [ ] Service: `App\Services\LeadService::assignTo()` — changes Lead owner and all associated Deal owners
- [ ] Authorization: only Business Owner can assign

**Tests:**
- `tests/Feature/Assignment/AssignLeadTest.php`:
  - Business Owner can assign a lead to a Salesperson
  - assigning a lead also reassigns all associated deals
  - dropdown only shows Salespersons from the same tenant
  - Salesperson cannot assign leads (403)
  - cannot assign to a user from a different tenant

---

### Phase 7.2: Reassign Deal to Different Salesperson

> **Ref:** US-5.2

- [ ] In the deal detail view: dropdown to change Deal owner (Business Owner only)
- [ ] Only active Salespersons from the same tenant are listed
- [ ] Lead owner does NOT change — only the Deal owner changes
- [ ] Service: `App\Services\DealService::reassign()`
- [ ] Authorization: only Business Owner can reassign

**Tests:**
- `tests/Feature/Assignment/ReassignDealTest.php`:
  - Business Owner can reassign a deal to a different Salesperson
  - lead owner does not change when deal is reassigned
  - previous owner can no longer see the deal (Salesperson scoping)
  - new owner can see the deal on their Kanban
  - Salesperson cannot reassign deals (403)

---

## Phase 8: Email Notifications

Queued email notifications for key events.

> **Ref:** US-8.1, US-8.2, US-8.3

---

### Phase 8.1: Invitation Email Notification

> **Ref:** US-8.1

- [ ] Already created in Phase 4.1 — `App\Notifications\InvitationSentNotification`
- [ ] Verify: email in pt-BR, contains company name and registration link
- [ ] Implements `ShouldQueue`

**Tests:**
- (Covered in Phase 4.1 tests — verify notification is sent, contains correct content in pt-BR)

---

### Phase 8.2: New Lead Assigned Notification

> **Ref:** US-8.2

- [ ] Notification: `App\Notifications\LeadAssignedNotification`
- [ ] Triggered when a Lead/Deal is assigned or reassigned to a Salesperson
- [ ] Email contains: Lead name, Deal title, link to the Deal
- [ ] Email in pt-BR
- [ ] Implements `ShouldQueue`

**Tests:**
- `tests/Feature/Notifications/LeadAssignedNotificationTest.php`:
  - notification is sent when lead is assigned to a Salesperson
  - notification is sent when deal is reassigned
  - notification email contains lead name, deal title, and link
  - notification is not sent when Business Owner assigns to themselves

---

### Phase 8.3: Deal Won/Lost Notification

> **Ref:** US-8.3

- [ ] Notification: `App\Notifications\DealOutcomeNotification`
- [ ] Triggered when a Deal is marked as Won or Lost
- [ ] Sent to all Business Owners in the tenant
- [ ] Email contains: Deal title, Lead name, Salesperson name, value, outcome (Won/Lost)
- [ ] If Lost, includes the loss reason
- [ ] Email in pt-BR
- [ ] Implements `ShouldQueue`

**Tests:**
- `tests/Feature/Notifications/DealOutcomeNotificationTest.php`:
  - notification sent to Business Owners when deal is marked Won
  - notification sent to Business Owners when deal is marked Lost
  - Lost notification includes loss reason
  - notification contains deal title, lead name, salesperson name, value
  - notification is not sent to Salespersons

---

## Phase 9: Dashboard

Minimal sales summary for Business Owners.

> **Ref:** US-7.1

---

### Phase 9.1: Sales Summary Dashboard

> **Ref:** US-7.1

- [ ] Livewire page component: `pages::dashboard.index`
- [ ] Route: `Route::livewire('/dashboard', 'pages::dashboard.index')->name('dashboard.index')`
- [ ] Displays summary cards (following `docs/design/dashboard.png` layout):
  - Total number of Leads
  - Total number of active Deals (non-terminal stages)
  - Total value of Won Deals (sum of `value` where stage is Won)
  - Total number of Won Deals
  - Total number of Lost Deals
- [ ] All data scoped to current tenant
- [ ] Computed properties with caching for performance
- [ ] Authorization: Business Owner only (Salesperson redirected to Kanban)

**Tests:**
- `tests/Feature/Dashboard/DashboardTest.php`:
  - Business Owner can view dashboard
  - Salesperson is redirected to kanban (or sees 403)
  - dashboard displays correct total leads count
  - dashboard displays correct active deals count
  - dashboard displays correct won deals value and count
  - dashboard displays correct lost deals count
  - data is scoped to current tenant (does not include other tenants' data)

---

## Phase 10: WhatsApp Integration

Connect WhatsApp via EvolutionAPI v2, view conversations, and send messages.

> **Ref:** US-6.1, US-6.2, US-6.3

---

### Phase 10.1: EvolutionAPI Configuration & Service

- [ ] Config file: `config/services.php` — add `evolution_api` section with base_url, api_key (from env vars)
- [ ] Service: `App\Services\WhatsappService` — wraps EvolutionAPI v2 HTTP client:
  - `createInstance()` — creates a WhatsApp instance
  - `getQrCode()` — fetches QR code for connection
  - `getConnectionStatus()` — checks if connected
  - `disconnect()` — disconnects instance
  - `fetchMessages()` — retrieves chat history for a phone number
  - `sendMessage()` — sends text message to a phone number
- [ ] Environment variables: `EVOLUTION_API_URL`, `EVOLUTION_API_KEY`

**Tests:**
- `tests/Feature/WhatsApp/WhatsappServiceTest.php` — mock HTTP responses from EvolutionAPI: create instance returns instance data, get QR code returns image/base64, fetch messages returns message list, send message returns success. Test error handling for API failures.

---

### Phase 10.2: Connect WhatsApp via QR Code

> **Ref:** US-6.1

- [ ] Livewire page component: `pages::settings.whatsapp` (or section within settings page)
- [ ] Route within settings section, Business Owner only
- [ ] Displays QR code fetched from EvolutionAPI
- [ ] Connection status indicator (Connected / Disconnected)
- [ ] "Disconnect" button if connected
- [ ] Creates/updates `WhatsappConnection` record for the tenant
- [ ] One connection per tenant (MVP)
- [ ] Authorization: Business Owner only

**Tests:**
- `tests/Feature/WhatsApp/ConnectWhatsappTest.php`:
  - Business Owner can view WhatsApp settings page
  - Salesperson cannot access WhatsApp settings (403)
  - QR code is displayed when disconnected (mocked API)
  - connection status updates after successful scan (mocked webhook/polling)
  - Business Owner can disconnect WhatsApp
  - only one connection per tenant

---

### Phase 10.3: View WhatsApp Conversation

> **Ref:** US-6.2

- [ ] WhatsApp tab in the Deal detail view (Phase 6.1)
- [ ] Tab only visible if tenant has an active WhatsApp connection
- [ ] Loads chat history from EvolutionAPI by Lead's phone number
- [ ] Displays messages with: sender, content, timestamp
- [ ] Salesperson can only see conversations for their assigned leads

**Tests:**
- `tests/Feature/WhatsApp/ViewConversationTest.php`:
  - WhatsApp tab visible when tenant has connected WhatsApp
  - WhatsApp tab hidden when no connection exists
  - messages are loaded and displayed (mocked API response)
  - Salesperson can view conversation for their assigned lead
  - Salesperson cannot view conversation for another user's lead (403)

---

### Phase 10.4: Send WhatsApp Message

> **Ref:** US-6.3

- [ ] Text input at the bottom of the WhatsApp conversation tab
- [ ] Sends message via EvolutionAPI using Lead's phone number
- [ ] Sent message appears in conversation immediately
- [ ] Salesperson can only message their assigned leads

**Tests:**
- `tests/Feature/WhatsApp/SendMessageTest.php`:
  - can send a message via WhatsApp (mocked API)
  - message is sent to the lead's phone number
  - Salesperson can send messages to their assigned leads
  - Salesperson cannot send messages to another user's leads (403)
  - message text is required (validation)

---

## Phase 11: Polish & Mobile

Final responsive adjustments and production readiness.

> **Ref:** US-10.1

---

### Phase 11.1: Responsive Kanban on Mobile

> **Ref:** US-10.1

- [ ] Kanban columns horizontally scrollable on mobile (CSS overflow-x-auto)
- [ ] `wire:sort` drag & drop works with touch gestures (Livewire 4 handles this natively)
- [ ] Deal cards: readable typography, clear touch targets (min 44px)
- [ ] Deal detail slide-over is full-screen on mobile
- [ ] Sidebar collapses to hamburger menu on small screens
- [ ] WhatsApp tab usable on mobile

**Tests:**
- `tests/Feature/Mobile/ResponsiveTest.php` — kanban page loads successfully, all components render (functional tests; visual testing is manual).

---

### Phase 11.2: Production Hardening

- [ ] Run `vendor/bin/sail bin pint --dirty --format agent` on all PHP files
- [ ] Verify all tests pass: `vendor/bin/sail artisan test --compact`
- [ ] Run `vendor/bin/sail npm run build` for production assets
- [ ] Verify no N+1 queries on key pages (Kanban, Dashboard, Team)
- [ ] Review all flash messages and validation errors are in pt-BR
- [ ] Verify all routes are named and used with `route()` helper
- [ ] Verify tenant isolation with cross-tenant access attempts

**Tests:**
- Full test suite passes with zero failures.

---

## Appendix: Phase Summary & Dependencies

| Phase | Name | Depends On | Status |
|-------|------|-----------|--------|
| 1.1 | Tailwind Theme & Color Palette | — | Pending |
| 1.2 | Blade UI Components | 1.1 | Pending |
| 1.3 | Layouts | 1.1, 1.2 | Pending |
| 1.4 | Migrations — Lookup Tables | — | Pending |
| 1.5 | Migrations — Core Tables | 1.4 | Pending |
| 1.6 | Models, Relationships & Factories | 1.5 | Pending |
| 1.7 | Multi-Tenancy Trait | 1.6 | Pending |
| 2.1 | Company & Owner Registration | 1.3, 1.7 | Pending |
| 2.2 | Login | 1.3, 1.7 | Pending |
| 2.3 | Password Reset | 1.3, 1.7 | Pending |
| 2.4 | Logout | 2.2 | Pending |
| 2.5 | Auth Middleware & Route Protection | 2.2 | Pending |
| 3.1 | Authorization Policies | 1.6, 2.5 | Pending |
| 4.1 | Invite Salesperson | 3.1 | Pending |
| 4.2 | Invitation Registration | 4.1 | Pending |
| 4.3 | View Team Members | 3.1 | Pending |
| 4.4 | Deactivate Team Member | 4.3 | Pending |
| 5.1 | Kanban Board — View | 3.1 | Pending |
| 5.2 | Kanban Drag & Drop (wire:sort) | 5.1 | Pending |
| 5.3 | Create Lead from Kanban | 5.1 | Pending |
| 5.4 | Create Deal for Existing Lead | 5.3 | Pending |
| 6.1 | View Deal Details | 5.1 | Pending |
| 6.2 | Edit Deal | 6.1 | Pending |
| 6.3 | Mark Deal as Won / Lost | 6.1 | Pending |
| 6.4 | Add Note to Deal | 6.1 | Pending |
| 7.1 | Assign Lead to Salesperson | 6.1, 4.3 | Pending |
| 7.2 | Reassign Deal | 6.1, 4.3 | Pending |
| 8.1 | Invitation Email | 4.1 | Pending |
| 8.2 | New Lead Assigned Notification | 7.1 | Pending |
| 8.3 | Deal Won/Lost Notification | 6.3 | Pending |
| 9.1 | Sales Summary Dashboard | 1.6, 3.1 | Pending |
| 10.1 | EvolutionAPI Configuration & Service | — | Pending |
| 10.2 | Connect WhatsApp via QR Code | 10.1, 3.1 | Pending |
| 10.3 | View WhatsApp Conversation | 10.2, 6.1 | Pending |
| 10.4 | Send WhatsApp Message | 10.3 | Pending |
| 11.1 | Responsive Kanban on Mobile | 5.2 | Pending |
| 11.2 | Production Hardening | All | Pending |
