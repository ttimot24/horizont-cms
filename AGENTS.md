# AGENTS.md

This file provides guidance for AI assistants working on the HorizontCMS codebase.

## Overview

HorizontCMS is an open-source CMS built on:

- **PHP 8.2+ / Laravel 11** (with a custom `App\HorizontCMS` Application class, not the stock Laravel)
- **Vue 2.6** (Options API, TS components under `resources/vue/ts`)
- **Bootstrap 5.3**, jQuery, CKEditor 4, Select2, SortableJS
- Modular structure: **themes** (`themes/`) and **plugins** (`plugins/`)

## Commands

| Command                                                                                                     | Purpose                                                |
| ----------------------------------------------------------------------------------------------------------- | ------------------------------------------------------ |
| `composer test`                                                                                             | PHPUnit Unit + Integration (Gui suite is NOT included) |
| `./vendor/bin/phpunit --testsuite Full`                                                                     | all PHP tests (unit, integration, gui)                 |
| `composer lint`                                                                                             | Pint code style check (`pint --test`)                  |
| `./vendor/bin/pint`                                                                                         | Pint formatting                                        |
| `npm run lint`                                                                                              | ESLint on `resources/vue/ts`                           |
| `npm test`                                                                                                  | Vitest (Vue component tests, with coverage)            |
| `npm run prod` / `npm run watch`                                                                            | Laravel Mix build (frontend)                           |
| `php artisan hcms:install`                                                                                  | CLI installer                                          |
| `php artisan hcms:plugin {--install\|--uninstall\|--activate\|--deactivate\|--download\|--remove} {plugin}` | plugin management                                      |
| `php artisan hcms:theme {--set} {theme}`                                                                    | switch theme                                           |
| `php artisan hcms:user {--create-admin}`                                                                    | create an admin user                                   |

PHPStan (`larastan`, vendor/bin/phpstan) and PhpArchitect (`phparkitect.php`) are available as dev dependencies.

## CI (what GitHub Actions actually runs)

`.github/workflows/github-ci.yml` runs on `master` push + PRs. When in doubt, verify against it:

- **Style**: `vendor/bin/pint --test app bootstrap config database routes` (a subset — `plugins/` and `themes/` are skipped) + `phplint` for syntax.
- **Static analysis**: `vendor/bin/phpstan analyse app bootstrap config database routes --level 0` (level 0, not the larastan default).
- **PHP tests**: `INSTALLED=YES XDEBUG_MODE=coverage ./vendor/bin/phpunit --testsuite Unit` across PHP 8.1–8.5. `INSTALLED=YES` is required because CI has no `.env`.
- **Frontend**: `npm install --force` (peer-dep conflicts under Vue 2.6 / Laravel Mix), then `npm test` and `npm run build`.

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

### Deviation from the default Laravel structure

> ⚠️ **Instead** of Laravel core's default `tests/Unit` and `tests/Feature`
> folders, HorizontCMS plugins **must** use the following naming (note the
> lowercase subfolder names `unit` and `integration`):

- `tests/unit` — isolated unit tests, test classes suffixed `Test.php`
- `tests/integration` — integration tests, test classes suffixed `Test.php`

The PHPUnit configuration (`phpunit.xml`) must define the test suites
accordingly:

```xml
<testsuites>
    <testsuite name="Unit">
        <directory suffix="Test.php">tests/unit</directory>
    </testsuite>
    <testsuite name="Integration">
        <directory suffix="Test.php">tests/integration</directory>
    </testsuite>
    <testsuite name="Full">
        <directory suffix="Test.php">tests</directory>
    </testsuite>
</testsuites>
```

A `Full` suite covering the whole `tests/` tree is included alongside `Unit`
and `Integration` so all plugin tests can be run in one pass in CI.

---

### Register.php — Mandatory Entry Point

Every plugin must ship a `Register.php` file at its root, which **must
implement** the core `App\Interfaces\PluginInterface`.

```php
<?php

namespace Plugins\ExamplePlugin;

use App\Interfaces\PluginInterface;

class Register implements PluginInterface
{
    // Mandatory implementation of the methods required by PluginInterface
    // (e.g. boot(), register(), getName(), getVersion(), etc. — as defined
    // by the core interface).
}
```

**Rules:**

- `Register.php` is the single official entry point that the HorizontCMS
  runtime looks for and instantiates when loading the plugin.
- If the class does **not** implement `PluginInterface`, the plugin is
  considered invalid — the runtime will reject or skip it.
- The namespace must be unique and must not collide with the core's or
  other plugins' namespaces.

---

### Composer Dependency Management

#### Strictly Forbidden

> 🚫 It is **FORBIDDEN** to modify the HorizontCMS core `composer.json` in any
> way (neither adding new dependencies nor changing existing ones is allowed
> as part of plugin development or installation).

#### Required Process

- Every plugin has its **own, independent `composer.json`** in the plugin's
  root directory.
- All **third-party** PHP packages required by the plugin must be declared in
  this plugin-local `composer.json`. The Laravel/Illuminate framework itself is
  **not** a third-party dependency for plugins — it is provided by the host CMS
  at runtime (see "Laravel/Illuminate dependencies are provided by the CMS"
  below).
- The plugin has its own `vendor/` directory, installed within the plugin's
  own folder (`composer install` run inside the plugin root), **not** merged
  into the core `vendor/` directory.
- At load time, the runtime reads the plugin's own autoloaders/dependencies —
  the core's composer state remains untouched.

```json
{
  "name": "vendor/example-plugin",
  "require": {
    "php": "^8.1",
    "vendor/some-third-party-package": "^1.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^9.6",
    "illuminate/support": "^11.0"
  },
  "replace": {
    "laravel/framework": "*"
  },
  "autoload": {
    "psr-4": {
      "Plugins\\ExamplePlugin\\": "src/"
    }
  }
}
```

#### Laravel/Illuminate dependencies are provided by the CMS

HorizontCMS is a Laravel application, so at production time the runtime
already provides the full Laravel (Illuminate) framework. Plugins therefore
**must not** declare Laravel/Illuminate framework packages in their own
`require` section — doing so only bundles a duplicate copy of what the host
CMS already supplies.

Rules:

- Laravel/Illuminate framework packages go in `require-dev` **only**, and only
  when the plugin's isolated tests (which bootstrap just the plugin's own
  `vendor/autoload.php`, see section 5) need those classes. They are never a
  runtime `require` dependency of the plugin.
- Use the `replace` block to tell Composer that the Laravel framework is
  provided by the host CMS, e.g. `"laravel/framework": "*"` plus any
  Illuminate components the host provides (e.g. `illuminate/http`,
  `illuminate/routing`). This keeps third-party packages (such as
  `laravel/mcp`) from trying to install a second copy of the framework.
- At runtime the plugin's Illuminate classes resolve from the **core's**
  autoloader (registered first), never from the plugin's own `vendor/`.

---

### PHPUnit Installation — Per-Plugin, Self-Contained

> ⚠️ The core's PHPUnit installation **cannot** be used to test a plugin.
> Every plugin must have **its own, independently installed PHPUnit**.

Required steps when developing a plugin:

1. Install PHPUnit at the plugin root via the plugin's own `composer.json`
   (in the `require-dev` section):

   ```json
   {
     "require-dev": {
       "phpunit/phpunit": "^10.0"
     }
   }
   ```

2. Run the plugin's own `vendor/bin/phpunit` binary to execute tests —
   **not** the core's:

   ```bash
   cd plugins/<plugin-name>
   composer install
   ./vendor/bin/phpunit
   ```

3. The plugin's own `phpunit.xml` configuration file must reference the
   `tests/unit` and `tests/integration` directories (see section 2), and should
   follow the reference template below.

#### Reference `phpunit.xml` template

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         backupGlobals="false"
         backupStaticAttributes="false"
         bootstrap="vendor/autoload.php"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="false"
         stopOnFailure="false"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.3/phpunit.xsd">
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">./app</directory>
        </include>
        <exclude>
            <directory>./app/View</directory>
        </exclude>
        <report>
            <clover outputFile="reports/coverage.xml"/>
            <html outputDirectory="reports/html"/>
        </report>
    </coverage>
    <logging>
        <junit outputFile="reports/junit.xml"/>
    </logging>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">tests/unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory suffix="Test.php">tests/integration</directory>
        </testsuite>
        <testsuite name="Full">
            <directory suffix="Test.php">tests</directory>
        </testsuite>
    </testsuites>
    <php>
        <server name="APP_ENV" value="testing"/>
        <server name="CACHE_DRIVER" value="array"/>
        <server name="SESSION_DRIVER" value="array"/>
        <server name="QUEUE_DRIVER" value="sync"/>
        <server name="DB_CONNECTION" value="sqlite"/>
        <server name="DB_DATABASE" value=":memory:"/>
        <server name="HTTP_SIGNATURE" value="-" />
    </php>
</phpunit>
```

**Notes on this template:**

- `bootstrap="vendor/autoload.php"` points at the **plugin's own** autoloader,
  not the core's — this only works once the plugin has its own `vendor/`
  directory installed (see section 4 and step 1 above).
- `<coverage><include>` targets `./app` — adjust this path to wherever the
  plugin keeps its testable source code (e.g. `./src` if the plugin doesn't
  use an `app/` folder).
- Coverage and JUnit reports are written to `reports/` inside the plugin
  directory; this folder should be generated at test-run time and excluded
  from version control.
- The `<php><server>` block forces an isolated, in-memory test environment
  (SQLite `:memory:` DB, array cache/session drivers, sync queue) so plugin
  tests never touch a real database or external services.
- Uses the PHPUnit 9.3 schema — pin `phpunit/phpunit` in the plugin's
  `require-dev` to a compatible 9.x release (see section 5, step 1) unless
  the plugin intentionally targets a newer PHPUnit major version, in which
  case update the schema URL and deprecated attributes (`backupGlobals`,
  `convert*ToExceptions`, etc. were removed/changed in PHPUnit 10+)
  accordingly.

---

### Vue Component Registration (Runtime)

Plugins **must** be able to register their own Vue components at runtime
through the global Vue API exposed by the core. The core provides two
globals (declared in `resources/vue/ts/global.d.ts`):

| Global        | Type                  | Description                                         |
| ------------- | --------------------- | --------------------------------------------------- |
| `window.vue`  | `VueConstructor<Vue>` | The Vue constructor (from `main.ts`)                |
| `window.hcms` | `Vue` (root instance) | The root Vue instance mounted on `#hcms` (`app.ts`) |

#### How it works today

1. The plugin's `Register.php` implements `injectAdminJs(): array` (and/or
   `injectWebsiteJs(): array`) returning paths to plain `.js` files relative
   to the plugin root.
2. `PluginServiceProvider::registerPluginJSScripts()` collects those paths
   and shares them as `$jsplugins` to the Blade layout.
3. `app/View/layout.blade.php` renders them as `<script>` tags in `<head>`.
4. Inside those scripts, plugins access `window.vue` and `window.hcms` to
   register components.

#### Registration pattern

A plugin's JS file (returned by `injectAdminJs`) should register components
**before** the root Vue instance mounts, or use `window.hcms.$forceUpdate()`
after late registration. Recommended approach — global registration via the
Vue constructor:

```javascript
// plugins/MyPlugin/resources/js/admin.js
window.vue.component("my-plugin-widget", {
  template: '<div class="my-widget">{{ msg }}</div>',
  data() {
    return { msg: "Hello from MyPlugin" };
  },
});
```

Alternatively, register on the root instance so the component is available
inside `#hcms`:

```javascript
window.hcms.$options.components["my-plugin-widget"] = {
  template: "<div>...</div>",
};
window.hcms.$forceUpdate();
```

#### Rules

- Plugin Vue components **must** be registered via `window.vue` (global) or
  `window.hcms.$options.components` (local). Do not assume any build-time
  bundling — the plugin's JS is loaded as a plain `<script>` tag.
- Plugin components **must not** overwrite core component names (`text-editor`,
  `lock-screen`, `file-manager`, `category-selector`, `parent-page-selector`,
  `filter-bar`).
- If a plugin needs its own `.vue` SFC files compiled, it must ship its own
  build step (e.g. its own `webpack.mix.js` or `vite.config.ts`) and output
  a single JS bundle that is returned by `injectAdminJs()`.
- Plugin components that depend on the i18n instance can access it via
  `window.hcms.$i18n`.
- Plugin components that need HTTP can use `window.vue.prototype.http`
  (axios-observable, pre-configured with CSRF/API tokens).

## Frontend conventions

- Vue 2.7. The `window.vue` global instance (`resources/vue/ts/main.ts`), components registered in `app.ts`.
- HTTP: `axios-observable` (rewritten in axios-observable.ts) (`window.vue.prototype.http`), CSRF + API token from `<head>` meta tags.
- Components follow a `.vue` + `.ts` (logic) + `.spec.ts` (Vitest) trio in `components/<name>/`.
- Build: Laravel Vite (`vite.config.ts`) into `resources/js` and `resources/css`.
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
- The app counts as installed only if `.env` exists OR `INSTALLED=YES` is set (see `App\HorizontCMS::isInstalled()`).
- **After any Vue/bundler change, always visually verify these in the browser:** CKEditor loads and is editable, Bootstrap 5 dropdowns open/close correctly, Select2 initializes and works (e.g. category selector on blogpost form), file upload works (file-manager drag-and-drop, upload dialog). These integrations depend on jQuery, Bootstrap, and third-party plugins sharing a single global instance — bundler misconfigurations silently break them.

## Additional Forbiddens (important!)

- it is forbbiden to commit to the main/master branch
