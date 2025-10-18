# Content Update Guide for HEAC Website

## Quick Start: Where to Update Content

### 1. Access the Admin Panel
**URL:** `http://127.0.0.1:8080/admin`

**Default Login:**
- Email: `admin@heac.com`
- Password: (check `database/seeders/DefaultAdminUserSeeder.php`)

### 2. Main Content Areas

#### A. Pages (About, Services, Training, Team)
**Location:** Admin Panel → Pages

**How to Update:**
1. Go to `/admin/pages`
2. Click on the page you want to edit (About, Services, Training, Team)
3. Update the content
4. Click "Save"
5. Clear cache: `php artisan cache:clear`

**Current Pages:**
- **About** (`/about`) - Company information, mission, vision
- **Services** (`/services`) - List of Islamic finance services
- **Training** (`/training`) - Training programs and workshops
- **Team** (`/team`) - Team members and scholars

#### B. Publications/Resources
**Location:** Admin Panel → Research

**How to Add:**
1. Go to `/admin/research`
2. Click "New Research"
3. Fill in:
   - Title
   - Abstract/Summary
   - Authors (with affiliations)
   - Publication Date
   - Categories (Shariah Advisory, Sukuk, etc.)
   - Tags (Islamic Banking, Takaful, etc.)
   - Upload PDF file (optional)
4. Set as "Published"
5. Click "Save"

**Categories Available:**
- Shariah Advisory
- Sukuk & Structuring
- Halal Certification
- Audit & Compliance
- Training & Education

**Tags Available:**
- Islamic Banking
- Takaful
- Sukuk
- Shariah Compliance
- Halal Business
- Zakat
- Islamic Finance
- Fintech
- Crypto & Blockchain
- Investment Screening

#### C. Homepage Content
**Location:** Admin Panel → Pages → Home (if exists) OR edit `resources/views/home.blade.php`

**What You Can Update:**
- Hero title and subtitle
- Featured services
- Statistics (edit `app/Http/Controllers/HomeController.php`)
- Call-to-action text

#### D. Contact Form Inquiries
**Location:** Admin Panel → Contact Inquiries

**View submissions at:** `/admin/contact-inquiries`

### 3. Content Types to Add

#### Publications/Resources
Based on your content plan, add these types:

**1. Fatwas (Islamic Legal Opinions)**
- Title: "Fatwa on [Topic]"
- Category: Shariah Advisory
- Tags: Relevant Islamic finance tags
- Upload PDF of the fatwa

**2. Research Papers**
- Title: Academic or industry research title
- Category: Relevant category
- Authors: Include scholar names and affiliations
- Upload PDF

**3. Whitepapers**
- Title: Industry report or guide title
- Category: Relevant category
- Abstract: Executive summary
- Upload PDF

**4. Market Reports**
- Title: "Sukuk Market Analysis 2024" etc.
- Category: Sukuk & Structuring
- Include charts/data in PDF

**5. Guides & How-Tos**
- Title: "Guide to Shariah-Compliant Crypto"
- Category: Relevant category
- Practical implementation guides

### 4. Adding Team Members

**Current Method:**
Edit the Team page content through Admin Panel → Pages → Team

**Recommended Structure:**
```
## Our Leadership

### Dr. [Name]
**Position:** CEO / Chief Shariah Advisor
**Qualifications:** [Degrees, certifications]
**Experience:** [Years of experience, previous roles]

### Sheikh [Name]
**Position:** Senior Shariah Scholar
**Qualifications:** [Islamic law qualifications]
**Specialization:** [Area of expertise]
```

### 5. Adding Training Programs

**Location:** Admin Panel → Pages → Training

**Add courses like:**
- Islamic Banking Fundamentals
- Advanced Shariah Audit
- Sukuk Structuring Workshop
- Halal Business Management
- Fintech & Shariah Compliance

**Include:**
- Course title
- Duration
- Target audience
- Learning objectives
- Registration link/button

### 6. Updating Services

**Location:** Admin Panel → Pages → Services

**Current Services to Expand:**
1. Shariah Advisory & Consultancy
2. Shariah Audit & Compliance
3. Sukuk Structuring
4. Halal Certification
5. Investment Screening
6. Zakat Advisory
7. Training & Education

**For Each Service, Include:**
- Detailed description
- What's included
- Benefits
- Process/methodology
- Case studies (optional)
- Call-to-action button

### 7. Media/Images

**Location:** Admin Panel → Media

**How to Upload:**
1. Go to `/admin/media`
2. Click "Upload"
3. Select images/files
4. Organize in folders
5. Use in pages and publications

**Recommended Images:**
- Team member photos
- Office photos
- Event photos
- Infographics
- Service illustrations

## After Making Changes

### Always Clear Cache
```bash
php artisan cache:clear
php artisan view:clear
```

### Or use the optimization script:
```bash
.\optimize-performance.ps1
```

## Common Tasks

### Task 1: Add a New Publication
1. Go to `/admin/research`
2. Click "New Research"
3. Fill in all fields
4. Select appropriate category and tags
5. Upload PDF file
6. Set status to "Published"
7. Save
8. Clear cache

### Task 2: Update About Page
1. Go to `/admin/pages`
2. Find "About HEAC"
3. Click to edit
4. Update content blocks
5. Save
6. Clear cache
7. Visit `/about` to verify

### Task 3: Add Team Member
1. Go to `/admin/pages`
2. Find "Our Team & Scholars"
3. Edit content
4. Add new team member section
5. Include photo (upload to Media first)
6. Save
7. Clear cache

### Task 4: Update Services
1. Go to `/admin/pages`
2. Find "Our Services"
3. Edit content
4. Add/update service descriptions
5. Save
6. Clear cache

## Content Format Tips

### For Page Content (JSON Format)
The pages use a block-based content system. Each block has a type:

**Heading:**
```json
{
    "type": "heading",
    "content": "Your Heading Text"
}
```

**Paragraph:**
```json
{
    "type": "paragraph",
    "content": "Your paragraph text here."
}
```

**List:**
```json
{
    "type": "list",
    "content": [
        "First item",
        "Second item",
        "Third item"
    ]
}
```

### For Authors (in Publications)
```json
[
    {
        "name": "Dr. Ahmad Al-Hassan",
        "affiliation": "HEAC Shariah Board"
    },
    {
        "name": "Sheikh Muhammad Al-Qadir",
        "affiliation": "Chief Shariah Advisor"
    }
]
```

## Troubleshooting

### Pages Show 500 Error
1. Clear all caches: `php artisan optimize:clear`
2. Check if page exists in database
3. Check error logs: `storage/logs/laravel.log`

### Changes Not Showing
1. Clear cache: `php artisan cache:clear`
2. Hard refresh browser: `Ctrl + Shift + R` (Windows) or `Cmd + Shift + R` (Mac)

### Can't Login to Admin
1. Check credentials in `.env` file
2. Run seeder: `php artisan db:seed --class=DefaultAdminUserSeeder`

### Images Not Displaying
1. Check if file was uploaded to `/admin/media`
2. Verify storage link: `php artisan storage:link`
3. Check file permissions

## Quick Reference

### Admin Panel Sections
- **Dashboard** - Overview and statistics
- **Pages** - About, Services, Training, Team
- **Research** - Publications, Fatwas, Reports
- **Media** - Images and files
- **Categories** - Publication categories
- **Tags** - Publication tags
- **Contact Inquiries** - Form submissions
- **Users** - Admin users management

### Important URLs
- Homepage: `/`
- About: `/about`
- Services: `/services`
- Training: `/training`
- Team: `/team`
- Publications: `/research`
- Contact: `/contact`
- Admin Panel: `/admin`

### Support Files
- `ADMIN_USER_GUIDE.md` - Detailed admin guide
- `ISLAMIC_FINANCE_UPDATE_SUMMARY.md` - Recent changes
- `PERFORMANCE_OPTIMIZATION_GUIDE.md` - Performance tips
- `LOGO_SETUP_GUIDE.md` - Logo replacement guide

## Need Help?
Refer to the detailed guides in the project root or check `storage/logs/laravel.log` for errors.
