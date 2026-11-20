<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::create([
            'username' => 'staff',
            'full_name' => 'Staff Member',
            'password' => 'staff-password',
            'is_active' => true,
        ]));

        Storage::fake('public');
    }

    public function test_staff_can_create_a_product_with_a_displayable_image(): void
    {
        $this->post(route('products.store'), [
            'name' => 'Wireless Keyboard',
            'price' => '1299.50',
            'stock_quantity' => 12,
            'image' => $this->fakePng('keyboard.png'),
        ])->assertRedirect(route('products.index'));

        $product = Product::firstOrFail();

        $this->assertSame('Wireless Keyboard', $product->name);
        $this->assertSame('1299.50', $product->price);
        $this->assertSame(12, $product->stock_quantity);
        Storage::disk('public')->assertExists($product->image);

        $this->get(route('products.index'))->assertOk()->assertSee('Wireless Keyboard');
    }

    public function test_product_fields_are_validated(): void
    {
        $this->from(route('products.create'))
            ->post(route('products.store'), [
                'name' => str_repeat('x', 101),
                'price' => '-1',
                'stock_quantity' => '-2',
                'image' => UploadedFile::fake()->create('notes.txt', 5, 'text/plain'),
            ])
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors(['name', 'price', 'stock_quantity', 'image']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_image_can_be_stored_in_imagekit(): void
    {
        config([
            'services.imagekit.driver' => 'imagekit',
            'services.imagekit.private_key' => 'imagekit-private-test-key',
        ]);
        Http::fake([
            'https://upload.imagekit.io/api/v1/files/upload' => Http::response([
                'url' => 'https://ik.imagekit.io/demo/products/product-test.png',
                'fileId' => 'imagekit-file-test-id',
            ]),
        ]);

        $this->post(route('products.store'), [
            'name' => 'Cloud image product',
            'price' => '25.00',
            'stock_quantity' => 2,
            'image' => $this->fakePng('product.png'),
        ])->assertRedirect(route('products.index'));

        $product = Product::where('name', 'Cloud image product')->firstOrFail();
        $this->assertSame('https://ik.imagekit.io/demo/products/product-test.png', $product->image);
        $this->assertSame('imagekit-file-test-id', $product->image_file_id);
        $this->assertSame('https://ik.imagekit.io/demo/products/product-test.png', $product->image_url);
        Http::assertSent(fn ($request) => $request->url() === 'https://upload.imagekit.io/api/v1/files/upload');
    }

    public function test_staff_can_update_a_product_and_replace_its_image(): void
    {
        Storage::disk('public')->put('products/old.png', 'old image');
        $product = Product::create([
            'name' => 'Old name',
            'price' => '10.00',
            'stock_quantity' => 3,
            'image' => 'products/old.png',
            'is_active' => true,
        ]);

        $this->put(route('products.update', $product), [
            'name' => 'New name',
            'price' => '12.50',
            'stock_quantity' => 8,
            'image' => $this->fakePng('new-image.png'),
        ])->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertSame('New name', $product->name);
        $this->assertSame('12.50', $product->price);
        $this->assertSame(8, $product->stock_quantity);
        $this->assertNotSame('products/old.png', $product->image);
        Storage::disk('public')->assertMissing('products/old.png');
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_staff_can_archive_and_restore_a_product(): void
    {
        $product = Product::create([
            'name' => 'Archived item',
            'price' => '9.99',
            'stock_quantity' => 4,
            'is_active' => true,
        ]);

        $this->patch(route('products.archive', $product))->assertRedirect(route('products.index'));
        $this->assertFalse($product->fresh()->is_active);
        $this->get(route('products.index'))->assertDontSee('Archived item');
        $this->get(route('products.index', ['status' => 'archived']))->assertSee('Archived item');

        $this->patch(route('products.restore', $product))->assertRedirect(route('products.index', ['status' => 'archived']));
        $this->assertTrue($product->fresh()->is_active);
    }

    public function test_guests_cannot_access_product_management(): void
    {
        auth()->logout();

        $this->get(route('products.index'))->assertRedirect(route('login'));
        $this->get(route('products.create'))->assertRedirect(route('login'));
    }

    private function fakePng(string $filename): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $filename,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
        );
    }
}
