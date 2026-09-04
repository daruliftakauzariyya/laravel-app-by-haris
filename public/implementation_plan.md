# Implementation Plan: Database Simplification & Specific Table Structure

This plan details the migration steps to refactor the database structure of the Fatwa Management System so that the final database state contains only the specified 8 tables.

---

## User Review Required

> [!IMPORTANT]
> - A complete database backup is recommended before running the schema refactoring migration on staging/production databases.
> - This migration will drop old tables (`questions`, `mufti_answers`, `supporting_references`, `fatwas`) and replace them with the simplified 3-table Core Q&A schema, while ensuring system tables (`users`, `roles`, `madhabs`, `site_settings`, `fatwa_activity_logs`) are structured exactly as directed.

---

## Final Database Schema (Target State)

After running the migration, the database will consist of exactly these tables:

### 1. `users`
- `id` (bigint(20) UNSIGNED)
- `role_id` (bigint(20) UNSIGNED, nullable)
- `name` (varchar(255))
- `despl_name` (varchar(255))
- `email` (varchar(255))
- `email_verified_at` (timestamp, nullable)
- `password` (varchar(255), nullable)
- `google_id` (varchar(255), nullable)
- `avatar` (varchar(255), nullable)
- `gender` (varchar(255), nullable)
- `country` (varchar(255), nullable)
- `mobile` (varchar(255), nullable)
- `country_code` (varchar(255), nullable)
- `address_line_1` (text, nullable)
- `address_line_2` (text, nullable)
- `state` (varchar(255), nullable)
- `district` (varchar(255), nullable)
- `mother_language` (varchar(255), nullable)
- `mufti_sort_order` (smallint(5) UNSIGNED, default '0')
- `madhab_id` (int(11), nullable)
- `profile_completed` (tinyint(1), default '0')
- `is_active` (tinyint(1), default '1')
- `can_check_approve` (tinyint(1), default '0')
- `otp` (varchar(255), nullable)
- `otp_expires_at` (timestamp, nullable)
- `remember_token` (varchar(100), nullable)
- `created_at` (timestamp, nullable)
- `updated_at` (timestamp, nullable)

### 2. `roles`
- `id` (bigint(20) UNSIGNED)
- `name` (varchar(255))
- `guard_name` (varchar(255))
- `created_at` (timestamp, nullable)
- `updated_at` (timestamp, nullable)

### 3. `madhabs`
- `id` (int(11))
- `name` (varchar(255))
- `slug` (varchar(255))
- `created_at` (timestamp, nullable)
- `updated_at` (timestamp, nullable)

### 4. `site_settings`
- `id` (bigint(20) UNSIGNED)
- `key` (varchar(255))
- `value` (longtext, nullable)
- `created_at` (timestamp, nullable)
- `updated_at` (timestamp, nullable)

### 5. `fatwa_activity_logs`
- `id` (bigint(20) UNSIGNED)
- `question_id` (bigint(20) UNSIGNED)
- `fatwa_id` (bigint(20) UNSIGNED, nullable)
- `user_id` (bigint(20) UNSIGNED, nullable)
- `event` (varchar(255))
- `event_label` (varchar(255))
- `notes` (text, nullable)
- `icon` (varchar(255), default 'bi-circle')
- `color` (varchar(50), default 'secondary')
- `event_at` (timestamp, nullable)
- `created_at` (timestamp, nullable)
- `updated_at` (timestamp, nullable)

### 6. `question_and_answer`
- `id` (bigint auto-increment)
- `question_id` (string, unique ID e.g. IFTA-YYYYMMDD-XXXX)
- `user_id` (foreign key to `users` - asker)
- `category_id` (foreign key to `categories`, nullable)
- `madhab_id` (foreign key to `madhabs`, nullable)
- `assigned_mufti_id` (foreign key to `users` - scholar, nullable)
- `title` (string, nullable)
- `body` (longtext HTML, question content)
- `language` (string, default 'English')
- `language_id` (unsigned bigint or integer, nullable)
- `status` (string, e.g. pending, assigned, answered, published)
- `status_id` (unsigned bigint or integer, nullable)
- `answer` (longtext HTML, live approved answer, nullable)
- `draft_answer` (longtext HTML, draft/pending answer, nullable)
- `published_by` (foreign key to `users`, nullable)
- `published_at` (timestamp, nullable)
- `featured_image` (string, nullable)
- `tags` (json, nullable)
- `views` (unsigned integer, default 0)
- `likes` (unsigned integer, default 0)
- `stars` (unsigned integer, default 0)
- `created_at`, `updated_at`

### 7. `references`
- `id` (bigint auto-increment)
- `question_and_answer_id` (foreign key to `question_and_answer`, cascade delete)
- `arabic_text` (longtext)
- `book_name` (string)
- `language` (string, default 'Arabic')
- `sort_order` (integer, default 0)
- `is_draft` (boolean, default false)
- `created_at`, `updated_at`

### 8. `question_status_history`
- `id` (bigint auto-increment)
- `question_and_answer_id` (foreign key to `question_and_answer`, cascade delete)
- `previous_status` (string, nullable)
- `new_status` (string)
- `previous_assigned_mufti_id` (foreign key to users, nullable)
- `new_assigned_mufti_id` (foreign key to users, nullable)
- `user_id` (foreign key to users, actor)
- `remarks` (text, nullable)
- `created_at`, `updated_at`

---

## Proposed Changes

### Database Layer (Migrations)

#### [NEW] [2026_07_05_120000_refactor_to_final_eight_table_schema.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/database/migrations/2026_07_05_120000_refactor_to_final_eight_table_schema.php)
We will create a single migration file that does the following:
1. **Drop old/unneeded tables**: `mufti_answers`, `supporting_references`, `fatwas`, `cache`, `jobs`, and Spatie pivot tables (if any) to clean up.
2. **Alter `users` table**: Add `role_id` and `despl_name` if they do not exist.
3. **Re-create or alter `roles` table** with standard columns (`id`, `name`, `guard_name`). Seed with Admin, Mufti, and Questioner roles.
4. **Re-create or alter `madhabs` table** with columns (`id`, `name`, `slug`). Seed with Hanafi, Shafii, and General/Other madhabs.
5. **Re-create or alter `site_settings` table** and seed defaults.
6. **Re-create or alter `fatwa_activity_logs` table**.
7. **Create `question_and_answer` table**.
8. **Create `references` table**.
9. **Create `question_status_history` table**.
10. **Data Migration**: Move existing question details, answers, supporting references, activity logs, views, and likes into the new schema.

---

### Backend Models

#### [NEW] [QuestionAndAnswer.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Models/QuestionAndAnswer.php)
#### [NEW] [Reference.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Models/Reference.php)
#### [NEW] [QuestionStatusHistory.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Models/QuestionStatusHistory.php)
#### [DELETE] [Question.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Models/Question.php)
#### [DELETE] [MuftiAnswer.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Models/MuftiAnswer.php)
#### [DELETE] [Fatwa.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Models/Fatwa.php)
#### [DELETE] [SupportingReference.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Models/SupportingReference.php)
#### [DELETE] [FatwaActivityLog.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Models/FatwaActivityLog.php)

---

### Controllers & Business Logic

#### [MODIFY] [FatwaController.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Http/Controllers/FatwaController.php)
#### [MODIFY] [HomeController.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Http/Controllers/HomeController.php)
#### [MODIFY] [QuestionAdminController.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Http/Controllers/Admin/QuestionAdminController.php)
#### [MODIFY] [MuftiController.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/app/Http/Controllers/Mufti/MuftiController.php)

---

### Views & Frontend Templates

#### [MODIFY] Blade Views
- [welcome.blade.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/resources/views/welcome.blade.php)
- [home.blade.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/resources/views/home.blade.php)
- [fatwa-show.blade.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/resources/views/fatwa-show.blade.php)
- [fatwa-archive.blade.php](file:///e:/code-from-haris/darulifta-kauzariyya-NEW-2026/laravel-app-by-haris/resources/views/fatwa-archive.blade.php)

---

## Verification Plan

### Automated Tests
- Run `php artisan migrate:fresh` to verify table creation.
- Run `php artisan db:seed` to verify seeding of default values.

### Manual Verification
- Test workflow: Asker submits question ➔ Mufti writes answer + references ➔ Admin approves/publishes ➔ Check views & likes.
