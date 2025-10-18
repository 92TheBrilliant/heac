<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContactInquiry;
use App\Models\Page;
use App\Models\Research;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $editor;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test data
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $this->artisan('db:seed', ['--class' => 'DefaultAdminUserSeeder']);
        $this->artisan('db:seed', ['--class' => 'SampleContentSeeder']);
        
        // Create test users
        $this->admin = User::where('email', 'admin@heac.test')->first();
        
        $this->editor = User::factory()->create();
        $this->editor->assignRole('editor');
        
        $this->viewer = User::factory()->create();
        $this->viewer->assignRole('viewer');
        
        Storage::fake('public');
    }

    /**
     * Test admin can create a new page
     */
    public function test_admin_can_create_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/pages', [
                'title' => 'UAT Test Page',
                'slug' => 'uat-test-page',
                'content' => ['blocks' => [['type' => 'paragraph', 'content' => 'Test content']]],
                'status' => 'published',
                'published_at' => now(),
            ]);

        $this->assertDatabaseHas('pages', [
            'title' => 'UAT Test Page',
            'slug' => 'uat-test-page',
        ]);
    }

    /**
     * Test admin can edit existing page
     */
    public function test_admin_can_edit_page(): void
    {
        $page = Page::factory()->create(['status' => 'published']);
        
        $response = $this->actingAs($this->admin)
            ->put("/admin/pages/{$page->id}", [
                'title' => 'Updated Page Title',
                'slug' => $page->slug,
                'content' => $page->content,
                'status' => 'published',
            ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Updated Page Title',
        ]);
    }

    /**
     * Test admin can delete page
     */
    public function test_admin_can_delete_page(): void
    {
        $page = Page::factory()->create();
        
        $response = $this->actingAs($this->admin)
            ->delete("/admin/pages/{$page->id}");

        $this->assertSoftDeleted('pages', [
            'id' => $page->id,
        ]);
    }

    /**
     * Test admin can create research entry
     */
    public function test_admin_can_create_research(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        
        Storage::fake('public');
        $file = UploadedFile::fake()->create('research.pdf', 1000, 'application/pdf');
        
        $response = $this->actingAs($this->admin)
            ->post('/admin/research', [
                'title' => 'UAT Research Paper',
                'slug' => 'uat-research-paper',
                'abstract' => 'This is a test abstract',
                'authors' => ['John Doe', 'Jane Smith'],
                'publication_date' => now()->format('Y-m-d'),
                'file' => $file,
                'status' => 'published',
                'categories' => [$category->id],
                'tags' => [$tag->id],
            ]);

        $this->assertDatabaseHas('research', [
            'title' => 'UAT Research Paper',
        ]);
    }

    /**
     * Test admin can edit research entry
     */
    public function test_admin_can_edit_research(): void
    {
        $research = Research::factory()->create(['status' => 'published']);
        
        $response = $this->actingAs($this->admin)
            ->put("/admin/research/{$research->id}", [
                'title' => 'Updated Research Title',
                'slug' => $research->slug,
                'abstract' => $research->abstract,
                'authors' => $research->authors,
                'publication_date' => $research->publication_date->format('Y-m-d'),
                'status' => 'published',
            ]);

        $this->assertDatabaseHas('research', [
            'id' => $research->id,
            'title' => 'Updated Research Title',
        ]);
    }

    /**
     * Test admin can mark research as featured
     */
    public function test_admin_can_mark_research_as_featured(): void
    {
        $research = Research::factory()->create(['featured' => false]);
        
        $response = $this->actingAs($this->admin)
            ->put("/admin/research/{$research->id}", [
                'title' => $research->title,
                'slug' => $research->slug,
                'abstract' => $research->abstract,
                'authors' => $research->authors,
                'publication_date' => $research->publication_date->format('Y-m-d'),
                'status' => $research->status,
                'featured' => true,
            ]);

        $this->assertDatabaseHas('research', [
            'id' => $research->id,
            'featured' => true,
        ]);
    }

    /**
     * Test admin can view contact inquiries
     */
    public function test_admin_can_view_contact_inquiries(): void
    {
        ContactInquiry::factory()->count(5)->create();
        
        $response = $this->actingAs($this->admin)
            ->get('/admin/contact-inquiries');

        $response->assertStatus(200);
    }

    /**
     * Test admin can change inquiry status
     */
    public function test_admin_can_change_inquiry_status(): void
    {
        $inquiry = ContactInquiry::factory()->create(['status' => 'new']);
        
        $response = $this->actingAs($this->admin)
            ->put("/admin/contact-inquiries/{$inquiry->id}", [
                'status' => 'resolved',
            ]);

        $this->assertDatabaseHas('contact_inquiries', [
            'id' => $inquiry->id,
            'status' => 'resolved',
        ]);
    }

    /**
     * Test admin can create new user
     */
    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/users', [
                'name' => 'New Test User',
                'email' => 'newuser@heac.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['editor'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@heac.test',
        ]);
    }

    /**
     * Test admin can assign roles to users
     */
    public function test_admin_can_assign_roles(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($this->admin)
            ->put("/admin/users/{$user->id}", [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => ['admin'],
            ]);

        $this->assertTrue($user->fresh()->hasRole('admin'));
    }

    /**
     * Test editor cannot delete pages
     */
    public function test_editor_cannot_delete_pages(): void
    {
        $page = Page::factory()->create();
        
        $response = $this->actingAs($this->editor)
            ->delete("/admin/pages/{$page->id}");

        $response->assertForbidden();
    }

    /**
     * Test viewer cannot create pages
     */
    public function test_viewer_cannot_create_pages(): void
    {
        $response = $this->actingAs($this->viewer)
            ->post('/admin/pages', [
                'title' => 'Test Page',
                'slug' => 'test-page',
                'content' => ['blocks' => []],
                'status' => 'draft',
            ]);

        $response->assertForbidden();
    }

    /**
     * Test public homepage displays correctly
     */
    public function test_public_homepage_displays(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('HEAC');
    }

    /**
     * Test research listing page displays
     */
    public function test_research_listing_displays(): void
    {
        Research::factory()->count(5)->create(['status' => 'published']);
        
        $response = $this->get('/research');

        $response->assertStatus(200);
        $response->assertSee('Research');
    }

    /**
     * Test research search functionality
     */
    public function test_research_search_works(): void
    {
        Research::factory()->create([
            'title' => 'Unique Research Title',
            'status' => 'published',
        ]);
        
        $response = $this->get('/research?search=Unique');

        $response->assertStatus(200);
        $response->assertSee('Unique Research Title');
    }

    /**
     * Test research category filtering
     */
    public function test_research_category_filtering_works(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        $research = Research::factory()->create(['status' => 'published']);
        $research->categories()->attach($category);
        
        $response = $this->get("/research?category={$category->id}");

        $response->assertStatus(200);
        $response->assertSee($research->title);
    }

    /**
     * Test research detail page displays
     */
    public function test_research_detail_page_displays(): void
    {
        $research = Research::factory()->create(['status' => 'published']);
        
        $response = $this->get("/research/{$research->slug}");

        $response->assertStatus(200);
        $response->assertSee($research->title);
        $response->assertSee($research->abstract);
    }

    /**
     * Test research download tracking
     */
    public function test_research_download_tracking(): void
    {
        $research = Research::factory()->create([
            'status' => 'published',
            'downloads_count' => 0,
        ]);
        
        $response = $this->get("/research/{$research->slug}/download");

        $this->assertEquals(1, $research->fresh()->downloads_count);
    }

    /**
     * Test research view tracking
     */
    public function test_research_view_tracking(): void
    {
        $research = Research::factory()->create([
            'status' => 'published',
            'views_count' => 0,
        ]);
        
        $response = $this->get("/research/{$research->slug}");

        $this->assertGreaterThan(0, $research->fresh()->views_count);
    }

    /**
     * Test contact form submission
     */
    public function test_contact_form_submission(): void
    {
        $response = $this->post('/contact', [
            '_token' => csrf_token(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'subject' => 'Test Subject',
            'message' => 'This is a test message',
        ]);

        $this->assertDatabaseHas('contact_inquiries', [
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
        ]);
    }

    /**
     * Test contact form validation
     */
    public function test_contact_form_validation(): void
    {
        $response = $this->post('/contact', [
            '_token' => csrf_token(),
            'name' => '',
            'email' => 'invalid-email',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    /**
     * Test page displays correctly
     */
    public function test_page_displays_correctly(): void
    {
        $page = Page::factory()->create([
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        
        $response = $this->get("/{$page->slug}");

        $response->assertStatus(200);
        $response->assertSee($page->title);
    }

    /**
     * Test 404 page for non-existent pages
     */
    public function test_404_page_for_nonexistent_pages(): void
    {
        $response = $this->get('/nonexistent-page-12345');

        $response->assertStatus(404);
    }

    /**
     * Test navigation menu displays
     */
    public function test_navigation_menu_displays(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<nav', false);
    }

    /**
     * Test footer displays
     */
    public function test_footer_displays(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<footer', false);
    }

    /**
     * Test SEO meta tags are present
     */
    public function test_seo_meta_tags_present(): void
    {
        $page = Page::factory()->create([
            'status' => 'published',
            'meta_title' => 'Test Meta Title',
            'meta_description' => 'Test meta description',
        ]);
        
        $response = $this->get("/{$page->slug}");

        $response->assertSee('Test Meta Title', false);
        $response->assertSee('Test meta description', false);
    }

    /**
     * Test admin dashboard displays
     */
    public function test_admin_dashboard_displays(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /**
     * Test admin dashboard widgets display
     */
    public function test_admin_dashboard_widgets_display(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin');

        $response->assertStatus(200);
        // Check for widget content
        $response->assertSee('Pages');
        $response->assertSee('Research');
    }

    /**
     * Test pagination works on research listing
     */
    public function test_pagination_works_on_research_listing(): void
    {
        Research::factory()->count(20)->create(['status' => 'published']);
        
        $response = $this->get('/research');

        $response->assertStatus(200);
        $response->assertSee('pagination', false);
    }

    /**
     * Test responsive design meta tag present
     */
    public function test_responsive_design_meta_tag_present(): void
    {
        $response = $this->get('/');

        $response->assertSee('viewport', false);
        $response->assertSee('width=device-width', false);
    }
}
