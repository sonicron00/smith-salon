# Salon Booking App (Laravel 11 + Filament + Laravel Cloud)

A simple, mobile-friendly appointment booking web app for a beauty salon:
- Public booking flow: **Service → Staff → Time → Details**
- Customer manage link: view / reschedule / cancel via secure token
- Admin panel (Filament): services, staff, working hours, time off, appointments, SMS templates, cancellation policy, Instagram settings
- SMS confirmations & reminders (via Laravel Notifications + Twilio channel)
- Designed to deploy on **Laravel Cloud** with **MySQL**

> This repository does **not** include `vendor/`. Run `composer install` locally/CI.

## Requirements
- PHP 8.2+
- Composer
- Node 18+ (for Vite assets)
- MySQL 8+

## Quick start (local)
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm ci
npm run build
php artisan serve
```

## Admin login
This project uses Filament's authentication.
Create your first admin user:
```bash
php artisan make:filament-user
```

## SMS
Set env vars:
- `TWILIO_SID`
- `TWILIO_TOKEN`
- `TWILIO_FROM` (E.164 number from Twilio)

SMS templates are editable in the admin panel under **Settings → Messaging**.

## Reminders
Reminders are sent by a scheduled command `appointments:send-reminders`.
Enable scheduler on Laravel Cloud (or locally run):
```bash
php artisan schedule:work
```

## Instagram feed on landing page
Because Instagram Basic Display API is deprecated, we use an **embed widget** approach:
- In admin: **Settings → Branding → Instagram**
- Paste an embed snippet from a provider (e.g., Behold, Curator, etc.) or use a simple profile link.

## Notes
This is an MVP starter scaffold intended for you to customize.
