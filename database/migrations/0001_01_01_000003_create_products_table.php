<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla de Categorías
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        // Tabla de Productos/Menú
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('stock')->default(0);
            $table->boolean('track_inventory')->default(true);
            $table->decimal('cost', 8, 2)->nullable(); // Costo del producto
            $table->timestamps();
        });
        
        // Tabla de Inventario
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit'); // kg, litros, unidades, etc.
            $table->decimal('quantity', 10, 2);
            $table->decimal('alert_threshold', 10, 2)->nullable(); // Para notificaciones de stock bajo
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });
        
        // Tabla de asignación de inventario a productos
        Schema::create('product_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('quantity', 8, 3); // Cantidad que usa el producto
            $table->timestamps();
        });

        // Tabla de mesas
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->integer('capacity');
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->timestamps();
        });

        // Tabla de Pedidos
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Cliente registrado
            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete(); // Para mesas físicas
            $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete(); // Empleado que atiende
            $table->string('customer_name')->nullable(); // Para clientes no registrados
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'preparing', 'ready', 'delivered', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable(); // ID de pago
            $table->text('notes')->nullable();
            $table->enum('order_type', ['dine_in', 'takeaway', 'delivery', 'online'])->default('dine_in');
            $table->timestamps();
        });
        
        // Tabla de Items de Pedido (líneas de pedido)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });
        
        // Movimientos de inventario
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->enum('type', ['purchase', 'usage', 'adjustment', 'waste']);
            $table->decimal('quantity', 10, 2); // Positivo para entradas, negativo para salidas
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained(); // Usuario que registra
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
        // Reservas
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone');
            $table->integer('num_guests');
            $table->dateTime('reservation_time');
            $table->integer('duration')->default(60); // En minutos
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('product_inventory');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
