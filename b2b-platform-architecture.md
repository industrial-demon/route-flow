# B2B Trading & Logistics Platform — Architecture Summary

> Working design recap (November 2026). This is a **B2B connection and deal-coordination platform**, not a conventional checkout marketplace. Some domain entities below are proposals for later implementation, not finalized requirements.

## 1. Product concept

The platform connects **sellers**, **buyers**, and **logistics providers**. Sellers advertise goods, buyers discover suppliers or publish purchasing needs, and logistics providers can participate when transport is required. A **super admin** oversees the process in an internal CRM.

Example workflow:

```text
Buyer needs 20 tonnes of apples
          |
          v
Seller offers available goods
          |
          v
Buyer and seller negotiate / agree
          |
          +---- Transport available? ---- Yes ---> Coordinate delivery
          |
          No
          |
          v
Logistics provider submits transport offer
          |
          v
Delivery coordination / deal completion

Super Admin CRM: oversight, moderation, support, disputes
```

Payment processing, contracts, and the exact deal lifecycle **have not yet been decided**.

## 2. Application areas and routing

One Laravel application, with four isolated authenticated route areas and one public SSR website:

```text
my-app.com/                 Public SSR website
my-app.com/admin/*          Super Admin CRM
my-app.com/seller/*         Seller dashboard
my-app.com/buyer/*          Buyer dashboard
my-app.com/logistics/*      Logistics dashboard
```

**Isolation is at the routing/authorization level**, not separate applications or databases. Each area can have its own route file, layout, UI, and features while sharing domain models and authentication.

```text
routes/
  web.php
  admin.php
  seller.php
  buyer.php
  logistics.php

resources/views/
  layouts/
    public.blade.php
    admin.blade.php
    seller.blade.php
    buyer.blade.php
    logistics.blade.php
  features/
    public/
    admin/
    seller/
    buyer/
    logistics/
```

Laravel + Livewire 4 is the intended stack. Filament is an optional tool for the admin CRM, not a requirement.

## 3. Identity and authorization — agreed design

**Key decision: `users.type` determines WHICH dashboard a user belongs to. The user's roles determine WHAT they can do inside that dashboard.**

```text
                    users
                  /       \
             type          roles (many-to-many)
              |                  |
    dashboard boundary     granular permissions
              |                  |
  seller / buyer /          seller_owner
  logistics / super_admin   seller_manager
                             buyer_owner
                             logistics_driver
```

A user belongs to **exactly one application area**, even though the `role_user` pivot can hold multiple roles. Cross-area combinations are **invalid by business rule**.

### User type enum

```php
<?php

namespace App\Enums;

enum UserType: string
{
    case Seller = 'seller';
    case Buyer = 'buyer';
    case Logistics = 'logistics';
    case SuperAdmin = 'super_admin';
}
```

### Example role slugs

```text
seller_owner
seller_manager
seller_employee

buyer_owner
buyer_manager
buyer_employee

logistics_owner
logistics_manager
logistics_driver
```

**`super_admin` is a user type, not a role.** A super admin does not need an entry in `role_user` for basic admin-area access.

| `users.type` | Route area | Example permitted roles |
|---|---|---|
| `seller` | `/seller/*` | `seller_owner`, `seller_manager`, `seller_employee` |
| `buyer` | `/buyer/*` | `buyer_owner`, `buyer_manager`, `buyer_employee` |
| `logistics` | `/logistics/*` | `logistics_owner`, `logistics_manager`, `logistics_driver` |
| `super_admin` | `/admin/*` | None required |

Valid/invalid examples:

```text
seller + [seller_owner]                    VALID
seller + [seller_manager, seller_employee] VALID structurally; check if both needed
buyer  + [buyer_owner]                     VALID
seller + [seller_manager, buyer_owner]     INVALID
buyer  + [logistics_driver]                INVALID
super_admin + [seller_owner]               INVALID under current design
```

Role names are examples, not a finalized permissions matrix. Roles within one area may be mutually exclusive if your business rules require it.

## 4. Database schema (Laravel migrations)

### 4.1 Users, roles, pivot

```php
// users table — relevant columns; keep Laravel's standard auth fields
Schema::table('users', function (Blueprint $table) {
    $table->string('type', 30)->index();

    // Optional: a user belongs to one business profile
    $table->foreignIdFor(BusinessProfile::class)
        ->nullable()
        ->constrained()
        ->nullOnDelete();
});

Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->timestamps();
});

Schema::create('role_user', function (Blueprint $table) {
    $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
    $table->foreignIdFor(Role::class)->constrained()->cascadeOnDelete();
    $table->primary(['user_id', 'role_id']);
});
```

Notes:
- The migration assumes `User`, `Role`, and `BusinessProfile` model imports and that `business_profiles` exists before adding its foreign key.
- If the project already has a `roles`/pivot migration, **do not recreate it**; adapt the existing schema.
- The `business_profile_id` arrangement reflects the earlier **one business per user** model. Multi-organization membership would require a different membership design later.
- If users can exist before choosing an area, make `type` nullable during onboarding and enforce it before dashboard access; otherwise keep it required.

### 4.2 Business profiles (proposed shape)

The business profile represents a real business or individual, **not its dashboard role**. User types and roles stay on users.

```php
Schema::create('business_profiles', function (Blueprint $table) {
    $table->id();
    $table->string('type', 30); // individual | organization (different concept from users.type)
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('legal_name')->nullable();
    $table->string('registration_number')->nullable();
    $table->string('tax_number')->nullable();
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->string('website')->nullable();
    $table->timestamps();
});
```

A business profile can have many users; each user currently has at most one business profile. A `slug` was **not required** for business profiles; ID-based routing is acceptable initially.

### 4.3 Categories and units

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->timestamps();
});

Schema::create('units', function (Blueprint $table) {
    $table->id();
    $table->string('code', 20)->unique();
    $table->string('name');
    $table->timestamps();
});
```

Units use a **`code`**, not a URL `slug`:

```text
kg   kilogram
 t   tonne
pcs  piece
 m   meter
 l   liter
```

### 4.4 Products / advertised goods

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();

    $table->foreignIdFor(BusinessProfile::class)
        ->constrained()
        ->restrictOnDelete();

    $table->foreignIdFor(Category::class)
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->foreignIdFor(Unit::class)
        ->constrained()
        ->restrictOnDelete();

    $table->string('name');
    $table->text('description')->nullable();
    $table->string('sku')->nullable();
    $table->decimal('available_quantity', 15, 3)->nullable();
    $table->timestamps();

    $table->unique(['business_profile_id', 'sku']);
});
```

**Important design choices:**
- Products belong to a **business profile**, not directly to a user.
- Category is optional; removing a category sets `category_id` to `NULL`.
- Units should generally be protected from deletion while referenced (`restrictOnDelete()`).
- Users can **change** a product's unit despite `restrictOnDelete()`; the restriction only concerns deletion of the referenced unit.
- If unit changes from `kg` to `t`, quantity must be converted (e.g. `1000 kg` → `1 t`), not simply relabeled.
- `available_quantity` may fit advertised B2B offers better than `stock_quantity` because the platform is not necessarily a warehouse inventory system.
- Whether `unit_id` is required or nullable depends on whether all listings represent measurable goods; the example above makes it required.
- `restrictOnDelete()` for business profiles is a conservative recommendation to avoid accidentally losing commercial records. You could instead soft-delete businesses/products or define a different lifecycle policy.
- SKU is optional and, when provided, unique **within a business**, not globally. MySQL permits multiple `NULL` values in a unique index.

### Foreign key delete actions

| Action | Effect when referenced parent is deleted |
|---|---|
| `cascadeOnDelete()` | Delete referencing child rows |
| `nullOnDelete()` | Set referencing foreign keys to `NULL` (requires nullable column) |
| `restrictOnDelete()` | Reject parent deletion while referenced |

These do **not** prevent updating a product's `unit_id`, `category_id`, etc.

## 5. Laravel model relationships

```php
// User.php
public function roles(): BelongsToMany
{
    return $this->belongsToMany(Role::class);
}

public function businessProfile(): BelongsTo
{
    return $this->belongsTo(BusinessProfile::class);
}

protected function casts(): array
{
    return [
        'type' => UserType::class,
    ];
}

// BusinessProfile.php
public function users(): HasMany
{
    return $this->hasMany(User::class);
}

public function products(): HasMany
{
    return $this->hasMany(Product::class);
}

// Product.php
public function businessProfile(): BelongsTo
{
    return $this->belongsTo(BusinessProfile::class);
}

public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

public function unit(): BelongsTo
{
    return $this->belongsTo(Unit::class);
}
```

Import the relevant model, enum, and Eloquent relation classes in each file.

## 6. Route-level isolation

Example registration in Laravel's `bootstrap/app.php` (within `withRouting(..., then: ...)`):

```php
use Illuminate\Support\Facades\Route;

foreach (['admin', 'seller', 'buyer', 'logistics'] as $area) {
    $type = $area === 'admin' ? 'super_admin' : $area;

    Route::middleware(['web', 'auth', "user.type:$type"])
        ->prefix($area)
        ->name("$area.")
        ->group(base_path("routes/$area.php"));
}
```

Example middleware (register alias `user.type` in Laravel's middleware configuration):

```php
use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

public function handle(Request $request, Closure $next, string $type): Response
{
    abort_unless(
        $request->user()?->type === UserType::from($type),
        403
    );

    return $next($request);
}
```

The route alias and middleware must be registered; `user.type` is **custom**, not a built-in Laravel middleware.

Example route file:

```php
// routes/seller.php
Route::livewire('/', 'seller.dashboard')->name('dashboard');
Route::livewire('/products', 'seller.products.index')->name('products.index');
```

Component names depend on the Livewire 4 component registration/conventions used by your project. Register your feature directories if needed.

### Login redirect

```php
return redirect()->route(match ($user->type) {
    UserType::Seller     => 'seller.dashboard',
    UserType::Buyer      => 'buyer.dashboard',
    UserType::Logistics  => 'logistics.dashboard',
    UserType::SuperAdmin => 'admin.dashboard',
});
```

Dashboard routes need matching `dashboard` names defined in each area.

## 7. Role assignment and action permissions

Role assignment should be **centralized** (service/action), not arbitrary controller or Livewire `attach()` calls. Check:

1. Every role belongs to the user's `type` area.
2. Only authorized administrators/owners can assign sensitive roles.
3. Users cannot self-register or self-promote to `super_admin`.
4. Optional: prevent redundant or incompatible same-area roles.
5. Update role assignments transactionally where several changes must succeed together.

Since role slugs are prefixed, you can validate prefixes, but an explicit `roles.area` column would be more robust if the system becomes complex. `users.type` remains the authority for route isolation.

```php
// Example role compatibility check (service-level)
$allowedPrefix = $user->type->value . '_';

foreach ($roles as $role) {
    if ($user->type === UserType::SuperAdmin ||
        ! str_starts_with($role->slug, $allowedPrefix)) {
        throw ValidationException::withMessages([
            'roles' => 'Role is not valid for this user type.',
        ]);
    }
}
```

For fine-grained permissions, use **Laravel Gates/Policies**, and scope business records by the authenticated user's `business_profile_id`. Checking a role alone does not authorize access to another company's products, deals, or shipments.

## 8. Possible future business modules (not finalized)

```text
Seller:    product offers, availability, incoming inquiries, deals
Buyer:     purchase requests, supplier discovery, negotiations, deals
Logistics: transport requests, quotations, assignments, shipments
Admin CRM: user/business moderation, deal oversight, disputes, support
```

Candidate tables for future iterations:

```text
buy_requests
  id, business_profile_id, product/category details, quantity, unit_id, status

deals
  id, buyer_business_profile_id, seller_business_profile_id, status, ...

shipments
  id, deal_id, logistics_business_profile_id?, status, ...

logistics_quotes
  id, shipment_id, logistics_business_profile_id, price, status, ...
```

These are **conceptual sketches**, not approved final migrations. Decide whether the platform coordinates transactions only or handles contracts, payments, and delivery tracking before finalizing them.

## 9. Agreed decisions vs open questions

**Agreed / strongly preferred**
- One Laravel application, one user/auth system, four isolated route areas.
- Public SSR home page.
- `users.type`: `seller`, `buyer`, `logistics`, `super_admin`.
- `super_admin` is a **type**, not a role.
- `roles` + `role_user` pivot for granular area-specific permissions.
- User roles must not cross type/area boundaries.
- Roles are assigned to **users**, not business profiles.
- Product belongs to business profile; category may be nullable.
- Units have `code` (`kg`, `pcs`, etc.) rather than URL slugs.

**To decide later**
- Whether users can switch their type after registration (currently treated as one fixed area).
- Whether users can join multiple businesses or only one.
- Whether each user can have several same-area roles or exactly one effective role.
- Which product listings require a unit and how conversions are managed.
- Whether deleting businesses/products is permitted or should use soft deletes.
- Exact buyer/seller negotiation, logistics, payments, and contract workflows.
- Whether to use Filament for admin CRM.

---

**Core principle:** `UserType` establishes **application boundary**; `Role` establishes **permission**; `BusinessProfile` establishes **business ownership**; Policies enforce **record-level authorization**.
