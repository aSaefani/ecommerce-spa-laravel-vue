<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcommerceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_customer_can_register_and_browse_products(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'budi@example.com', 'role' => 'customer']);

        $response = $this->get('/products');
        $response->assertStatus(200);
    }

    public function test_customer_cart_and_checkout_flow(): void
    {
        $customer = User::where('role', 'customer')->first();
        $product = Product::first();

        $response = $this->actingAs($customer)->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $response->assertRedirect();

        $response = $this->actingAs($customer)->get('/cart');
        $response->assertStatus(200);

        $initialStock = $product->stock;
        $response = $this->actingAs($customer)->post('/checkout/process', [
            'discount_code' => 'HEMAT10',
        ]);

        $order = Order::where('user_id', $customer->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals($initialStock - 2, $product->fresh()->stock);
    }

    public function test_cashier_can_confirm_payment_and_customer_earns_points(): void
    {
        $cashier = User::where('role', 'cashier')->first();
        $order = Order::where('status', 'pending')->first();

        $response = $this->actingAs($cashier)->post("/cashier/orders/{$order->id}/confirm-payment");
        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertGreaterThan(0, $order->points_earned);
        $this->assertDatabaseHas('customer_points', [
            'order_id' => $order->id,
            'type' => 'earn',
        ]);
    }

    public function test_admin_can_access_daily_report(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/reports/daily');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Harian');
    }
}
