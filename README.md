# Fatwa App by Haris

A comprehensive charity and fatwa portal built with Laravel 11, PHP 8.x, and PostgreSQL. The application provides an elegant platform for managing and displaying fatwas, taking help requests, managing donations, and dynamically securely updating application configuration.

## Key Features

- **Fatwa Display & Management**: 
  - Rich text support for fatwa questions and answers.
  - Multi-language typography for enhanced readability (Inter, Gulzar, Noto Serif Malayalam).
  - "Print Fatwa" feature for easy distribution.
  - Public facing question form for users.
- **Charity & Community Tools**:
  - Platform to lodge Help Requests and manage Donations.
  - Transparent Ledger interface.
  - "Our Team" presentation and Professional UI Theme (Green & Magenta color palette).
  - Dynamic Hero Slider on the homepage using Swiper.js.
- **Advanced Environment Management**:
  - Secure `.env` file upload and management system.
  - Encrypted storage of environment variables.
  - Strict file type validation and versioning for configuration entries.
  - Comprehensive audit logging for all configuration actions.

## Technology Stack

- **Backend**: Laravel 11 (upgraded for modern ecosystem support)
- **Language**: PHP 8.x
- **Database**: PostgreSQL (requires `pdo_pgsql` extension)
- **Frontend**: Blade templates, Tailwind CSS, Javascript (Swiper.js)

## Getting Started

1. Clone the repository and navigate to the project root.
2. Run `composer install` to install PHP dependencies.
3. Run `npm install` and `npm run build` or `npm run dev` for assets.
4. Copy `.env.example` to `.env` and configure your database settings (ensure your PostgreSQL setup matches).
5. Run migrations using `php artisan migrate`.
6. Start the server via `php artisan serve`.
