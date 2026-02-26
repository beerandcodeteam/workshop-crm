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