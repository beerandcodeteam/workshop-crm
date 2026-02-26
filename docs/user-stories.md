# User Stories — Workshop CRM

## Overview

This document contains user stories for **Workshop CRM**, a multi-tenant CRM MVP designed for small and medium-sized businesses that need a simple, focused, and production-ready sales management system.

The system is centered around a **Kanban-based sales pipeline**, where all lead and deal operations happen directly within the Kanban interface.

**User Types:**
- **Visitor** — Unauthenticated user accessing public pages (login, registration)
- **Business Owner** — Full access to all tenant data; manages team, leads, deals, settings (1–3 per tenant)
- **Salesperson** — Can only view and manage leads/deals assigned to them (5–50+ per tenant)

---

## 1. Registration & Authentication

### US-1.1: Company & Owner Registration
**As a** Visitor
**I want to** register my company and personal account
**So that** I can start using the CRM with my team

**Acceptance Criteria:**
- [ ] Registration form collects: company name, user name, email, password, password confirmation
- [ ] Email must be unique across the system
- [ ] Password must meet minimum security requirements (8+ characters)
- [ ] A Tenant is created with the provided company name
- [ ] The user is assigned the Business Owner role automatically
- [ ] User is redirected to the Kanban dashboard after registration
- [ ] All subsequent data is scoped to the created tenant

**Expected Result:** Tenant and Business Owner account created, ready to invite team members and manage the pipeline.

---

### US-1.2: Login
**As a** Registered User
**I want to** log in with email and password
**So that** I can access the CRM

**Acceptance Criteria:**
- [ ] Login form collects: email, password
- [ ] Rate limiting applied to prevent brute force (max 5 attempts per minute)
- [ ] CSRF protection enabled
- [ ] Session security enforced
- [ ] Authentication is tenant-aware (user belongs to exactly one tenant)
- [ ] User redirected to Kanban dashboard on success
- [ ] Error message displayed on invalid credentials

**Expected Result:** User securely authenticated and redirected to their tenant's dashboard.

---

### US-1.3: Password Reset
**As a** Registered User
**I want to** reset my password
**So that** I can regain access to my account

**Acceptance Criteria:**
- [ ] "Forgot password" link available on login page
- [ ] Reset email sent with secure token link
- [ ] Reset link valid for 60 minutes
- [ ] User can define a new password
- [ ] User redirected to login page after successful reset

**Expected Result:** User regains account access securely.

---

### US-1.4: Logout
**As a** Registered User
**I want to** log out of the system
**So that** my session is securely terminated

**Acceptance Criteria:**
- [ ] Logout option visible in the navigation
- [ ] Session invalidated on logout
- [ ] User redirected to login page

**Expected Result:** User session terminated securely.

---

## 2. Team Management

### US-2.1: Invite Salesperson
**As a** Business Owner
**I want to** invite a Salesperson by email
**So that** they can join my tenant and manage assigned leads

**Acceptance Criteria:**
- [ ] Business Owner enters the invitee's email address
- [ ] System sends an invitation email with a registration link
- [ ] Invitation link is unique, secure, and time-limited
- [ ] Invited user registers with: name, password, password confirmation
- [ ] Invited user is automatically assigned to the tenant with Salesperson role
- [ ] Business Owner can see pending invitations
- [ ] Business Owner can revoke pending invitations

**Expected Result:** Salesperson receives an email, completes registration, and joins the tenant.

---

### US-2.2: View Team Members
**As a** Business Owner
**I want to** view all users in my tenant
**So that** I can manage my sales team

**Acceptance Criteria:**
- [ ] List displays all users within the tenant
- [ ] Shows: name, email, role, status (active/inactive)
- [ ] Only users from the same tenant are visible (tenant isolation enforced)

**Expected Result:** Business Owner sees a complete list of team members.

---

### US-2.3: Deactivate Team Member
**As a** Business Owner
**I want to** deactivate a Salesperson account
**So that** they can no longer access the CRM

**Acceptance Criteria:**
- [ ] Deactivation option available per user
- [ ] Deactivated user cannot log in
- [ ] Leads/Deals assigned to deactivated user remain in the system
- [ ] Business Owner can reassign those Leads/Deals
- [ ] Confirmation required before deactivation

**Expected Result:** Salesperson account deactivated; their data preserved for reassignment.

---

## 3. Kanban Pipeline

### US-3.1: View Kanban Board
**As a** Business Owner
**I want to** see all Deals in the pipeline as a Kanban board
**So that** I can monitor the entire sales process at a glance

**Acceptance Criteria:**
- [ ] Kanban displays columns for each pipeline stage:
  1. New Lead
  2. Contacted
  3. Qualified
  4. Proposal Sent
  5. Negotiation
  6. Won
  7. Lost
- [ ] Each Deal displayed as a card showing: title, monetary value, Lead name, owner (Salesperson)
- [ ] All Deals within the tenant are visible to Business Owner
- [ ] Board is responsive and mobile-friendly (scrollable columns, touch-friendly)

**Expected Result:** Business Owner sees full pipeline overview for the tenant.

---

### US-3.2: View Kanban Board (Salesperson)
**As a** Salesperson
**I want to** see only my assigned Deals in the Kanban board
**So that** I can manage my own pipeline

**Acceptance Criteria:**
- [ ] Same Kanban layout as Business Owner view
- [ ] Only Deals assigned to the current Salesperson are visible
- [ ] Salesperson cannot see Deals from other Salespersons
- [ ] Tenant isolation enforced at query level

**Expected Result:** Salesperson sees only their own Deals in the Kanban.

---

### US-3.3: Move Deal Between Stages (Drag & Drop)
**As a** Business Owner or Salesperson
**I want to** drag a Deal card to a different pipeline stage
**So that** I can update the Deal progress quickly

**Acceptance Criteria:**
- [ ] Drag & drop works on desktop and mobile (touch-friendly)
- [ ] Deal stage updated in the database on drop
- [ ] If moved to "Lost", a modal prompts for a required loss reason
- [ ] UI updates without full page reload
- [ ] Salesperson can only move their own Deals

**Expected Result:** Deal stage updated visually and in the database.

---

### US-3.4: Create Lead from Kanban
**As a** Business Owner or Salesperson
**I want to** create a new Lead directly from the Kanban board
**So that** I can quickly add prospects and start a Deal

**Acceptance Criteria:**
- [ ] "New Lead" button visible on the Kanban board
- [ ] Clicking opens a modal with a search-by-email field
- [ ] If the Lead already exists within the tenant:
  - Lead is selected
  - A new Deal is created for the existing Lead
- [ ] If the Lead does not exist:
  - Modal expands to collect: name, email, phone
  - Lead is created inline
  - A new Deal is created immediately
- [ ] Lead uniqueness enforced per tenant (`tenant_id + email` unique index)
- [ ] New Deal appears in the "New Lead" column
- [ ] Deal owner defaults to the current user

**Expected Result:** Lead and Deal created; Deal card appears on the Kanban board.

---

### US-3.5: Create Deal for Existing Lead
**As a** Business Owner or Salesperson
**I want to** create a new Deal for a Lead that already exists
**So that** I can track multiple sales opportunities for the same Lead

**Acceptance Criteria:**
- [ ] When searching by email in the "New Lead" modal, existing Lead is found
- [ ] User can create a new Deal with: title, monetary value
- [ ] New Deal is linked to the existing Lead
- [ ] Lead can have multiple active Deals
- [ ] Deal appears in "New Lead" column

**Expected Result:** New Deal created for an existing Lead without duplicating Lead data.

---

## 4. Deal Management

### US-4.1: View Deal Details
**As a** Business Owner or Salesperson
**I want to** click on a Deal card to see its full details
**So that** I can review and update the Deal information

**Acceptance Criteria:**
- [ ] Clicking a Deal card opens a detail view (modal or slide-over)
- [ ] Detail view displays:
  - Deal title
  - Monetary value
  - Current stage
  - Lead info (name, email, phone)
  - Deal owner (Salesperson name)
  - Notes tab
  - WhatsApp tab (if connected)
- [ ] Salesperson can only view their own Deals

**Expected Result:** Full Deal information displayed with navigation tabs.

---

### US-4.2: Edit Deal
**As a** Business Owner or Salesperson
**I want to** edit a Deal's title and monetary value
**So that** I can keep Deal information accurate

**Acceptance Criteria:**
- [ ] Title and value fields are editable in the Deal detail view
- [ ] Changes saved and reflected on the Kanban card
- [ ] Salesperson can only edit their own Deals
- [ ] Validation: title required, value must be a positive number

**Expected Result:** Deal updated successfully.

---

### US-4.3: Mark Deal as Won
**As a** Business Owner or Salesperson
**I want to** mark a Deal as Won
**So that** it is recorded as a successful sale

**Acceptance Criteria:**
- [ ] "Mark as Won" action available in the Deal detail view
- [ ] Deal moved to "Won" column
- [ ] Deal status updated in database
- [ ] Confirmation required before marking

**Expected Result:** Deal recorded as Won and moved to Won column.

---

### US-4.4: Mark Deal as Lost
**As a** Business Owner or Salesperson
**I want to** mark a Deal as Lost with a reason
**So that** I can track why opportunities were lost

**Acceptance Criteria:**
- [ ] "Mark as Lost" action available in Deal detail view
- [ ] Modal prompts for a required loss reason (text field)
- [ ] Deal moved to "Lost" column
- [ ] Loss reason stored in database
- [ ] Confirmation required before marking

**Expected Result:** Deal recorded as Lost with reason; moved to Lost column.

---

### US-4.5: Add Note to Deal
**As a** Business Owner or Salesperson
**I want to** add notes to a Deal
**So that** I can record important context about the negotiation

**Acceptance Criteria:**
- [ ] Notes tab visible in Deal detail view
- [ ] User can add text notes
- [ ] Notes display with author name, date, and time
- [ ] Notes are listed in reverse chronological order
- [ ] Salesperson can only add notes to their own Deals
- [ ] Business Owner can add notes to any Deal

**Expected Result:** Note saved and visible in the Deal's notes tab.

---

## 5. Lead & Deal Assignment

### US-5.1: Assign Lead to Salesperson
**As a** Business Owner
**I want to** assign a Lead (and its Deals) to a Salesperson
**So that** the right person manages the relationship

**Acceptance Criteria:**
- [ ] Business Owner can select a Salesperson from a dropdown in the Lead/Deal detail view
- [ ] Only Salespersons from the same tenant are listed
- [ ] When a Lead is reassigned, associated Deals are also reassigned to the same Salesperson
- [ ] The new owner sees the Lead/Deals in their Kanban immediately

**Expected Result:** Lead and Deals reassigned to the selected Salesperson.

---

### US-5.2: Reassign Deal to Different Salesperson
**As a** Business Owner
**I want to** reassign a specific Deal to a different Salesperson
**So that** I can redistribute workload

**Acceptance Criteria:**
- [ ] Business Owner can change Deal owner in the Deal detail view
- [ ] Only Salespersons from the same tenant are listed
- [ ] The Lead owner does NOT change (only the Deal owner changes)
- [ ] Previous owner loses visibility of the Deal
- [ ] New owner sees the Deal in their Kanban immediately

**Expected Result:** Deal reassigned; previous owner no longer sees it.

---

## 6. WhatsApp Integration

### US-6.1: Connect WhatsApp via QR Code
**As a** Business Owner
**I want to** connect the company's WhatsApp number via QR Code
**So that** the team can communicate with leads from within the CRM

**Acceptance Criteria:**
- [ ] Settings page has a "Connect WhatsApp" section
- [ ] QR Code displayed from EvolutionAPI v2
- [ ] User scans QR Code with WhatsApp on their phone
- [ ] Connection status displayed (connected/disconnected)
- [ ] One WhatsApp connection per tenant (MVP)
- [ ] Only Business Owner can connect/disconnect WhatsApp

**Expected Result:** WhatsApp connected to the tenant; team can use it from Deal views.

---

### US-6.2: View WhatsApp Conversation
**As a** Business Owner or Salesperson
**I want to** view the WhatsApp chat history with a Lead
**So that** I can review previous conversations

**Acceptance Criteria:**
- [ ] WhatsApp tab visible in Deal detail view (only if WhatsApp is connected)
- [ ] Chat history loaded from EvolutionAPI
- [ ] Messages displayed with sender, content, and timestamp
- [ ] Conversation scoped to the Lead's phone number
- [ ] Salesperson can only see conversations for their assigned Leads

**Expected Result:** Chat history displayed within the Deal view.

---

### US-6.3: Send WhatsApp Message
**As a** Business Owner or Salesperson
**I want to** send a WhatsApp message to a Lead from the CRM
**So that** I can communicate without switching to my phone

**Acceptance Criteria:**
- [ ] Text input field at the bottom of the WhatsApp tab
- [ ] Message sent via EvolutionAPI v2
- [ ] Sent message appears in the conversation immediately
- [ ] Lead's phone number used as the recipient
- [ ] Salesperson can only message their assigned Leads

**Expected Result:** Message sent and displayed in the conversation thread.

---

## 7. Dashboard

### US-7.1: View Sales Summary Dashboard
**As a** Business Owner
**I want to** see a simple summary of sales metrics
**So that** I can understand the overall performance of my team

**Acceptance Criteria:**
- [ ] Dashboard displays:
  - Total number of Leads
  - Total number of active Deals
  - Total value of Won Deals
  - Total number of Won Deals
  - Total number of Lost Deals
- [ ] Data scoped to the current tenant
- [ ] Dashboard accessible from the main navigation

**Expected Result:** Business Owner sees a quick overview of sales performance.

---

## 8. Email Notifications

### US-8.1: Invitation Email
**As a** Salesperson (invited)
**I want to** receive an email with a registration link
**So that** I can join my company's CRM

**Acceptance Criteria:**
- [ ] Email sent when Business Owner creates an invitation
- [ ] Email contains: company name, invitation link
- [ ] Link leads to a registration form pre-filled with the email
- [ ] Email written in Brazilian Portuguese (pt-BR)

**Expected Result:** Invited user receives email and can complete registration.

---

### US-8.2: New Lead Assigned Notification
**As a** Salesperson
**I want to** receive an email when a new Lead is assigned to me
**So that** I can take action promptly

**Acceptance Criteria:**
- [ ] Email sent when a Lead/Deal is assigned or reassigned to a Salesperson
- [ ] Email contains: Lead name, Deal title, link to the Deal
- [ ] Email written in Brazilian Portuguese (pt-BR)

**Expected Result:** Salesperson notified of new assignment via email.

---

### US-8.3: Deal Won/Lost Notification
**As a** Business Owner
**I want to** receive an email when a Deal is marked as Won or Lost
**So that** I can stay informed about outcomes

**Acceptance Criteria:**
- [ ] Email sent to Business Owner(s) when a Deal status changes to Won or Lost
- [ ] Email contains: Deal title, Lead name, Salesperson name, value, outcome
- [ ] If Lost, includes the loss reason
- [ ] Email written in Brazilian Portuguese (pt-BR)

**Expected Result:** Business Owner informed of deal outcomes via email.

---

## 9. Multi-Tenancy & Security

### US-9.1: Tenant Data Isolation
**As a** User
**I want to** be sure that my data is completely isolated from other tenants
**So that** there is no data leakage between companies

**Acceptance Criteria:**
- [ ] All tenant-scoped models contain `tenant_id` column
- [ ] A shared Tenant Trait applies a global scope filtering by `tenant_id`
- [ ] `tenant_id` is automatically filled on model creation
- [ ] Database-level constraints (foreign keys) enforce tenant integrity
- [ ] No cross-tenant data access is possible at the query level
- [ ] Users belong to exactly one tenant

**Expected Result:** Complete data isolation between tenants at the application and database level.

---

### US-9.2: Role-Based Access Control
**As the** System
**I want to** enforce permissions at the server level
**So that** users can only perform actions their role allows

**Acceptance Criteria:**
- [ ] Business Owner:
  - Can view all Leads and Deals within the tenant
  - Can manage team members (invite, deactivate)
  - Can reassign Leads and Deals
  - Can access WhatsApp settings
  - Can view the dashboard
- [ ] Salesperson:
  - Can only view/manage Leads and Deals assigned to them
  - Cannot see other Salespersons' data
  - Cannot manage team or settings
- [ ] Authorization enforced at controller/action level (not just UI)
- [ ] Unauthorized access returns 403

**Expected Result:** Strict RBAC enforced server-side for all actions.

---

## 10. Mobile-Friendly Interface

### US-10.1: Responsive Kanban on Mobile
**As a** Salesperson
**I want to** use the Kanban board on my phone
**So that** I can manage Deals while on the go

**Acceptance Criteria:**
- [ ] Kanban columns are horizontally scrollable on mobile
- [ ] Drag & drop works with touch gestures
- [ ] Deal cards have readable typography and clear CTAs
- [ ] Deal detail view is fully usable on mobile
- [ ] WhatsApp tab works on mobile

**Expected Result:** Full CRM functionality accessible on mobile devices.

---

## Appendix: User Story Status

| ID | Story | Priority | Status |
|----|-------|----------|--------|
| US-1.1 | Company & Owner Registration | High | Pending |
| US-1.2 | Login | High | Pending |
| US-1.3 | Password Reset | Medium | Pending |
| US-1.4 | Logout | High | Pending |
| US-2.1 | Invite Salesperson | High | Pending |
| US-2.2 | View Team Members | Medium | Pending |
| US-2.3 | Deactivate Team Member | Medium | Pending |
| US-3.1 | View Kanban Board (Owner) | High | Pending |
| US-3.2 | View Kanban Board (Salesperson) | High | Pending |
| US-3.3 | Move Deal (Drag & Drop) | High | Pending |
| US-3.4 | Create Lead from Kanban | High | Pending |
| US-3.5 | Create Deal for Existing Lead | High | Pending |
| US-4.1 | View Deal Details | High | Pending |
| US-4.2 | Edit Deal | High | Pending |
| US-4.3 | Mark Deal as Won | High | Pending |
| US-4.4 | Mark Deal as Lost | High | Pending |
| US-4.5 | Add Note to Deal | Medium | Pending |
| US-5.1 | Assign Lead to Salesperson | High | Pending |
| US-5.2 | Reassign Deal | Medium | Pending |
| US-6.1 | Connect WhatsApp (QR Code) | High | Pending |
| US-6.2 | View WhatsApp Conversation | High | Pending |
| US-6.3 | Send WhatsApp Message | High | Pending |
| US-7.1 | Sales Summary Dashboard | Low | Pending |
| US-8.1 | Invitation Email | Medium | Pending |
| US-8.2 | New Lead Assigned Notification | Medium | Pending |
| US-8.3 | Deal Won/Lost Notification | Low | Pending |
| US-9.1 | Tenant Data Isolation | High | Pending |
| US-9.2 | Role-Based Access Control | High | Pending |
| US-10.1 | Responsive Kanban on Mobile | Medium | Pending |
