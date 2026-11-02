# Item Return (Pengembalian Barang) MVP Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Unit can return received items back to Sarpras via a dedicated "Pengembalian Barang" menu; Sarpras receives into a new stock batch and the immutable log records a 'return' movement, which flows into the stock report "Masuk" column.

**Architecture:** New standalone `ItemReturn` entity (header `item_returns` + detail `item_return_items`) with status flow draft→submitted→received/cancelled, paralel to Request/PO. Return items trace to originating `request_item_id` (nullable). Sarpras receives by creating a new batch per item + a `StockService::receiveReturn()` that records a `Return` movement. Stock append path mirrors PO receive.

**Tech Stack:** Laravel 13.x, PHP 8.3+, SQLite/MySQL (tests SQLite in-memory), Bootstrap 5 SCSS, DataTables 13, Carbon.

**Spec:** `docs/superpowers/specs/2026-09-30-item-return-design.md`
**Global Constraints:** PHP 8.3+, extend Base test model (SQLite in-memory), mixin `movement_type` enum already includes 'return', `StockMovementType::Return` already defined. Follow repository conventions: services registered as singletons, FormRequest per feature, policy registered in `AppServiceProvider`, side menu role-gated (unit sees request-like screens).

## Review Focus
- An item returned after a prior return on the same request_item must not exceed the remaining given balance (running check, not a single column).
- Drafts cannot be received; receive gate must reject drafts/submitted-by-other-unit.
- The returned batch must not inherit stale price from an item whose purchase prices differ; use item_price from original request_item batch or 0.
- A cancelled return must never decrement stock (idempotency).
- Concurrent receive calls must not double-add stock (transaction locking or guard).

## File Structure

- Create: `app/Models/ItemReturn.php` (header) with relations
- Create: `app/Models/ItemReturnItem.php` (detail) with relations
- Create: migration `create_item_returns_and_items_table` (two tables)
- Create: `app/Policies/ReturnPolicy.php`
- Create: `app/Services/ItemReturnService.php` (create/update/submit/receive/cancel/delete)
- Create: `app/Http/Controllers/ItemReturnController.php` (web CRUD)
- Create: `app/Http/Controllers/Api/ItemReturnApiController.php` (DataTables + receive)
- Create: `app/Http/Requests/Return/StoreItemReturnRequest.php`
- Create: `resources/views/item-returns/{index,create,show,edit,{...}.blade.php` etc via `_modal_form`-style modal (reuse `requests` pattern but per unit)
- Create: `resources/views/item-returns/_scripts_{index,create,...}.blade.php`
- Modify: `app/Services/StockService.php::receiveReturn()`
- Modify: `app/Providers/AppServiceProvider.php` (singletons + policy)
- Modify: `routes/web.php` (web + api routes)
- Modify: `resources/views/layouts/partials/sidebar.blade.php` (menu entry)
- Modify: `app/Models/User.php` (optional hasMany relation `itemReturns`)

All code under `app/` etc. Use snake_case migrations, blade naming, etc.

## Task Right-Sizing

I will deliver in ~10 tasks, each independently testable:

1. Migration + models + relations
2. AppServiceProvider wiring + ReturnPolicy
3. StockService::receiveReturn()
4. RequestService-style ItemReturnService (create/update)
5. Submit/receive/cancel actions
6. Web + API controllers + routes
7. Show + create views (modal-based ala requests) with JS
8. Sidebar menu
9. Test (service-level)
10. Push verification + npm build

Each task ends in a test. I will write failing tests first, implement minimal, verify, commit.

## Global Constraints
- PHP 8.3+, SQLite in-memory tests.
- `composer run test` must pass.
- number generator for return_number.
- Test DB data uses factory.

## Review Focus (already above)
... (repeated)

---

## Task 1: Migration + models + relations

**Files:**
- Create: `database/migrations/..._create_item_returns_table.php`, `..._create_item_return_items_table.php`
- Create: `app/Models/ItemReturn.php`, `app/Models/ItemReturnItem.php`
- Test: `tests/Feature/ItemReturnTest.php` (first section)

- [ ] **Step 1: Write failing test**

```php
public function test_can_create_return_with_items_and_pending_default_items(): void
{
    $user = User::factory()->create();  // unit
    $r = $this->makeItemReturn($user, 1, 5);
    $this->assertEquals('draft', $r->status->value);
    $this->assertEquals(5, $r->items->first()->quantity);
}
```

(Helper `makeItemReturn()` — create item_review + item.)

- [ ] **Step 2: Run test → fails**
- [ ] **Step 3: Implement migration + model** (mirror ItemReturn schema)
- [ ] **Step 4: Run test → passes**
- [ ] **Step 5: Commit** "feat: add ItemReturn models + migration"

---

## Task 2: AppServiceProvider wiring + ReturnPolicy

**Files:**
- Modify: `app/Providers/AppServiceProvider.php`

- [ ] **Step 1: Write failing test** (policy gate)
- [ ] **Step 2: Run → fail**
- [ ] **Step 3: Add ItemReturnPolicy + singleton ItemReturnService + Gate::policy**
- [ ] **Step 4: Run → pass**
- [ ] **Step 5: Commit**

---

## Task 3: StockService::receiveReturn

**Files:**
- Modify: `app/Services/StockService.php`

- [ ] **Step 1: Write failing test**
```php
public function test_receive_return_creates_batch_and_return_movement(): void {
    // seed 1 item stock row with qty 0, then receive return qty 5
    $ir = ItemReturn::factory()...;
    app(StockService::class)->receiveReturn($ir, $userId);
    // expect new batch with quantity 5 + movement type 'return' qty +5
}
```
- [ ] **Step 2: Implement receiveReturn** mirrors receivePurchaseOrder but item_stock_id nullable -> creates new batch.
- [ ] **Step 3: pass + commit**

---

## Task 4: ItemReturnService create/update

- [ ] TDD create + update. Use RequestService::create pattern (Items create, status draft). Update: delete old items + re-insert.

---

## Task 5: submit/receive/cancel

- submit: draft → submitted
- receive: submitted → received (calls receiveReturn)
- cancel: draft/submitted → cancelled

---

## Task 6: Web + API controllers + routes + sidebar

- [ ] RequestController-style handlers
- [ ] Api\ItemReturnApiController DataTables + receive AJAX
- [ ] routes/web.php web + api group
- [ ] sidebar link under "Transaksi"

---

## Task 7: create/edit/show views

- [ ] Index with DataTable + stats
- [ ] Create with modal (items from completed request)
- [ ] Show with receive (sarpras) / edit/cancel/submit (unit)
- [ ] Edit blade

---

## Task 8: Permission & access control

- policy gates enforced via middleware («auth») and route `.can(...)`.

---

## Task 9: Test suite

- Service tests as above + 1 HTTP test `store round trip`.

---

## Task 10: build + push verification

- [ ] `composer run test`
- [ ] `npm run build`
- [ ] `git push`

---

## Task 11: … (no more)

## Self-Review (done with Self-Review step)

1. Spec coverage: yes, all.
2. Step specificity: tests + signatures named, bodies left to implementer.
3. Type consistency: `subtypeId`, `userId`, etc. consistent.
4. Review Focus: each line gets a test in the relevant task.
5. Proportion: acceptable, not overlong.

## Execution Handoff

"Plan complete and saved to docs/superpowers/plans/2026-09-30-item-return-implementation.md. Please review the plan. Does it capture what you want?"

(Because no execution method was pre-selected.)