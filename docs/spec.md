# Billify: Project Specification

| | |
| --- | --- |
| **Status** | Ready for build |
| **Date** | 2026-11-06 |
| **Describes** | `main` at commit `d1b2bcb` (Laravel 11.20, Livewire 3.5, Tailwind 3.4) |
| **Rebuild target** | Laravel 13, Livewire 4 (bundled Alpine), hand-written CSS, MySQL through Sail |
| **Purpose** | Portfolio application, deployed in demo mode |

This document has two jobs:

- **Part 1** records what the 2024 application does, so its features are not lost when it is archived.
- **Part 2** defines the rebuild. Where the two differ, Part 2 wins.

Part 1 comes from reading the source. The application was not run for this analysis.

---

# Part 1: Current Application

## 1. Overview

Billify is a personal bill manager. A user records their after-tax income and their recurring bills, and sees how much of each month's income is left, what is due soon, and where the money goes.

The application is a single dashboard plus authentication pages. It has no API, and it does not track payments: a bill is a recurring template, with no record of whether a given month's instance has been paid.

### History

| Period | Stack | Branch today | Branch after the rebuild |
| --- | --- | --- | --- |
| 2019 to 2020 | Laravel with Vue, controllers | `archive` | `archive-2019` |
| July to August 2024 | Laravel 11, Livewire 3, Tailwind | `main` | `archive-2024` |

### Stack

| Layer | Current |
| --- | --- |
| PHP | `^8.2` |
| Framework | Laravel 11.20 |
| Reactive UI | Livewire 3.5.4, with its bundled Alpine |
| CSS | Tailwind 3.4, plus a small `app.css` |
| Charts | `livewire-charts` (ApexCharts) |
| Icons | `blade-ui-kit/blade-heroicons` |
| Database | MySQL 8 through Sail |
| Mail | SMTP through SendGrid |
| Session, cache, queue | `database` drivers |
| Tests | PHPUnit 11 |

## 2. Domain rules

### 2.1 Income

Each user stores one income amount (labelled "Income (After Taxes)") and one pay frequency. The income is the amount per pay period.

| Frequency value | Label | Payments per year |
| --- | --- | --- |
| `1` | Yearly (Annual Salary) | 1 |
| `12` | Monthly (Every Month) | 12 |
| `26` | Bi-weekly (Every Two Weeks) | 26 |
| `52` | Weekly (Every Week) | 52 |

Derived values, each rounded to 2 decimals:

```text
annual     = income × frequency
monthly    = annual / 12
bi-weekly  = annual / 26
weekly     = annual / 52
```

Example: income 100000 at frequency 1 gives $100,000.00 / $8,333.33 / $3,846.15 / $1,923.08.

### 2.2 Bills

A bill has a name, a category, an optional description, an amount, a day of the month, and a recurrence.

**Recurrence** is a set of months, with these rules (shown to the user on the form):

- No months selected: the bill recurs **every** month.
- One or more selected: the bill applies **only** in those months.

**Categories:** Housing, Utilities, Food, Transportation, Clothing, Medical/Health, Insurance, Personal, Debt, Retirement, Education, Savings, Gifts/Donations, Entertainment, Miscellaneous, Other.

### 2.3 Calculations

Using only bills due in the current month:

```text
monthly remainder = monthly income − sum(bill amounts)
bills vs income   = sum(bill amounts) / monthly income × 100   (shown as "12.34%")
```

Currency is always USD, formatted `$1,234.56`.

### 2.4 Due-soon notifications

The dashboard lists bills due between today and today + 5 days, under the heading "You have bills that will be due soon!".

### 2.5 Demo account

A seeded demo user with income of $100,000 yearly and 10 sample bills. The credentials are displayed on the login page, with a note that the data resets daily. A daily job restores the sample data.

## 3. Features

### 3.1 Authentication

| Feature | Behaviour |
| --- | --- |
| **Login** | Email and password. Success goes to the dashboard. |
| **Register** | Name, email, password + confirmation, income, frequency. Redirects to login with a success message. |
| **Forgot password** | Sends a reset link by email. |
| **Reset password** | Sets a new password from the emailed link, then redirects to login. |
| **Logout** | Ends the session and returns to login. |

### 3.2 Dashboard

Top to bottom:

1. **Header:** logo link to home, "My Profile" (opens modal), "Logout".
2. **Alert:** flashed message, due-soon list, or welcome message.
3. **Six summary tiles:** Annual Income, Monthly Income, Bi-Weekly Income, Weekly Income, Monthly Remainder, and the bills-to-income percentage.
4. **Two column charts:**
   - *Income vs Bills:* monthly income against the current month's bill total.
   - *Top Bill Categories:* top 5 categories by current-month total.
5. **"My Monthly Bills" card** with an "Add Bill" button and the bill list.
6. **Footer:** copyright year, Privacy Policy link, "Developed by Chase Terry" link.

### 3.3 Bill list

- **Filters:**
  - Search on bill name, live with a 500 ms debounce.
  - Showing: "Current Month Only" (default) or "All Months".
  - By Category.
  - Reset button restores the defaults.
- **Heading:** "For November 2026" or "For All Months", with "(Filtered)" when search or category is set.
- **Result count:** "N Results".
- **Sort:** by day of month, ascending.
- **Item:** name, then chips for Amount, Category, Day Due, and each selected month; Edit and Delete buttons.
- **Inline delete confirmation:** "Are you sure you want to delete "Name"?" with Yes, Delete / No, Cancel.
- **Totals:** Monthly Income, Bills Total, Monthly Remainder (positive and negative shown in different colours).
- **Empty states:**
  - No filters: "You have not added any bills yet." / "Click the "Add Bill" button above to add a new bill."
  - Filtered: "No results were found." / "Please try a different filter."

### 3.4 Bill form (modal)

- **Fields:** Name\*, Category\*, Description, Amount\*, Day of Month\* (default 1), Monthly Recurrence checkboxes with explanatory copy.
- **After saving,** the modal stays open in edit mode with "Bill saved successfully!".
- **"New Bill" link** clears the form.
- **In edit mode,** a Delete button swaps to a "Confirm Deletion" view.

### 3.5 My Profile (modal)

Three views:

- **Update profile:** name, email, income, frequency.
- **Change password:** new password + confirmation.
- **Delete account:** confirmation screen, then deletes the user and their bills and returns to login.

### 3.6 Static and error pages

- **Privacy policy:** nine short sections.
- **Error pages:** 403, 404, 503.
- **PWA metadata:** `site.webmanifest` and favicons.

### 3.7 Scheduled jobs

Daily demo data reset, and hourly cleanup of expired password reset tokens.

### 3.8 File uploads

There are none. The current application has no file upload feature for any user.

## 4. Data model

### `users`

`id`, `name`, `email` (unique), `email_verified_at`, `password`, `income`, `frequency`, `demo`, `remember_token`, timestamps.

### `bills`

`id`, `user_id` (cascades on delete), `name`, `category`, `description`, `amount`, `day`, twelve month flags (`january` to `december`), timestamps.

### Relationships

`User` has many `Bill`.

## 5. Routes

| Path | Page |
| --- | --- |
| `/` | Dashboard (authenticated) |
| `/login` | Login |
| `/register` | Register |
| `/forgot-password` | Forgot password |
| `/reset-password/{token}` | Reset password |
| `/logout` | Logout |
| `/privacy-policy` | Privacy policy |
| `/up` | Health check |

## 6. Tests and tooling

- 12 feature test files using `RefreshDatabase` against MySQL, covering each Livewire component and both scheduled jobs.
- Local development through Sail. The 2024 deployment scripts and workflow are retired with this branch and are not carried forward.

---

# Part 2: Rebuild

## 7. Goals and non-goals

**Goals**

- Fresh Laravel 13 application with the feature set in Part 1.
- A brand new interface: sleek, modern, and minimal. Nothing from the old visual identity is reused.
- Hand-written CSS only, with light and dark themes.
- Secure by default, with business logic that can be tested without the UI.
- Fewer dependencies.
- Portfolio-ready: one setting puts the application into demo mode.

**Non-goals** (not in the rebuild unless added later)

- Payment tracking, bill history, or email reminders.
- Multi-currency, shared households, native mobile apps, or a public API.
- File uploads of any kind.

## 8. Target stack

Versions were checked against Packagist on 2026-11-06. Use whatever is latest stable at scaffold time.

| Layer | Choice |
| --- | --- |
| PHP | 8.4 minimum. Sail runs the 8.4 runtime, and `config.platform.php` is pinned to 8.4, so dependencies always resolve for the production target. |
| Framework | Laravel 13 |
| Reactive UI | Livewire 4. Alpine ships inside Livewire and **must not** be installed separately. |
| CSS | Plain modern CSS through Vite. No Tailwind, PostCSS plugins, Sass, or component libraries. |
| Charts | Hand-built with Blade, SVG, and CSS. |
| Icons | Local inline SVGs behind one `<x-icon>` Blade component. No icon package. |
| Fonts | One self-hosted variable sans-serif in WOFF2, chosen during the foundation PR. No third-party font requests. |
| Local environment | Laravel Sail, with MySQL and Mailpit containers |
| Database | MySQL 8 everywhere: local, tests, CI, and production |
| Mail | Resend in production (`resend/resend-php`, `MAIL_MAILER=resend`), Mailpit locally |
| Bot protection | Cloudflare Turnstile on registration |
| Tests | Pest, with `laravel/pint` for style |

**Skeleton cleanup.** The Laravel 13 skeleton ships Tailwind by default. Remove `tailwindcss` and `@tailwindcss/vite` from `package.json`, the plugin from `vite.config.js`, the Tailwind directives from `app.css`, and the default `welcome` page.

## 9. Data model

### `users`

Skeleton columns, plus:

| Column | Type | Notes |
| --- | --- | --- |
| `income_cents` | unsigned bigint, default 0 | Per pay period |
| `pay_frequency` | unsigned tinyint, default 12 | Cast to `PayFrequency` enum |
| `timezone` | string, default `UTC` | IANA name, set by the user on their profile |
| `is_demo` | boolean, default false | Never mass assignable |

Income is required at registration but may be 0 later. Zero income is a normal state: "Bills vs Income" shows "N/A".

### `bills`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint PK | |
| `user_id` | FK, cascade on delete, indexed | |
| `name` | string(64) | |
| `category` | string(32), not null | Cast to `BillCategory` enum |
| `description` | string(255), nullable | |
| `amount_cents` | unsigned bigint | |
| `due_day` | unsigned tinyint | 1 to 31 |
| `months` | JSON, nullable | Array of 1 to 12. `null` means every month. |
| timestamps | | |

Index on `(user_id, due_day)`.

**Why JSON for months:** the data is a small set that is always read with the bill, and it replaces twelve columns with one scope. Selecting all twelve months is stored as `null`, so "every month" has one representation.

### Enums and values

- `App\Enums\BillCategory`: the 16 categories from section 2.2, with `label()`.
- `App\Enums\PayFrequency`: `Yearly = 1`, `Monthly = 12`, `BiWeekly = 26`, `Weekly = 52`, with `label()`.
- Money is integer cents everywhere. Inputs accept up to 2 decimals. A single helper does the conversion and formatting.

## 10. Business logic

Keep rules in models and small classes, not in components.

| Unit | Responsibility |
| --- | --- |
| `Bill::scopeDueInMonth(int $month)` | The one definition of "applies this month" |
| `Bill::dueDateIn(CarbonImmutable $month)` | The due date for a month, clamped to the month's last day, so day 31 in February is the 28th or 29th |
| `App\Support\UpcomingBills` | Bills due from today through today + 5, spanning month and year ends and honouring recurrence. Sorted by soonest date, then amount. |
| `App\Support\IncomeSummary` | Annual, monthly, bi-weekly, weekly, remainder, Bills vs Income. Safe for zero income. |
| `App\Support\CategoryTotals` | Top 5 categories for the current month, for the chart |
| `BillPolicy` | `view`, `update`, `delete` are owner only |

### Behaviour decisions

- **Naming:** the percentage tile is labelled **"Bills vs Income"**. The chart keeps its "Income vs Bills" title. Its second column is labelled "Bills".
- **Totals:** the page's summary tiles always use the current month, regardless of filters. The list footer shows the total of the visible bills, and shows income and remainder only when viewing the unfiltered current month.
- **Notification wording:** "due today", "due tomorrow", or "due in N days", with formatted currency.
- **Alerts:** flash messages become dismissible toasts, and the due-soon panel is always visible when it has content. Success messages always use the success style.
- **Timezone:** `APP_TIMEZONE` stays `UTC` and all timestamps are stored in UTC. "Today", "current month", and due dates are always computed in the signed-in user's `timezone`. The profile form offers a searchable list of IANA timezones, and the value is validated with Laravel's `timezone` rule.

## 11. Routes

| Method | Path | Name | Component | Middleware |
| --- | --- | --- | --- | --- |
| GET | `/` | `dashboard` | `pages::dashboard` | `auth` |
| GET | `/login` | `login` | `pages::auth.login` | `guest` |
| GET | `/register` | `register` | `pages::auth.register` | `guest`, `registration.enabled` |
| GET | `/forgot-password` | `password.request` | `pages::auth.forgot-password` | `guest` |
| GET | `/reset-password/{token}` | `password.reset` | `pages::auth.reset-password` | `guest` |
| POST | `/logout` | `logout` | Invokable controller | `auth` |
| GET | `/privacy-policy` | `privacy-policy` | Blade view | |
| GET | `/up` | | Health check | |

Use `Route::livewire()`. The `password.*` names are the ones Laravel's reset notification expects.

## 12. Authentication and security

Use framework primitives in custom Livewire pages. Starter kits are not used because they bring Tailwind and a UI library.

- **Login:** `Auth::attempt()` with a "remember me" checkbox, and `RateLimiter` at 5 attempts per minute by email + IP.
- **Registration:** logs the user in on success. Protected by Turnstile and rate limiting.
- **Password reset:** Laravel's `Password` broker, with hashed tokens, enforced expiry, and built-in throttling. Forgot-password always shows the same neutral message, whether or not the email exists. Schedule `auth:clear-resets` hourly.
- **Password rules:** one `Password::defaults()` definition used everywhere, minimum 8.
- **Sensitive changes:** changing password or email and deleting the account require the current password. Deleting the account also logs out and invalidates the session.
- **Mail:** configuration comes from `config()` only, and messages are queued through the `database` queue.

### Cloudflare Turnstile

- Widget on the registration form, verified server-side against the `siteverify` endpoint through a small custom validation rule. No package.
- `TURNSTILE_SITE_KEY` and `TURNSTILE_SECRET_KEY` come from `.env`. Local and CI environments use Cloudflare's published test keys.
- Registration fails closed if verification fails or Cloudflare cannot be reached.
- The script is loaded only on the registration page, so it never loads in demo mode. Tests use `Http::fake()`.

### Livewire rules (these apply to every component)

1. Any property holding an ID or other server-trusted value is `#[Locked]`, or the component holds the model itself.
2. Every action that takes an ID loads through `auth()->user()->bills()` and authorizes with `BillPolicy`.
3. No client-supplied component names. Modals are purpose-built components.
4. No `{!! !!}` with any user-influenced data. Blade `{{ }}` only.
5. All validation lives in Livewire form objects with the bounds below.

| Field | Rules |
| --- | --- |
| Bill name | required, string, max 64 |
| Category | required, `Rule::enum(BillCategory::class)` |
| Description | nullable, string, max 255 |
| Amount | required, numeric, 0.01 to 9,999,999.99, at most 2 decimals |
| Due day | required, integer, 1 to 31 |
| Months | array of distinct integers, 1 to 12 |
| Income | required, numeric, 0 to 999,999,999.99, at most 2 decimals |
| Pay frequency | required, `Rule::enum(PayFrequency::class)` |
| Timezone | required, `timezone` |
| Email | required, email, max 255, unique (ignoring self on update) |

## 13. Demo mode

A single setting, `DEMO_MODE=true` (exposed as `config('billify.demo_mode')`), prepares the application for the portfolio site. It is off by default.

| Area | With demo mode on |
| --- | --- |
| **Registration** | Disabled. The `registration.enabled` middleware returns 404 for `/register`, and all links to it are hidden. |
| **Login page** | Shows the demo credentials and the note that data resets daily. |
| **Demo user** | Seeded and kept present by `demo:reset`. Credentials come from `.env`. |
| **Daily reset** | Scheduled `demo:reset` restores the demo user's name, email, password, income, frequency, timezone, and sample bills. |

Whenever demo mode is off, public registration is available and the login page shows no demo information.

**Demo user restrictions** apply to any user with `is_demo = true`:

- May not change their password or email, or delete the account. Those controls are hidden, and an `EnsureNotDemo` guard enforces the rule on the server with a friendly message.
- May not request a password reset. The forgot-password page shows the same neutral message, and no mail is sent.
- May freely add, edit, and delete bills and change income, frequency, and timezone. The daily reset restores them.
- **May not upload files.** The application has no uploads today and none are planned. If an upload feature is ever added, it must be blocked for demo users on the server, not only hidden in the UI.

Seeders contain no real people or fixed passwords. Any local account data comes from environment variables, and only outside production.

## 14. Application structure

```text
app/
  Enums/                BillCategory, PayFrequency
  Http/Controllers/     LogoutController
  Http/Middleware/      EnsureRegistrationEnabled, EnsureNotDemo
  Livewire/Forms/       BillForm, ProfileForm
  Models/               Bill, User
  Policies/             BillPolicy
  Rules/                Turnstile
  Support/              IncomeSummary, UpcomingBills, CategoryTotals, Money
  Console/Commands/     DemoResetCommand
config/
  billify.php           demo mode, demo credentials, Turnstile keys
resources/
  css/                  see section 15
  js/app.js             Alpine stores and helpers only
  views/
    layouts/            app, guest
    pages/              dashboard, auth/*
    components/
      ui/               button, card, field, icon, modal, toast, chart-bar, stat-tile, theme-toggle
      bills/            list, item, editor (Livewire)
      profile/          editor (Livewire)
    errors/             403, 404, 419, 500, 503
    mail/               Mailable or notification views
```

Follow Livewire 4's default single-file component format. Disable the `⚡` filename prefix in `config/livewire.php` for simpler paths and tooling. Publish `layouts::app`, and set the auth pages to `layouts::guest`.

**Component boundaries**

- `pages::dashboard` owns the summary, due-soon panel, and charts, and listens for one explicit `bills-changed` event.
- `bills.list` owns filters, using `#[Url]` so filter state survives a reload, and a `#[Computed]` query. Models are not stored in public properties.
- `bills.editor` is opened by an `edit-bill` event carrying an ID, and authorizes on load.
- `profile.editor` holds the profile views.

**Alpine** handles only client-side UI state: modal open and close, delete confirmation views, tab switching, toast timers, theme, and mobile menu. Data and rules stay on the server.

## 15. Design and styling

### Direction

Sleek, modern, and minimal. This is a new identity, and no fonts, colours, illustrations, logos, or media carry over from the old application. New favicons and a new manifest are created to match.

- Generous whitespace, a restrained neutral palette, and a single accent colour. Exact values are set in the foundation PR.
- Hierarchy comes from type and spacing rather than from borders, heavy shadows, or colourful chips.
- Colour carries meaning only: accent for actions, and distinct positive and negative tones for money.
- Subtle, quick motion.

### Login page

A modern background styled entirely in CSS, with no video or image files: for example, slow-moving layered gradients. It adapts to both themes and stays still under `prefers-reduced-motion`.

### Dark mode

Light and dark themes are both first-class from the first PR.

- Theme control with three options: System (default), Light, Dark. The choice is stored in `localStorage`.
- A small inline script in `<head>` applies the theme before first paint, to avoid a flash of the wrong theme.
- Colours are semantic tokens (`--color-surface`, `--color-text`) using `light-dark()` and `color-scheme`. Components never reference a raw colour.
- Both themes must meet the contrast requirements below, and form controls, charts, and the Turnstile widget must follow the active theme.

### CSS architecture

Hand-written, with no utility framework. Modern native CSS is enough: custom properties, cascade layers, nesting, `:has()`, and container queries.

```text
resources/css/
  app.css               @layer order and imports only
  tokens.css            colours, type scale, spacing, radii, shadows, motion
  reset.css             small modern reset
  base.css              element defaults, fonts, focus styles
  layout.css            page shell, grids, stacks
  components/           button, card, field, chip, modal, toast, stat-tile,
                        chart, header, footer, ...
  pages/                login, error
```

**Conventions**

- `@layer reset, tokens, base, layout, components, pages;` so specificity stays predictable and `!important` is unnecessary.
- BEM-style class names: `.bill-item`, `.bill-item__meta`, `.btn--danger`.
- Every colour, size, and duration comes from a token.
- Mobile-first with `min-width` queries.
- Tests assert on visible behaviour, not class names.

**Charts.** Two simple Blade components rendering column bars from computed percentages, with no JavaScript, styled by the same tokens.

### Accessibility baseline

- Modals use the native `<dialog>` element with `showModal()`, for focus trapping, Esc, and the top layer.
- Real `<button>` for actions and real `<a>` for navigation.
- Labels tied to inputs, errors linked with `aria-describedby`, `aria-invalid` on failed fields.
- Visible `:focus-visible` rings, WCAG AA contrast in both themes, and charts with a text equivalent.
- `prefers-reduced-motion` disables transitions and background animation.

### Content changes

There is no support mailbox, so `support@billify.com` is removed everywhere. The privacy policy and error pages instead state that **this is a portfolio application that is not intended for public use**. The privacy policy keeps its other sections, updated to describe what the rebuild does, including its use of Cloudflare Turnstile on registration.

## 16. Testing and CI

### Required coverage (Pest)

- **Unit:** `Bill::dueDateIn()` (clamping, leap year), `dueInMonth` scope, `UpcomingBills` with frozen dates (Dec 28 to Jan 2, Feb 26 to Mar 3, recurrence exclusion), `IncomeSummary` (each frequency, zero income), `Money`.
- **Timezone:** a user whose local date or month differs from UTC sees the correct current month and due dates.
- **Feature:** each page and component, including validation boundaries and all empty states.
- **Authorization:** another user's bill cannot be viewed, edited, or deleted, including by tampering with component state.
- **Security:** rate limits, expired reset token, neutral forgot-password response, Turnstile success and failure.
- **Demo mode:** registration disabled and hidden, credentials shown, demo user restrictions enforced, `demo:reset` restores everything, and all of it absent when demo mode is off.

Freeze time in every date-dependent test. Aim for at least 90% coverage, measured in CI.

### CI (GitHub Actions)

On every pull request and push to `main`: `pint --test`, the Pest suite against a MySQL 8 service container, and `npm run build`.

## 17. Rebuild plan

1. **Commit** this spec to `main`.
2. **Archive and tidy branches.** All of these are on GitHub as well as locally:
   1. Create `archive-2024` from `d1b2bcb` and push it.
   2. Rename `archive` to `archive-2019`.
   3. Delete `production` and `deploy`, which are no longer used. `production` is at `d1b2bcb`, so `archive-2024` preserves it. `deploy` holds only that code plus generated build assets.
3. **Clear `main`:** in a pull request, remove everything except `docs/`. A normal commit rather than an orphan branch keeps history.
4. **Scaffold,** as the first build PR:
   1. `composer create-project laravel/laravel` into a temporary directory, then move it into the repo. The installer will not write into a non-empty directory.
   2. Install Sail with MySQL and Mailpit, using the PHP 8.4 runtime.
   3. Install Livewire and Pest. Strip Tailwind (section 8).
5. **Build,** in reviewable pull requests:
   1. Foundation: tokens, themes, base CSS, layouts, UI components, CI.
   2. Models, enums, support classes, and their tests.
   3. Authentication, including Turnstile.
   4. Dashboard, bill list, and bill editor.
   5. Profile, including timezone.
   6. Demo mode.
   7. Static pages, error pages, and mail.
   8. Accessibility and responsive pass in both themes, then a feature check against Part 1.
6. **Deploy,** once the hosting exists (section 18).

There is no production data, so no import or migration from the old schema is needed.

## 18. Deployment

The hosting has not been set up and is out of scope for the rebuild PRs. None of the old deployment scripts or workflow carry over. Whatever is chosen must provide:

- PHP 8.4 or later and MySQL 8.
- A cron entry for `schedule:run`, for `demo:reset` and `auth:clear-resets`.
- A queue worker for mail.
- A Resend API key and verified sending domain, and Turnstile keys if demo mode is off.
- `DEMO_MODE=true` on the portfolio deployment.

## 19. Decisions

| Topic | Decision |
| --- | --- |
| Registration | Public, but disabled whenever demo mode is on |
| Demo account | Kept, with daily reset and no file uploads |
| Privacy policy | Kept, updated |
| Registration bot protection | Cloudflare Turnstile |
| Percentage label | "Bills vs Income" |
| Semi-monthly pay | Not added |
| Login background | CSS only |
| Dark mode | Included |
| Existing data | None to keep |
| Timezone | Per user, default UTC |
| Support contact | None; portfolio notice instead |
| Hosting | To be decided; PHP 8.4 or later |
| Mail | Resend |
| Branches | `archive-2024`, `archive-2019`; `production` and `deploy` removed |
| Delivery | Series of pull requests |
| Tests | Pest |
| Database | MySQL everywhere, through Sail locally |
