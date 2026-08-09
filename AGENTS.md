# AGENTS.md

This file provides guidance for AI assistants working on the HorizontCMS codebase.

## Overview

HorizontCMS is an open-source CMS built on:

- **PHP 8.2+ / Laravel 11** (with a custom `App\HorizontCMS` Application class, not the stock Laravel)
- **Vue 2.6** (Options API, TS components under `resources/vue/ts`)
- **Bootstrap 5.3**, jQuery, CKEditor 4, Select2, SortableJS
- Modular structure: **themes** (`themes/`) and **plugins** (`plugins/`)

## Commands

| Command | Purpose |
|---|---|
| `composer test` | PHPUnit (Unit + Integration suite) |
| `./vendor/bin/phpunit --testsuite Full` | all PHP tests |
| `composer lint` | Pint code style check |
| `./vendor/bin/pint` | Pint formatting |
| `npm run lint` | ESLint on `resources/vue/ts` |
| `npm test` | Vitest (Vue component tests, with coverage) |
| `npm run prod` / `npm run watch` | Laravel Mix build (frontend) |
| `php artisan hcms:install` | CLI installer |
| `php artisan hcms:plugin {--install\|--activate\|...} {plugin}` | plugin management |
| `php artisan hcms:theme {--set} {theme}` | switch theme |

PHPStan (`larastan`, vendor/bin/phpstan) and PhpArchitect (`phparkitect.php`) are available as dev dependencies.

## Directory structure

- `app/Controllers/` — admin + website controllers. **Important:** `App\Controllers` namespace, NOT `App\Http\Controllers`.
- `app/Model/` — Eloquent models (Blogpost, Page, Plugin, Settings, User, UserRole, HeaderImage, ScheduledTask, Visits...). Traits: `Draftable`, `HasAuthor`, `HasImage`, `IsActive`, `PaginateSortAndFilter`.
- `app/Services/` — ThemeEngine (`hcms`), BladeThemeEngine, SpaThemeEngine, Theme, Website, ShortCode, SearchEngine.
- `app/Helpers/` — global helpers (classmap autoload): `Security`, `Html`, `UrlManager`, `SocialLink`, `Functions/link.php`. Legacy helpers like `str_slug`, `studly_case` live here.
- `app/Providers/` — module loader / plugin / theme ServiceProviders.
- `app/Http/Middleware/` — web, admin, website, plugin middleware.
- `app/Console/Commands/` — `hcms:*` artisan commands.
- `routes/` — `web.php` (frontend), `backend.php` (admin), `api.php` (api/v1), `console.php`.
- `config/horizontcms.php` — central config: module dirs, theme engines, languages, `backend_prefix` (default `admin`).
- `resources/vue/ts/` — Vue components (`.vue` + `.ts` + `.spec.ts` trio), `model/`, `environments/`.
- `resources/views/` — admin Blade views.
- `resources/tests/` — **tests live here, not in `tests/`.** Namespace: `Tests\`. Suites: unit, integration, gui.
- `plugins/` — installed plugins.

## Routing (important!)

The CMS uses **legacy, URL-based "magic routing"**:

1. Admin (`routes/backend.php`, prefix `admin`): the `/{controller}/{action}/{args}` catch-all resolves the `App\Controllers\<Controller>Controller` class via `App\Http\RouteResolver` and calls the `{action}` method. The RouteResolver is `@deprecated` but still central.
2. Frontend (`routes/web.php`): the `/{slug?}/{args?}` catch-all first tries the theme (`Theme\<Name>\App\Controllers\`) controllers, falling back to `WebsiteController` on error.
3. `routes/backend.php` dynamically registers every `app/Controllers/*Controller.php` as a resource route, as well as plugin controllers (`/plugin/run/...`).
4. API: `routes/api.php`, prefix `api/v1`, `api` middleware group.

New controller: put it in `app/Controllers/`; the URL automatically becomes `/{lowercase-name}`, and a method automatically `/name/{action}`.

## Theme structure (`themes/<Name>/`)

- `theme_info.xml` (or .yml/.json) — metadata: name, version, `requires.core`.
- `app/Controllers/` — registered as resource routes (`theme.<controller>.*` names).
- `page_templates/` — page templates.
- `routes/web.php`, `routes/api.php` — optional custom routes (`web` middleware).
- `config/*.php` — merged under the `theme:<filename>` key.
- `resources/lang/` — JSON translations.
- Views under the `theme::` namespace (`$theme->getPath().'app/View'` + `resources/views`).

Theme engine selection: `config('horizontcms.theme_engines')` + `HCMS_DEFAULT_THEME_ENGINE` env. Engines: `hcms` (PHP template), `blade`, `spa`.

## Plugin structure (`plugins/<Plugin>/`)

Required elements:

- **`Register.php`** — `Plugin\PluginName\Register`, implements `App\Interfaces\PluginInterface`. Methods: `webRouteOptions`, `apiRouteOptions`, `navigation`, `eventHooks`, `widget`, `injectWebsiteJs`, `injectAdminJs`, `onInstall`, `addProviders`, `addMiddlewares`, `addAliases`, `cliCommands`.
- **`plugin_info.xml`** (or .yml/.json) — name, version, `requires.core`.
- Controllers: `app/Controllers/`, namespace `Plugin\<Name>\App\Controllers\`.
- Views: `app/View/` or `resources/views/`.
- Config: `config/*.php` → `plugin:<root_dir>:<filename>` key.
- Custom routes: `routes/web.php`, `routes/api.php`.
- Activation: `plugins` table (`root_dir`, `active`, `version`). Inactive plugins are not loaded.
- Plugin link helpers: `plugin_link()`, `namespace_to_slug()`, `str_slug()` etc. in `app/Helpers`.

## Models

- `Settings` — key-value store; `Settings::get('theme')`, `Settings::getAll()`.
- `Plugin` — plugin metadata + state; `$plugin->getRegister('...')`, `getPath()`, `getInfo()`.
- `User` / `UserRole` — roles/permissions (Gates, `can:global-authorization`).
- `Blogpost`, `Page`, `HeaderImage`, `BlogpostComment`, `BlogpostCategory`, `ScheduledTask`, `Visits`.

## Frontend conventions

- Vue 2.6, Options API. The `window.vue` global instance (`resources/vue/ts/main.ts`), components registered in `app.ts`.
- HTTP: `axios-observable` (`window.vue.prototype.http`), CSRF + API token from `<head>` meta tags.
- Every component has a `.vue` + `.ts` (logic) + `.spec.ts` (Vitest) trio in `components/<name>/`.
- Build: Laravel Mix (`webpack.mix.js`) into `resources/js` and `resources/css`.
- i18n: `laravel-vue-i18n`, translations in `resources/lang/*.json` (en, de, hu, es).

## Testing

- PHP: `resources/tests/{unit,integration,gui}`, `Tests\` namespace. SQLite `:memory:` (see `phpunit.xml`). `resources/tests/TestCase.php` and `ModelFactory.php` are the bootstrap.
- Frontend: Vitest, `resources/vue/**/*.(test|spec).ts`, coverage into `reports/`.

## Common pitfalls

- DO NOT create a `tests/` directory — PHP tests live under `resources/tests/`.
- The controller namespace is `App\Controllers`, not `App\Http\Controllers`.
- For a new global helper, put it in `app/Helpers/` (classmap) and run `composer dump-autoload`.
- `app/HorizontCMS.php` overrides `publicPath()` and keeps active plugins on `app()->plugins`.
- Theme/plugin version compatibility is checked via `requires.core` + `isCompatibleWithCore()`.