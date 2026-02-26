# Overview

This project aims to build a **Multi-Tenant CRM MVP** designed for small and medium-sized businesses that need a simple, focused, and production-ready sales management system.

The system allows a **Business Owner** to register both themselves and their company (tenant). After registration, this user is automatically assigned the role of Business Owner and can invite additional users (Salespersons) to join their company.

All data is isolated per tenant using a **single database multi-tenancy architecture**, ensuring strict data separation at the application layer.

The CRM is centered around a **Kanban-based sales pipeline**, where all lead and deal operations happen directly within the Kanban interface. There is no separate "lead management page" in the MVP — the Kanban is the operational core of the system.

### Lead Creation Flow (Inside Kanban)

- A button on the Kanban allows users to create a Lead.
- Clicking it opens a modal:
    - The user searches by email (or unique identifier).
    - If the Lead already exists within the tenant, it is selected and a new Deal is created.
    - If it does not exist, the Lead is created inline and then a Deal is created immediately.
- Leads must be unique within each tenant (enforced by combined unique index such as `tenant_id + email`).

### Deals & Pipeline

Deals are displayed in a Kanban board with **fixed pipeline stages (global for all tenants in MVP)**.

Suggested default pipeline stages:

1. New Lead
2. Contacted
3. Qualified
4. Proposal Sent
5. Negotiation
6. Won
7. Lost

Each Deal must contain:
- Title
- Monetary value
- Status (based on pipeline stage)
- Owner (Salesperson)
- If marked as Lost → a required loss reason must be stored.

A Lead can have multiple Deals.
The Lead owner automatically becomes the Deal owner by default.

When clicking on a Deal card, users can:
- Edit title and value
- Add notes
- Change stage (drag & drop)
- Mark as Won or Lost
- Access the WhatsApp conversation tab

---

# Tech Stack

- Laravel 12
- PostgreSQL 18
- Livewire v4 (User Panel)
- Multi-tenancy (Single Database Architecture)
- EvolutionAPI v2 (WhatsApp Integration)

---

## Users & Roles (RBAC)

### Business Owner (1–3 users per tenant)

- Full access to all data within their tenant.
- Can manage:
    - Leads
    - Deals
    - Salespersons
    - Assignment rules
    - Company-wide dashboards
- Can reassign Leads and Deals between Salespersons.
- Can connect WhatsApp via QR Code in the Settings screen.
- Can invite new users to the tenant.

### Salesperson (5–50+ users per tenant)

- Can only view and manage Leads and Deals assigned to them.
- Can:
    - Move Deals between pipeline stages.
    - Update Lead data.
    - Add notes.
    - Access WhatsApp tab for their assigned Leads.
- Cannot see other Salespersons’ Leads.
- Cannot manage tenant settings.

---

## Core Workflows

### 1. Company & Owner Registration

- User registers:
    - Company name
    - Personal account credentials
- The first user becomes Business Owner automatically.
- Tenant is created.
- All users are scoped to their tenant.
- Business Owner invites additional users.
- Users can only see other users from their own tenant.

---

### 2. Lead & Deal Management (Kanban-First Approach)

- All Lead creation happens inside Kanban.
- Lead uniqueness enforced at DB level (`tenant_id + email`).
- Business Owner can assign Lead to any Salesperson.
- Salesperson sees only their own Leads.
- Business Owner sees all Leads within the tenant.

---

### 3. WhatsApp Integration (EvolutionAPI v2)

- Business Owner connects WhatsApp via QR Code in Settings.
- Once connected:
    - Salespersons gain access to the WhatsApp tab inside Deal view.
- Conversation screen:
    - Displays chat history via EvolutionAPI.
    - Allows sending messages directly from CRM.
- WhatsApp is tenant-scoped (one connection per tenant for MVP).

---

## Tech Requirements

### Authentication (Production-Ready)

- Secure login with:
    - Rate limiting
    - Session security
    - CSRF protection
- Password reset flow
- Tenant-aware authentication
- Users belong to exactly one tenant

---

### Multi-Tenancy (Single Database, Strict Isolation)

All tenant-related models must:

- Contain `tenant_id`
- Use a shared Tenant Trait that:
    - Applies a global scope to automatically filter by `tenant_id`
    - Automatically fills `tenant_id` on creation
    - Prevents cross-tenant access at model/query level
- Enforce DB-level constraints where possible

Tenant isolation must be enforced in backend logic — never rely solely on UI filtering.

---

### RBAC (Server-Side Enforcement)

- Permissions enforced at controller/action level.
- Salespersons:
    - Can only access Leads/Deals assigned to them.
- Business Owners:
    - Can access all tenant data.
- Authorization checks must not rely on frontend validation.

---

### Mobile-Friendly Interface

- Fully responsive UI.
- Optimized for Salesperson mobile usage:
    - Scrollable Kanban
    - Touch-friendly drag & drop
    - Fast Deal detail navigation
    - Accessible WhatsApp tab
- Clear card design with readable typography and quick actions.
- Must follow the base design guidelines inside `@docs/design`.

---

This MVP focuses on:

- Strong tenant isolation
- Clean RBAC enforcement
- Kanban-first operational simplicity
- WhatsApp-native sales communication
- Production-grade architecture

No advanced reporting, automation rules, or AI features are included in this phase.