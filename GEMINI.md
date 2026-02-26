<laravel-boost-guidelines>
=== .ai/language-and-localization rules ===

## Language & Localization Rules


- All technical guidelines, code standards, and documentation MUST be written in English.
- All source code (class names, method names, variables, comments, enums, etc.) MUST be written in English.
- However, ALL user-facing content MUST be written in Brazilian Portuguese (pt-BR).

This includes, but is not limited to:

- Labels
- Validation error messages
- Flash messages
- Alerts
- Notifications
- Button texts
- Form placeholders
- Tooltips
- Modal texts
- Email contents
- API validation responses
- Exception messages displayed to users
- Front-end UI strings

- Do NOT mix English and Portuguese in user-facing content.
- Do NOT leave default Laravel validation messages in English.
- If translations are needed, they MUST use Brazilian Portuguese (pt-BR) conventions and terminology.
- Avoid literal translations. Prefer natural Brazilian Portuguese phrasing used in real applications.

Technical rule of thumb:

> Code in English. Interface in Brazilian Portuguese.

=== .ai/php-laravel rules ===

## General code instructions


- Don't generate code comments above the methods or code blocks if they are obvious. Don't add docblock comments when defining variables, unless instructed to, like `/** @var \App\Models\User $currentUser */`. Generate comments only for something that needs extra explanation for the reasons why that code was written.
- For new features, you MUST generate Pest automated tests.
- For library documentation, if some library is not available in Laravel Boost 'search-docs', always use context7. Automatically use the Context7 MCP tools to resolve library id and get library docs without me having to explicitly ask.
- If you made changes to CSS/Javascript files or added new Tailwind classes in Blade, run `npm run build` after all front-end changes are finished.

---


## PHP instructions


- In PHP, use `match` operator over `switch` whenever possible
- Generate Enums always in the folder `app/Enums`, not in the main `app/` folder, unless instructed differently.
- Always use Enum value as the default in the migration if column values are from the enum. Always casts this column to the enum type in the Model.
- Don't create temporary variables like `$currentUser = auth()->user()` if that variable is used only one time.
- Always use Enum where possible instead of hardcoded string values, if Enum class exists. For example, in Blade files, and in the tests when creating data if field is casted to Enum then use that Enum instead of hardcoding the value.

---


## Laravel instructions


- Always run Laravel-related CLI commands using Sail: prefix commands with `./vendor/bin/sail`.
    - Use `./vendor/bin/sail artisan migrate` instead of `php artisan migrate`
    - Use `./vendor/bin/sail artisan test` instead of `php artisan test`
    - Use `./vendor/bin/sail artisan make:model User` instead of `php artisan make:model User`
    - Use `./vendor/bin/sail composer install` instead of `composer install`
    - Use `./vendor/bin/sail npm run build` instead of `npm run build`
- Never assume global PHP, Composer, or Node execution. Always run commands inside the Sail container.
- **Eloquent Observers** should be registered in Eloquent Models with PHP Attributes, and not in AppServiceProvider. Example: `#[ObservedBy([UserObserver::class])]` with `use Illuminate\Database\Eloquent\Attributes\ObservedBy;` on top
- Aim for "slim" Controllers/Components and put larger logic pieces in Service classes
- Use Laravel helpers instead of `use` section classes. Examples: use `auth()->id()` instead of `Auth::id()` and adding `Auth` in the `use` section. Other examples: use `redirect()->route()` instead of `Redirect::route()`, or `str()->slug()` instead of `Str::slug()`.
- Don't use `whereKey()` or `whereKeyNot()`, use specific fields like `id`. Example: instead of `->whereKeyNot($currentUser->getKey())`, use `->where('id', '!=', $currentUser->id)`.
- Don't add `::query()` when running Eloquent `create()` statements. Example: instead of `User::query()->create()`, use `User::create()`.
- When adding columns in a migration, update the model's `$fillable` array to include those new attributes.
- Never chain multiple migration-creating commands (e.g., `make:model -m`, `make:migration`) with `&&` or `;` — they may get identical timestamps. Run each command separately and wait for completion before running the next.
- Enums: If a PHP Enum exists for a domain concept, always use its cases (or their `->value`) instead of raw strings everywhere — routes, middleware, migrations, seeds, configs, and UI defaults.
- Don't create Controllers with just one method which just returns `view()`. Instead, use `Route::view()` with Blade file directly.
- Always use Laravel's @session() directive instead of @if(session()) for displaying flash messages in Blade templates.
- In Blade files always use `@selected()` and `@checked()` directives instead of `selected` and `checked` HTML attributes. Good example: @selected(old('status') === App\Enums\ProjectStatus::Pending->value). Bad example: {{ old('status') === App\Enums\ProjectStatus::Pending->value ? 'selected' : '' }}.


### Service classes


- Use Service classes to encapsulate reusable business logic, keeping Controllers and Livewire Components slim.
- Service classes MUST be created in the `app/Services/` folder.
- If a Service is used in only ONE method of a Controller or Component, inject it directly into that method via type-hinting. If it is used in MULTIPLE methods, initialize it in the Constructor (or `mount()`/`boot()` for Livewire Components).
- The same injection rule applies to both traditional Controllers and Livewire Components — use `mount()` or `boot()` to inject Services in Components when needed across multiple methods, or inject directly into the action method.
- Services MUST NOT contain presentation logic (views, redirects, flash messages). Return data or throw exceptions, and let the Controller/Component decide how to present the result.
- Services MUST be independently testable — avoid coupling with `request()`, `session()`, or `auth()` directly. Receive those values as parameters instead.


### Model construction rules


- Models MUST define the `$fillable` property correctly for all mass-assignable attributes.
- When adding new columns via migration, you MUST update the corresponding Model `$fillable` array.
- Relationships MUST follow Laravel naming conventions (`user()`, `orders()`, `profile()`, etc.).
- Relationship methods MUST use correct return types (`HasMany`, `BelongsTo`, `HasOne`, etc.).
- All relationships MUST have their inverse defined when applicable.
    - If `User` hasMany `Order`, then `Order` MUST define `belongsTo(User::class)`.
    - If `User` hasOne `Profile`, then `Profile` MUST define `belongsTo(User::class)`.
- Do not assume foreign key naming. Explicitly define foreign keys if they don't follow Laravel conventions.
- If a column represents a domain concept backed by an Enum, the Model MUST cast it using `$casts`.

---


## Livewire 4 instructions


- In Livewire projects, don't use Livewire Volt. Only Livewire class components (single-file or multi-file).
- In Livewire projects, computed properties should be used with PHP attribute `#[Computed]` and not method `getSomethingProperty()`.


### Full-page components (Pages)


- Use Livewire components as full pages instead of traditional Controllers for routes that render interactive views.
- Register full-page component routes with `Route::livewire()` in `routes/web.php`:
  ```php
  Route::livewire('/posts', 'pages::post.index');
  Route::livewire('/posts/create', 'pages::post.create');
  Route::livewire('/posts/{post}', 'pages::post.show');
  Route::livewire('/posts/{post}/edit', 'pages::post.edit');
  ```
- Page components MUST use the `pages::` prefix for organization. They live in `resources/views/pages/`.
- To create a new page component via Artisan: `./vendor/bin/sail artisan make:livewire pages::post.create`
- The default layout is located at `resources/views/layouts/app.blade.php`. To use a different layout, use the `#[Layout('layouts::admin')]` attribute on the component class.
- To set a dynamic page title, use the `->title()` fluent method in `render()`:
  ```php
  public function render()
  {
      return view('pages.post.show')
          ->title($this->post->title);
  }
  ```
- Route Model Binding works automatically in full-page components. Define the typed parameter in `mount()`, or simply declare a typed public property with the same name as the route parameter:
  ```php
  // Option 1: via mount()
  public function mount(Post $post)
  {
      $this->post = $post;
  }

  // Option 2: typed public property (Livewire resolves it automatically)
  public Post $post;
  ```
- DO NOT create Controllers that only return views with data for interactive pages — use full-page Livewire components instead. Traditional Controllers should only be used for API routes, downloads, redirects, or actions that don't require interactivity.


### Forms


- For forms, ALWAYS use **Livewire Form Objects** when the component has more than 2 form fields. This keeps the component clean and allows reusing validation logic.
- Create Form Objects with the command: `./vendor/bin/sail artisan make:livewire-form PostForm`
- Form Objects live in `app/Livewire/Forms/` and extend `Livewire\Form`.
- Use the `#[Validate]` attribute to define validation rules directly on Form Object properties:
  ```php
  namespace App\Livewire\Forms;

  use Livewire\Attributes\Validate;
  use Livewire\Form;

  class PostForm extends Form
  {
      #[Validate('required|min:5')]
      public string $title = '';

      #[Validate('required|min:10')]
      public string $content = '';
  }
  ```
- In the component, declare the Form Object as a public property and use `wire:model="form.field"` in the template:
  ```php
  public PostForm $form;

  public function save()
  {
      $this->form->validate();

      Post::create($this->form->all());

      $this->form->reset();
  }
  ```
  ```html
  <form wire:submit="save">
      <input type="text" wire:model="form.title" />
      @error('form.title') <span>{{ $message }}</span> @enderror

      <textarea wire:model="form.content"></textarea>
      @error('form.content') <span>{{ $message }}</span> @enderror

      <button type="submit">Save</button>
  </form>
  ```
- For simple forms (1-2 fields), it is acceptable to use public properties directly on the component with `#[Validate]`.
- Use `wire:model` (without `.live`) by default. Use `wire:model.live` or `wire:model.live.blur` only when real-time validation is needed.
- For edit forms, populate the Form Object in `mount()` using `$this->form->fill($model->toArray())` or by setting properties individually.
- Heavy persistence logic (create, update, process) inside `save()` should be delegated to a **Service class**, keeping the component slim.


### Component structure


- Livewire components should follow the same "slim Controllers" philosophy: business logic goes into Services, the component only handles binding, validation, and orchestration.
- For actions involving complex business logic, inject the Service directly into the method:
  ```php
  public function approve(PostService $postService)
  {
      $this->form->validate();

      $postService->approve($this->post);

      $this->redirect(route('posts.index'));
  }
  ```
- Use `wire:navigate` on links between Livewire pages for SPA-like navigation without full page reloads.

---


## Testing instructions



### Before Writing Tests


1. **Check database schema** - Use `database-schema` tool to understand:
    - Which columns have defaults
    - Which columns are nullable
    - Foreign key relationship names

2. **Verify relationship names** - Read the model file to confirm:
    - Exact relationship method names (not assumed from column names)
    - Return types and related models

3. **Test realistic states** - Don't assume:
    - Empty model = all nulls (check for defaults)
    - `user_id` foreign key = `user()` relationship (could be `author()`, `employer()`, etc.)
    - When testing form submissions that redirect back with errors, assert that old input is preserved using `assertSessionHasOldInput()`.


### Livewire component testing


- Use `Livewire::test()` to test Livewire components.
- Test Form Objects by verifying validation, reset, and data population.
- For full-page components, test the route with `$this->get('/posts/create')` and verify the component is rendered.
- Example test for a component with Form Object:
  ```php
  use Livewire\Livewire;

  it('can create a post', function () {
      Livewire::test(PostCreate::class)
          ->set('form.title', 'My Post Title')
          ->set('form.content', 'This is the post content.')
          ->call('save')
          ->assertHasNoErrors()
          ->assertRedirect(route('posts.index'));

      expect(Post::where('title', 'My Post Title')->exists())->toBeTrue();
  });

  it('validates required fields', function () {
      Livewire::test(PostCreate::class)
          ->call('save')
          ->assertHasErrors(['form.title', 'form.content']);
  });
  ```

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5.3
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `livewire-development` — Develops reactive Livewire 4 components. Activates when creating, updating, or modifying Livewire components; working with wire:model, wire:click, wire:loading, or any wire: directives; adding real-time updates, loading states, or reactivity; debugging component behavior; writing Livewire tests; or when the user mentions Livewire, component, counter, or reactive UI.
- `pest-testing` — Tests applications using the Pest 4 PHP framework. Activates when writing tests, creating unit or feature tests, adding assertions, testing Livewire components, browser testing, debugging test failures, working with datasets or mocking; or when the user mentions test, spec, TDD, expects, assertion, coverage, or needs to verify functionality works.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `vendor/bin/sail npm run build`, `vendor/bin/sail npm run dev`, or `vendor/bin/sail composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail npm run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `vendor/bin/sail artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `vendor/bin/sail npm run build` or ask the user to run `vendor/bin/sail npm run dev` or `vendor/bin/sail composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces using only PHP — no JavaScript required.
- Instead of writing frontend code in JavaScript frameworks, you use Alpine.js to build the UI when client-side interactions are required.
- State lives on the server; the UI reflects it. Validate and authorize in actions (they're like HTTP requests).
- IMPORTANT: Activate `livewire-development` every time you're working with Livewire-related tasks.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/sail bin pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test --format agent`, simply run `vendor/bin/sail bin pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `vendor/bin/sail artisan make:test --pest {name}`.
- Run tests: `vendor/bin/sail artisan test --compact` or filter: `vendor/bin/sail artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.
- CRITICAL: ALWAYS use `search-docs` tool for version-specific Pest documentation and updated code examples.
- IMPORTANT: Activate `pest-testing` every time you're working with a Pest or testing-related task.

</laravel-boost-guidelines>
