# Feature Spec: Pengembalian Barang (Item Return) — MVP

**Status**: Approved (user confirmed MVP tier)
**Date**: 2026-09-30

## Intent & Why
Unit kini bisa mengembalikan barang yang pernah diterima dari Sarpras kembali ke gudang. Tujuan utama: koreksi propagasi stok — barang yang tidak terpakai dikembalikan agar tersedia lagi, dan audit trail lengkap menunjukkan aliran masuk dari "Pengembalian" (StockMovement type 'return', sudah defined).

## Goals
- Unit dapat menyusun daftar barang yang mau dikembalikan dari Permintaan yang sudah completed (atau manual), dengan qty ≤ sisa yang dapat dikembalikan.
- Sarpras/Admin menerima pengembalian → stok masuk kembali (batch baru + movement Return).
- Status flow jelas & idempoten: draft → submitted → received (atau cancelled dari draft/submitted).
- Laporan stok (Masuk) otomatis menangkap retur karena movement qty positif.
- Audit: siapa merekam penerimaan, kapan, dari request mana.

## Non-goals (MVP)
- Barang rusak/discard (Phase 2).
- Dedicated report pengembalian.
- Multi-condition handling.
- Partial return split across multiple events (awalnya cukup satu event per item_return).

## Constraints (existing)
- Role: Unit/Sarpras/Admin. Unit hanya lihat miliknya.
- Stock uses FIFO batches; receive creates new batch (mirrors PO receive).
- NumberGeneratorService pattern for unique return_number.
- Status filters in dahboard/API should remain unaffected by these changes.

## Design decisions (as decided)
- Entitas baru: ItemReturn (header) + ItemReturnItem (detail), paralel dengan Request/PurchaseOrder. Bukan extension dari Request.
- Menu dedicated "Pengembalian Barang" di sidebar (Unit, Sarpras, Admin).
- Return movement → creates new batch (safety + audit), not restore to original.
- Create form pulls items from completed request by unit (request_items.quantity_given - sum returned).
- Condition minimal 'usable' (damaged = phase 2).
- Approval: nerima = stock in (no separate approve state; cycle is submit → receive).
- Policy paralel ReturnPolicy selayaknya RequestPolicy.
- performed_by di movement = Sarpras receiver.

## Parts — what's left to builder
- form validation strictness (min 1 item, qty 0 validity).
- whether to allow submit without priority/needed-date (return tak butuh deadline).
- error handling batch.
- exact dashboard badge colors.

## Open (minor, builder decides)
- Detail display count for "remaining" derived rather than stored.
- Deletion guard (admin only, only draft).