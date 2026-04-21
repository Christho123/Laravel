<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        if (Schema::hasColumn('products', 'price') && !Schema::hasColumn('products', 'price_purchase')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->decimal('price_purchase', 10, 2)->default(0)->after('description');
            });

            DB::statement('UPDATE products SET price_purchase = price WHERE price_purchase IS NULL');
        }

        if (!Schema::hasColumn('products', 'price_sale')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->decimal('price_sale', 10, 2)->default(0)->after('price_purchase');
            });
        }

        if (Schema::hasColumn('products', 'price')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->dropColumn('price');
            });
        }

        if (Schema::hasColumn('products', 'supplier_id')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            });
        }

    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        if (!Schema::hasColumn('products', 'price')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->decimal('price', 10, 2)->nullable()->after('description');
            });
        }

        if (!Schema::hasColumn('products', 'supplier_id')) {
            Schema::table('products', function (Blueprint $table): void {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            });
        }
    }
};
