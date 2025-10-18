<?php

namespace Tests\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test data
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $this->artisan('db:seed', ['--class' => 'DefaultAdminUserSeeder']);
    }

    /**
     * Test CSRF protection on forms
     */
    public function test_csrf_protection_on_contact_form(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ]);

        // Should fail without CSRF token
        $response->assertStatus(419);
    }

    /**
     * Test XSS protection in input fields
     */
    public function test_xss_protection_in_contact_form(): void
    {
        $xssPayload = '<script>alert("XSS")</script>';
        
        $response = $this->post('/contact', [
            '_token' => csrf_token(),
            'name' => $xssPayload,
            'email' => 'test@example.com',
            'message' => $xssPayload,
        ]);

        // Check that the response doesn't contain unescaped script tags
        $this->assertStringNotContainsString('<script>alert("XSS")</script>', $response->getContent());
    }

    /**
     * Test SQL injection protection
     */
    public function test_sql_injection_protection_in_search(): void
    {
        $sqlInjection = "' OR '1'='1";
        
        $response = $this->get('/research?search=' . urlencode($sqlInjection));
        
        // Should not cause an error and should be handled safely
        $response->assertStatus(200);
    }

    /**
     * Test authentication rate limiting
     */
    public function test_login_rate_limiting(): void
    {
        $credentials = [
            'email' => 'admin@heac.test',
            'password' => 'wrongpassword',
        ];

        // Attempt multiple failed logins
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/admin/login', $credentials);
        }

        // Should be rate limited after multiple attempts
        $response->assertStatus(429);
    }

    /**
     * Test password hashing
     */
    public function test_passwords_are_hashed(): void
    {
        $user = User::where('email', 'admin@heac.test')->first();
        
        // Password should be hashed, not plain text
        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    /**
     * Test file upload restrictions
     */
    public function test_file_upload_restrictions(): void
    {
        $user = User::where('email', 'admin@heac.test')->first();
        
        // Create a fake PHP file
        $file = \Illuminate\Http\UploadedFile::fake()->create('malicious.php', 100);
        
        $response = $this->actingAs($user)
            ->post('/admin/media', [
                'file' => $file,
            ]);

        // Should reject PHP files
        $response->assertSessionHasErrors();
    }

    /**
     * Test authorization on admin routes
     */
    public function test_admin_routes_require_authentication(): void
    {
        $response = $this->get('/admin');
        
        // Should redirect to login
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test role-based access control
     */
    public function test_role_based_access_control(): void
    {
        // Create a viewer user (read-only)
        $viewer = User::factory()->create();
        $viewer->assignRole('viewer');
        
        $response = $this->actingAs($viewer)
            ->post('/admin/pages', [
                'title' => 'Test Page',
                'content' => 'Test content',
            ]);

        // Viewer should not be able to create pages
        $response->assertForbidden();
    }

    /**
     * Test session security
     */
    public function test_session_security(): void
    {
        $user = User::where('email', 'admin@heac.test')->first();
        
        $response = $this->actingAs($user)->get('/admin');
        
        // Check for secure session cookie attributes
        $cookies = $response->headers->getCookies();
        
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === config('session.cookie')) {
                $this->assertTrue($cookie->isHttpOnly(), 'Session cookie should be HttpOnly');
                
                if (config('session.secure')) {
                    $this->assertTrue($cookie->isSecure(), 'Session cookie should be Secure in production');
                }
            }
        }
    }

    /**
     * Test security headers
     */
    public function test_security_headers_present(): void
    {
        $response = $this->get('/');
        
        // Check for important security headers
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options');
        $response->assertHeader('X-XSS-Protection');
    }

    /**
     * Test HTTPS redirect in production
     */
    public function test_https_redirect_in_production(): void
    {
        // This test should be run in production environment
        if (app()->environment('production')) {
            $response = $this->get('http://example.com/');
            $response->assertRedirect();
            $this->assertStringStartsWith('https://', $response->headers->get('Location'));
        } else {
            $this->markTestSkipped('HTTPS redirect only applies in production');
        }
    }

    /**
     * Test contact form rate limiting
     */
    public function test_contact_form_rate_limiting(): void
    {
        // Submit contact form multiple times
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/contact', [
                '_token' => csrf_token(),
                'name' => 'Test User',
                'email' => 'test@example.com',
                'message' => 'Test message',
            ]);
        }

        // Should be rate limited
        $response->assertStatus(429);
    }

    /**
     * Test honeypot spam protection
     */
    public function test_honeypot_spam_protection(): void
    {
        $response = $this->post('/contact', [
            '_token' => csrf_token(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
            'website' => 'http://spam.com', // Honeypot field
        ]);

        // Should reject if honeypot is filled
        $response->assertStatus(422);
    }

    /**
     * Test mass assignment protection
     */
    public function test_mass_assignment_protection(): void
    {
        $user = User::where('email', 'admin@heac.test')->first();
        
        $response = $this->actingAs($user)
            ->post('/admin/pages', [
                'title' => 'Test Page',
                'content' => 'Test content',
                'id' => 999, // Try to set ID
                'created_at' => now()->subYears(5), // Try to set timestamp
            ]);

        // Mass assignment should be protected
        if ($response->isSuccessful()) {
            $page = \App\Models\Page::where('title', 'Test Page')->first();
            $this->assertNotEquals(999, $page->id);
        }
    }

    /**
     * Test sensitive data is not exposed in errors
     */
    public function test_sensitive_data_not_exposed_in_errors(): void
    {
        // Force an error
        $response = $this->get('/nonexistent-page');
        
        $content = $response->getContent();
        
        // Should not expose sensitive information
        $this->assertStringNotContainsString('DB_PASSWORD', $content);
        $this->assertStringNotContainsString('APP_KEY', $content);
        $this->assertStringNotContainsString('database', strtolower($content));
    }

    /**
     * Test audit logging for admin actions
     */
    public function test_audit_logging_for_admin_actions(): void
    {
        $user = User::where('email', 'admin@heac.test')->first();
        
        $this->actingAs($user)
            ->post('/admin/pages', [
                'title' => 'Test Page',
                'slug' => 'test-page',
                'content' => ['blocks' => []],
                'status' => 'draft',
            ]);

        // Check if activity was logged
        $this->assertDatabaseHas('activity_log', [
            'causer_id' => $user->id,
            'causer_type' => User::class,
        ]);
    }

    /**
     * Test input sanitization
     */
    public function test_input_sanitization(): void
    {
        $maliciousInput = '<img src=x onerror=alert(1)>';
        
        $response = $this->post('/contact', [
            '_token' => csrf_token(),
            'name' => $maliciousInput,
            'email' => 'test@example.com',
            'message' => $maliciousInput,
        ]);

        // Check database for sanitized input
        $this->assertDatabaseMissing('contact_inquiries', [
            'name' => $maliciousInput,
        ]);
    }

    /**
     * Test file path traversal protection
     */
    public function test_file_path_traversal_protection(): void
    {
        $user = User::where('email', 'admin@heac.test')->first();
        
        // Try to access file with path traversal
        $response = $this->actingAs($user)
            ->get('/storage/../../../etc/passwd');

        // Should not allow path traversal
        $response->assertStatus(404);
    }

    /**
     * Test open redirect protection
     */
    public function test_open_redirect_protection(): void
    {
        $response = $this->get('/redirect?url=http://evil.com');
        
        // Should not redirect to external URLs
        if ($response->isRedirect()) {
            $location = $response->headers->get('Location');
            $this->assertStringNotContainsString('evil.com', $location);
        }
    }

    /**
     * Test debug mode is disabled in production
     */
    public function test_debug_mode_disabled_in_production(): void
    {
        if (app()->environment('production')) {
            $this->assertFalse(config('app.debug'), 'Debug mode should be disabled in production');
        } else {
            $this->markTestSkipped('Debug mode check only applies in production');
        }
    }
}
