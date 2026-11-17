<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add CAPEX/OPEX expense type and group asset flag to purchase_orders.
     *
     * Defaults:
     *   - expense_type = 'capex'  → existing POs remain eligible for asset conversion.
     *   - is_group_asset = false  → existing POs retain per-unit asset behaviour.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'expense_type')) {
                $table->string('expense_type', 10)
                    ->default('capex')
                    ->after('status')
                    ->comment('capex = eligible for asset conversion; opex = not eligible');
            }

            if (! Schema::hasColumn('purchase_orders', 'is_group_asset')) {
                $table->boolean('is_group_asset')
                    ->default(false)
                    ->after('expense_type')
                    ->comment('CAPEX only: true = one grouped asset record; false = one asset per unit');
            }
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'is_group_asset')) {
                $table->dropColumn('is_group_asset');
            }

            if (Schema::hasColumn('purchase_orders', 'expense_type')) {
                $table->dropColumn('expense_type');
            }
        });
    }
};

