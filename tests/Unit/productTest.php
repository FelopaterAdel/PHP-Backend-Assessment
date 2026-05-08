<?php
namespace productTest;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
class ProductTest extends TestCase
{

    use RefreshDatabase;

    public function test_add_product(): void
    {
           $payload = [
             'name' => 'laptop',
             'sku' => 'lab-001',
             'price' => 99.99,
             'stock_quantity' => 50,
             'low_stock_threshold' => 5,
             'status' => 'active',
       ];
       
        $response = $this->postJson('/api/products', $payload);
        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'laptop', 'sku' => 'lab-001']);
        $this->assertDatabaseHas('products', ['sku' => 'lab-001']);
    }

    public function test_get_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->getJson("/api/products");
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => $product->name, 'sku' => $product->sku]);
    }


    public function test_update_product(): void
    {
        $product = Product::factory()->create();
        $payload = [
            'name' => 'Updated Product',
            'price' => 149.99,
        ];
        $response = $this->putJson("/api/products/{$product->id}", $payload);
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Product', 'price' => 149.99]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Product']);
    }

    public function test_delete_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->deleteJson("/api/products/{$product->id}");
        $response->assertStatus(204);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_get_low_stock_products(): void
    {
        // Create product with low stock (stock_quantity <= low_stock_threshold)
        Product::factory()->create(['stock_quantity' => 5, 'low_stock_threshold' => 10]);
        // Create product with adequate stock (stock_quantity > low_stock_threshold)
        Product::factory()->create(['stock_quantity' => 15, 'low_stock_threshold' => 10]);
        
        $response = $this->getJson('/api/products/low-stock');
        
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['stock_quantity' => 5]);
    }

    public function test_adjust_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 20]);
        $payload = ['quantity' => -5];
        $response = $this->postJson("/api/products/{$product->id}/stock", $payload);
        $response->assertStatus(200)
                 ->assertJsonFragment(['stock_quantity' => 15]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 15]);
    }
}
        
