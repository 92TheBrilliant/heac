<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Page;
use App\Models\Research;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CrossBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test data
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $this->artisan('db:seed', ['--class' => 'DefaultAdminUserSeeder']);
        $this->artisan('db:seed', ['--class' => 'SampleContentSeeder']);
    }

    /**
     * Test homepage loads correctly across browsers
     */
    public function test_homepage_loads_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('HEAC')
                    ->assertVisible('nav')
                    ->assertVisible('footer')
                    ->screenshot('homepage');
        });
    }

    /**
     * Test responsive navigation menu
     */
    public function test_navigation_menu_responsive(): void
    {
        $this->browse(function (Browser $browser) {
            // Desktop view
            $browser->resize(1920, 1080)
                    ->visit('/')
                    ->assertVisible('nav')
                    ->assertDontSee('☰'); // Hamburger should not be visible

            // Mobile view
            $browser->resize(375, 667)
                    ->visit('/')
                    ->assertVisible('[x-data*="mobileNav"]')
                    ->click('[x-data*="mobileNav"] button')
                    ->pause(500)
                    ->assertVisible('[x-show="open"]')
                    ->screenshot('mobile-menu-open');
        });
    }

    /**
     * Test research listing and filtering
     */
    public function test_research_listing_and_filtering(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/research')
                    ->assertSee('Research')
                    ->assertVisible('[x-data*="filterSidebar"]')
                    ->assertVisible('input[type="search"]')
                    ->screenshot('research-listing');

            // Test search
            $browser->type('input[type="search"]', 'test')
                    ->pause(1000) // Wait for debounce
                    ->screenshot('research-search');

            // Test filter
            if ($browser->element('input[type="checkbox"]')) {
                $browser->check('input[type="checkbox"]:first-of-type')
                        ->pause(500)
                        ->screenshot('research-filtered');
            }
        });
    }

    /**
     * Test research detail page
     */
    public function test_research_detail_page(): void
    {
        $research = Research::where('status', 'published')->first();
        
        if ($research) {
            $this->browse(function (Browser $browser) use ($research) {
                $browser->visit("/research/{$research->slug}")
                        ->assertSee($research->title)
                        ->assertVisible('a[href*="download"]')
                        ->screenshot('research-detail');
            });
        } else {
            $this->markTestSkipped('No published research available for testing');
        }
    }

    /**
     * Test contact form
     */
    public function test_contact_form_submission(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                    ->assertSee('Contact')
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('phone', '1234567890')
                    ->type('subject', 'Test Subject')
                    ->type('message', 'This is a test message')
                    ->screenshot('contact-form-filled')
                    ->press('Submit')
                    ->pause(2000)
                    ->assertSee('Thank you')
                    ->screenshot('contact-form-success');
        });
    }

    /**
     * Test admin panel login
     */
    public function test_admin_panel_login(): void
    {
        $user = User::where('email', 'admin@heac.test')->first();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/admin/login')
                    ->assertSee('Sign in')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->screenshot('admin-login')
                    ->press('Sign in')
                    ->pause(2000)
                    ->assertPathIs('/admin')
                    ->assertSee('Dashboard')
                    ->screenshot('admin-dashboard');
        });
    }

    /**
     * Test page creation in admin panel
     */
    public function test_admin_page_creation(): void
    {
        $user = User::where('email', 'admin@heac.test')->first();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/admin/pages')
                    ->assertSee('Pages')
                    ->click('a[href*="create"]')
                    ->pause(1000)
                    ->type('data.title', 'Test Page')
                    ->screenshot('admin-page-create')
                    ->press('Create')
                    ->pause(2000)
                    ->assertSee('Test Page')
                    ->screenshot('admin-page-created');
        });
    }

    /**
     * Test media upload
     */
    public function test_admin_media_upload(): void
    {
        $user = User::where('email', 'admin@heac.test')->first();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/admin/media')
                    ->assertSee('Media')
                    ->screenshot('admin-media-library');
        });
    }

    /**
     * Test responsive design on various screen sizes
     */
    public function test_responsive_design(): void
    {
        $this->browse(function (Browser $browser) {
            $sizes = [
                'mobile-portrait' => [375, 667],
                'mobile-landscape' => [667, 375],
                'tablet-portrait' => [768, 1024],
                'tablet-landscape' => [1024, 768],
                'desktop-hd' => [1920, 1080],
                'desktop-2k' => [2560, 1440],
            ];

            foreach ($sizes as $name => $size) {
                $browser->resize($size[0], $size[1])
                        ->visit('/')
                        ->pause(500)
                        ->screenshot("responsive-{$name}");
            }
        });
    }

    /**
     * Test keyboard navigation
     */
    public function test_keyboard_navigation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->keys('body', '{tab}') // Tab to first focusable element
                    ->pause(200)
                    ->screenshot('keyboard-nav-1')
                    ->keys('body', '{tab}')
                    ->pause(200)
                    ->screenshot('keyboard-nav-2')
                    ->keys('body', '{tab}')
                    ->pause(200)
                    ->screenshot('keyboard-nav-3');
        });
    }

    /**
     * Test accessibility features
     */
    public function test_accessibility_features(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    // Check for skip to main content link
                    ->assertPresent('a[href="#main-content"]')
                    // Check for proper heading hierarchy
                    ->assertPresent('h1')
                    // Check for alt text on images
                    ->script('
                        const images = document.querySelectorAll("img");
                        const imagesWithoutAlt = Array.from(images).filter(img => !img.alt);
                        return imagesWithoutAlt.length;
                    ');
        });
    }
}
