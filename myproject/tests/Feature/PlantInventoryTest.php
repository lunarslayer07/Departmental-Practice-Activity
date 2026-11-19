<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlantInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_requires_authentication(): void
    {
        $this->get(route('plants.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_log_in_with_email_or_username_and_log_out(): void
    {
        $user = User::factory()->create(['username' => 'greenkeeper']);

        $this->post(route('login.store'), [
            'identifier' => $user->username,
            'password' => 'password',
        ])->assertRedirect(route('plants.index'));

        $this->assertAuthenticatedAs($user);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->post(route('login.store'), [
            'identifier' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('plants.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_plant_creation_validates_price_and_records_creator(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('plants.store'), [
            'name' => 'Aloe Vera',
            'category' => 'Succulent',
            'unit_price' => '-1',
            'description' => 'A hardy indoor plant.',
            'stock_status' => 'in_stock',
        ])->assertSessionHasErrors('unit_price');

        $this->assertDatabaseCount('plants', 0);

        $this->post(route('plants.store'), [
            'name' => 'Aloe Vera',
            'category' => 'Succulent',
            'unit_price' => '12.50',
            'description' => 'A hardy indoor plant.',
            'stock_status' => 'in_stock',
        ])->assertRedirect();

        $this->assertDatabaseHas('plants', [
            'name' => 'Aloe Vera',
            'user_id' => $user->id,
            'unit_price' => '12.50',
        ]);
        $this->assertSame(1, $user->plants()->count());
    }

    public function test_inventory_row_has_a_delete_action_that_removes_the_plant(): void
    {
        $user = User::factory()->create();
        $plant = $user->plants()->create([
            'name' => 'Garden Mint',
            'category' => 'Herb',
            'unit_price' => 4,
            'description' => 'Fresh mint.',
            'stock_status' => 'in_stock',
        ]);

        $this->actingAs($user)
            ->get(route('plants.index'))
            ->assertOk()
            ->assertSee(route('plants.destroy', $plant))
            ->assertSee('>Delete</button>', false);

        $this->delete(route('plants.destroy', $plant))
            ->assertRedirect(route('plants.index'));

        $this->assertDatabaseMissing('plants', ['id' => $plant->id]);
    }

    public function test_report_filters_are_reflected_in_excel_and_pdf_exports(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $matching = $user->plants()->create([
            'name' => 'Sunset Aloe',
            'category' => 'Succulent',
            'unit_price' => 14.25,
            'description' => 'Orange bloom.',
            'stock_status' => 'in_stock',
        ]);
        $user->plants()->create([
            'name' => 'Empty Pot',
            'category' => 'Succulent',
            'unit_price' => 8,
            'description' => 'Not available.',
            'stock_status' => 'out_of_stock',
        ]);
        $user->plants()->create([
            'name' => 'Kitchen Thyme',
            'category' => 'Herb',
            'unit_price' => 3,
            'description' => 'Fragrant herb.',
            'stock_status' => 'in_stock',
        ]);
        $filters = ['report' => 'in_stock', 'category' => 'Succulent'];

        $this->get(route('reports.plants', $filters))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee('Empty Pot')
            ->assertDontSee('Kitchen Thyme');

        $this->get(route('reports.plants.excel', $filters))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8')
            ->assertSee($matching->name)
            ->assertDontSee('Empty Pot')
            ->assertDontSee('Kitchen Thyme');

        $pdf = $this->get(route('reports.plants.pdf', $filters));
        $pdf->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $pdf->getContent());
        $this->assertStringContainsString('Sunset Aloe', $pdf->getContent());
        $this->assertStringNotContainsString('Empty Pot', $pdf->getContent());
    }

    public function test_plant_photos_can_be_uploaded_replaced_and_removed(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);
        $details = [
            'name' => 'Fern',
            'category' => 'Ornamental',
            'unit_price' => '9.50',
            'description' => 'A leafy indoor plant.',
            'stock_status' => 'in_stock',
        ];
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lfoAAAAASUVORK5CYII=');

        $this->post(route('plants.store'), $details + [
            'image' => UploadedFile::fake()->createWithContent('fern.png', $png),
        ])->assertRedirect();

        $plant = Plant::where('name', 'Fern')->firstOrFail();
        $originalImage = $plant->image_path;
        Storage::disk('public')->assertExists($originalImage);
        $this->get(route('plants.show', $plant))->assertOk()->assertSee('storage/'.$originalImage);

        $this->put(route('plants.update', $plant), $details + [
            'image' => UploadedFile::fake()->createWithContent('fern-new.png', $png),
        ])->assertRedirect();

        $plant->refresh();
        $this->assertNotSame($originalImage, $plant->image_path);
        Storage::disk('public')->assertMissing($originalImage);
        Storage::disk('public')->assertExists($plant->image_path);

        $this->delete(route('plants.destroy', $plant))->assertRedirect(route('plants.index'));
        Storage::disk('public')->assertMissing($plant->image_path);
    }
}
