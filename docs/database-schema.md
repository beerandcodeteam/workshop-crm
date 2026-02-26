# Database Schema — Workshop CRM

> **Engine:** PostgreSQL 18 | **Framework:** Laravel 12
>
> Schema defined in [DBML](https://dbml.dbdiagram.io/) format.
> Visualize at [dbdiagram.io](https://dbdiagram.io/) by pasting the DBML block below.

---

## Entity-Relationship Summary

```
tenants 1---* users
tenants 1---* leads
tenants 1---* deals
tenants 1---* invitations
tenants 1---1 whatsapp_connections

roles 1---* users
user_statuses 1---* users

leads *---1 users (owner)
leads 1---* deals

deals *---1 users (owner)
deals *---1 pipeline_stages
deals 1---* deal_notes

deal_notes *---1 users (author)

invitations *---1 users (invited_by)
invitations *---1 invitation_statuses

whatsapp_connections *---1 whatsapp_connection_statuses
```

---

## DBML Schema

```dbml
// ============================================================
// Lookup / Auxiliary Tables
// ============================================================

Table roles {
  id bigint [pk, increment]
  name varchar(50) [not null, unique, note: 'Business Owner, Salesperson']
  created_at timestamp
  updated_at timestamp

  Note: 'User roles. Seeded on deploy — not editable by users.'
}

Table user_statuses {
  id bigint [pk, increment]
  name varchar(50) [not null, unique, note: 'Active, Inactive']
  created_at timestamp
  updated_at timestamp

  Note: 'Account statuses. Controls login access.'
}

Table pipeline_stages {
  id bigint [pk, increment]
  name varchar(100) [not null, unique]
  sort_order integer [not null, unique, note: 'Display order on Kanban board']
  is_terminal boolean [not null, default: false, note: 'true for Won and Lost']
  created_at timestamp
  updated_at timestamp

  Note: 'Fixed Kanban pipeline stages (global for all tenants in MVP). Seeded: New Lead (1), Contacted (2), Qualified (3), Proposal Sent (4), Negotiation (5), Won (6), Lost (7).'
}

Table invitation_statuses {
  id bigint [pk, increment]
  name varchar(50) [not null, unique, note: 'Pending, Accepted, Revoked, Expired']
  created_at timestamp
  updated_at timestamp

  Note: 'Invitation lifecycle statuses.'
}

Table whatsapp_connection_statuses {
  id bigint [pk, increment]
  name varchar(50) [not null, unique, note: 'Connected, Disconnected']
  created_at timestamp
  updated_at timestamp

  Note: 'WhatsApp connection statuses.'
}

// ============================================================
// Core Application Tables
// ============================================================

Table tenants {
  id bigint [pk, increment]
  name varchar(255) [not null, note: 'Company name provided at registration']
  created_at timestamp
  updated_at timestamp

  Note: 'Companies / organizations. Root entity for multi-tenancy. All tenant-scoped tables reference this via tenant_id FK.'
}

Table users {
  id bigint [pk, increment]
  tenant_id bigint [not null]
  role_id bigint [not null]
  user_status_id bigint [not null]
  name varchar(255) [not null]
  email varchar(255) [not null, unique, note: 'Unique globally across all tenants']
  email_verified_at timestamp [null]
  password varchar(255) [not null]
  remember_token varchar(100) [null]
  created_at timestamp
  updated_at timestamp

  indexes {
    tenant_id [name: 'users_tenant_id_index']
  }

  Note: 'All CRM users (Business Owners and Salespersons). Each user belongs to exactly one tenant.'
}

Ref: users.tenant_id > tenants.id [delete: cascade]
Ref: users.role_id > roles.id [delete: restrict]
Ref: users.user_status_id > user_statuses.id [delete: restrict]

Table leads {
  id bigint [pk, increment]
  tenant_id bigint [not null]
  user_id bigint [not null, note: 'Owner — Salesperson or Business Owner']
  name varchar(255) [not null]
  email varchar(255) [not null]
  phone varchar(50) [null]
  created_at timestamp
  updated_at timestamp

  indexes {
    (tenant_id, email) [unique, name: 'leads_tenant_id_email_unique']
    user_id [name: 'leads_user_id_index']
  }

  Note: 'Prospects / contacts. Unique per tenant by email. A Lead can have multiple Deals.'
}

Ref: leads.tenant_id > tenants.id [delete: cascade]
Ref: leads.user_id > users.id [delete: restrict]

Table deals {
  id bigint [pk, increment]
  tenant_id bigint [not null]
  lead_id bigint [not null]
  user_id bigint [not null, note: 'Deal owner — may differ from Lead owner']
  pipeline_stage_id bigint [not null]
  title varchar(255) [not null]
  value decimal(12,2) [not null, default: 0, note: 'Monetary value in BRL']
  loss_reason text [null, note: 'Required when pipeline stage is Lost']
  sort_order integer [not null, default: 0, note: 'Position within a Kanban column']
  created_at timestamp
  updated_at timestamp

  indexes {
    tenant_id [name: 'deals_tenant_id_index']
    lead_id [name: 'deals_lead_id_index']
    user_id [name: 'deals_user_id_index']
    pipeline_stage_id [name: 'deals_pipeline_stage_id_index']
    (tenant_id, pipeline_stage_id, sort_order) [name: 'deals_kanban_order_index']
  }

  Note: 'Sales opportunities displayed as cards on the Kanban board.'
}

Ref: deals.tenant_id > tenants.id [delete: cascade]
Ref: deals.lead_id > leads.id [delete: cascade]
Ref: deals.user_id > users.id [delete: restrict]
Ref: deals.pipeline_stage_id > pipeline_stages.id [delete: restrict]

Table deal_notes {
  id bigint [pk, increment]
  tenant_id bigint [not null]
  deal_id bigint [not null]
  user_id bigint [not null, note: 'Note author']
  body text [not null]
  created_at timestamp
  updated_at timestamp

  indexes {
    deal_id [name: 'deal_notes_deal_id_index']
    tenant_id [name: 'deal_notes_tenant_id_index']
  }

  Note: 'Free-text notes attached to deals. Displayed in reverse chronological order.'
}

Ref: deal_notes.tenant_id > tenants.id [delete: cascade]
Ref: deal_notes.deal_id > deals.id [delete: cascade]
Ref: deal_notes.user_id > users.id [delete: restrict]

Table invitations {
  id bigint [pk, increment]
  tenant_id bigint [not null]
  invited_by_user_id bigint [not null, note: 'Business Owner who sent the invite']
  invitation_status_id bigint [not null]
  email varchar(255) [not null]
  token varchar(255) [not null, unique, note: 'Secure unique token for the invitation link']
  expires_at timestamp [not null]
  created_at timestamp
  updated_at timestamp

  indexes {
    tenant_id [name: 'invitations_tenant_id_index']
    (tenant_id, email) [name: 'invitations_tenant_email_index']
  }

  Note: 'Email invitations for new Salespersons. Token-based, time-limited registration links.'
}

Ref: invitations.tenant_id > tenants.id [delete: cascade]
Ref: invitations.invited_by_user_id > users.id [delete: restrict]
Ref: invitations.invitation_status_id > invitation_statuses.id [delete: restrict]

Table whatsapp_connections {
  id bigint [pk, increment]
  tenant_id bigint [not null, unique, note: 'One connection per tenant (MVP)']
  whatsapp_connection_status_id bigint [not null]
  instance_name varchar(255) [null, note: 'EvolutionAPI instance name']
  instance_id varchar(255) [null, note: 'EvolutionAPI instance ID']
  phone_number varchar(50) [null, note: 'Connected WhatsApp phone number']
  created_at timestamp
  updated_at timestamp

  Note: 'WhatsApp integration via EvolutionAPI v2. One active connection per tenant in MVP.'
}

Ref: whatsapp_connections.tenant_id > tenants.id [delete: cascade]
Ref: whatsapp_connections.whatsapp_connection_status_id > whatsapp_connection_statuses.id [delete: restrict]

// ============================================================
// Laravel Framework Tables
// ============================================================

Table password_reset_tokens {
  email varchar(255) [pk]
  token varchar(255) [not null]
  created_at timestamp [null]

  Note: 'Laravel built-in password reset tokens.'
}

Table sessions {
  id varchar(255) [pk]
  user_id bigint [null]
  ip_address varchar(45) [null]
  user_agent text [null]
  payload text [not null]
  last_activity integer [not null]

  indexes {
    user_id [name: 'sessions_user_id_index']
    last_activity [name: 'sessions_last_activity_index']
  }

  Note: 'Laravel session storage (database driver).'
}

Ref: sessions.user_id > users.id [delete: set null]

Table cache {
  key varchar(255) [pk]
  value text [not null]
  expiration integer [not null]

  Note: 'Laravel cache storage (database driver).'
}

Table cache_locks {
  key varchar(255) [pk]
  owner varchar(255) [not null]
  expiration integer [not null]

  Note: 'Laravel cache atomic locks.'
}

Table jobs {
  id bigint [pk, increment]
  queue varchar(255) [not null]
  payload text [not null]
  attempts smallint [not null]
  reserved_at integer [null]
  available_at integer [not null]
  created_at integer [not null]

  indexes {
    queue [name: 'jobs_queue_index']
  }

  Note: 'Laravel queue jobs (email notifications, async processing).'
}

Table job_batches {
  id varchar(255) [pk]
  name varchar(255) [not null]
  total_jobs integer [not null]
  pending_jobs integer [not null]
  failed_jobs integer [not null]
  failed_job_ids text [not null]
  options text [null]
  cancelled_at integer [null]
  created_at integer [not null]
  finished_at integer [null]

  Note: 'Laravel job batch tracking.'
}

Table failed_jobs {
  id bigint [pk, increment]
  uuid varchar(255) [not null, unique]
  connection text [not null]
  queue text [not null]
  payload text [not null]
  exception text [not null]
  failed_at timestamp [not null, default: `now()`]

  Note: 'Laravel failed queue jobs for inspection and retry.'
}
```

---

## Seed Data

### roles

| id | name |
|----|------|
| 1 | Business Owner |
| 2 | Salesperson |

### user_statuses

| id | name |
|----|------|
| 1 | Active |
| 2 | Inactive |

### pipeline_stages

| id | name | sort_order | is_terminal |
|----|------|-----------|-------------|
| 1 | New Lead | 1 | false |
| 2 | Contacted | 2 | false |
| 3 | Qualified | 3 | false |
| 4 | Proposal Sent | 4 | false |
| 5 | Negotiation | 5 | false |
| 6 | Won | 6 | true |
| 7 | Lost | 7 | true |

### invitation_statuses

| id | name |
|----|------|
| 1 | Pending |
| 2 | Accepted |
| 3 | Revoked |
| 4 | Expired |

### whatsapp_connection_statuses

| id | name |
|----|------|
| 1 | Connected |
| 2 | Disconnected |

---

## Design Notes

1. **Multi-Tenancy** — All domain tables include `tenant_id` with a foreign key to `tenants`. A global scope trait will filter queries automatically. Lookup tables (`roles`, `user_statuses`, `pipeline_stages`, `invitation_statuses`, `whatsapp_connection_statuses`) are global and shared across tenants.

2. **No Enum Columns** — Every categorical field uses a lookup table with a foreign key instead of database enum or string-based enum columns.

3. **Lead Uniqueness** — Enforced at the database level via the composite unique index `leads(tenant_id, email)`.

4. **Deal Ownership vs Lead Ownership** — A Deal's `user_id` can differ from its Lead's `user_id`, allowing Business Owners to reassign individual Deals without changing the Lead owner (US-5.2).

5. **Kanban Ordering** — `deals.sort_order` enables drag-and-drop reordering within a pipeline stage column. The composite index `(tenant_id, pipeline_stage_id, sort_order)` optimizes Kanban queries.

6. **Loss Reason** — Stored as nullable text on `deals`. Application logic enforces it as required when `pipeline_stage_id` points to the "Lost" stage.

7. **Invitation Flow** — Invitations use a unique secure token with an `expires_at` timestamp. Status transitions: Pending → Accepted (on registration), Pending → Revoked (by Business Owner), Pending → Expired (past `expires_at`).

8. **WhatsApp (MVP)** — One connection per tenant enforced by a unique constraint on `whatsapp_connections.tenant_id`. Chat messages are not stored locally — they are fetched from EvolutionAPI v2 on demand.

9. **Soft Deletes** — Not used in MVP. User deactivation is handled via `user_status_id` (Active → Inactive) rather than soft deletes.

10. **Delete Cascading** — Tenant deletion cascades to all owned data. Deal deletion cascades to notes. Lead deletion cascades to deals. Lookup table references use `restrict` to prevent orphaned records.
