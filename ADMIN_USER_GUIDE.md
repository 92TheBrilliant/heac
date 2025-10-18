# HEAC CMS - Admin User Guide

This guide provides comprehensive instructions for content managers and administrators using the HEAC CMS admin panel.

## Table of Contents

1. [Getting Started](#getting-started)
2. [Dashboard Overview](#dashboard-overview)
3. [Managing Pages](#managing-pages)
4. [Managing Research](#managing-research)
5. [Media Library](#media-library)
6. [Contact Inquiries](#contact-inquiries)
7. [User Management](#user-management)
8. [Analytics & Reports](#analytics--reports)
9. [Best Practices](#best-practices)

---

## Getting Started

### Accessing the Admin Panel

1. Navigate to `https://yourdomain.com/admin`
2. Enter your email and password
3. Complete two-factor authentication if enabled
4. You'll be redirected to the dashboard

### First Login

After your first login:
1. Change your default password immediately
2. Set up two-factor authentication (recommended)
3. Update your profile information
4. Familiarize yourself with the dashboard

### User Roles

The system has four user roles with different permissions:

- **Super Admin**: Full system access, can manage everything
- **Admin**: Can manage content, research, and media
- **Editor**: Can create and edit content but not publish
- **Viewer**: Read-only access to the admin panel

---

## Dashboard Overview

The dashboard provides a quick overview of your website's status and activity.

### Dashboard Widgets

**Stats Overview Widget**
- Total pages, research papers, and media files
- Recent activity counts
- Trend indicators showing growth or decline

**Popular Content Widget**
- Most viewed pages
- Most downloaded research papers
- Date range filter for custom periods

**Recent Activity Widget**
- Latest page updates
- New research submissions
- Recent contact inquiries

### Navigation Menu

The left sidebar contains:
- **Dashboard**: Main overview page
- **Pages**: Content management
- **Research**: Research paper management
- **Media**: Media library
- **Categories**: Research categories
- **Tags**: Research tags
- **Contact Inquiries**: Form submissions
- **Users**: User management (admin only)
- **Settings**: System settings (admin only)

---

## Managing Pages

Pages are the main content sections of your website (About, Services, etc.).

### Creating a New Page

1. Click **Pages** in the sidebar
2. Click **New Page** button
3. Fill in the page details:
   - **Title**: Page title (e.g., "About HEAC")
   - **Slug**: URL-friendly version (auto-generated from title)
   - **Content**: Rich text content using the editor
   - **Excerpt**: Short summary for listings
   - **Status**: Draft, Published, or Archived
   - **Published At**: Schedule publication date/time
   - **Parent Page**: Create hierarchical pages (optional)

4. Configure SEO settings:
   - **Meta Title**: Custom title for search engines
   - **Meta Description**: Description for search results
   - **OG Image**: Social media sharing image

5. Click **Create** to save

### Editing Pages

1. Go to **Pages** in the sidebar
2. Click on the page you want to edit
3. Make your changes
4. Click **Save** to update

### Using the Content Editor

The TipTap editor provides rich formatting options:

**Text Formatting**
- Bold, italic, underline, strikethrough
- Headings (H1-H6)
- Bullet and numbered lists
- Blockquotes
- Code blocks

**Media**
- Insert images from media library
- Add videos (embed codes)
- Insert links

**Advanced Features**
- Tables
- Horizontal rules
- Text alignment
- Text colors and highlights

**Tips**
- Use headings to structure your content
- Keep paragraphs short for readability
- Add alt text to all images for accessibility
- Preview your page before publishing

### Page Status

- **Draft**: Not visible to public, work in progress
- **Published**: Live on the website
- **Archived**: Hidden but preserved for reference

### Scheduling Pages

To schedule a page for future publication:
1. Set **Status** to "Published"
2. Set **Published At** to a future date/time
3. Save the page
4. The page will automatically go live at the scheduled time

### Organizing Pages

**Hierarchical Structure**
- Create parent-child relationships
- Use **Parent Page** field when creating/editing
- Example: Services > Accreditation Services > Initial Accreditation

**Ordering**
- Drag and drop pages in the list to reorder
- Order affects navigation menu display

### Deleting Pages

1. Go to the page you want to delete
2. Click the **Delete** button
3. Confirm the deletion
4. Note: Deleted pages can be restored from trash (if soft deletes enabled)

---

## Managing Research

Research papers are academic publications, reports, and documents.

### Adding Research Papers

1. Click **Research** in the sidebar
2. Click **New Research** button
3. Fill in the research details:
   - **Title**: Research paper title
   - **Slug**: URL-friendly identifier
   - **Abstract**: Summary of the research
   - **Authors**: List of authors (one per line or comma-separated)
   - **Publication Date**: When the research was published
   - **File**: Upload PDF or document file
   - **Thumbnail**: Cover image (optional)
   - **Status**: Draft or Published
   - **Featured**: Mark as featured for homepage display

4. Assign categories and tags:
   - **Categories**: Primary classification (e.g., "Quality Assurance")
   - **Tags**: Additional keywords (e.g., "higher education", "standards")

5. Configure SEO settings
6. Click **Create** to save

### Research File Requirements

**Supported Formats**
- PDF (recommended)
- DOC/DOCX
- Maximum file size: 50MB (configurable)

**Best Practices**
- Use descriptive filenames
- Ensure PDFs are searchable (not scanned images)
- Include bookmarks in PDFs for navigation
- Optimize file size before uploading

### Editing Research

1. Go to **Research** in the sidebar
2. Click on the research paper to edit
3. Make your changes
4. Click **Save**

### Managing Categories

Categories organize research into main topics.

**Creating Categories**
1. Go to **Categories**
2. Click **New Category**
3. Enter:
   - **Name**: Category name
   - **Slug**: URL identifier
   - **Description**: Brief description
   - **Parent Category**: For subcategories (optional)
4. Click **Create**

**Category Hierarchy**
- Create parent-child relationships
- Example: Research > Quality Assurance > Institutional Assessment

### Managing Tags

Tags provide flexible keyword classification.

**Creating Tags**
1. Go to **Tags**
2. Click **New Tag**
3. Enter name and slug
4. Click **Create**

**Tag Best Practices**
- Use consistent naming conventions
- Avoid duplicate tags with similar meanings
- Review and merge redundant tags periodically

### Featured Research

Mark important research as "Featured" to display on:
- Homepage
- Research landing page
- Special sections

To feature research:
1. Edit the research paper
2. Toggle **Featured** to ON
3. Save

### Research Analytics

Track research performance:
- **Views**: Number of times viewed
- **Downloads**: Number of downloads
- View statistics in the dashboard widgets

---

## Media Library

The media library is a centralized location for all images, documents, and files.

### Uploading Media

**Single Upload**
1. Go to **Media** in the sidebar
2. Click **New Media** or **Upload**
3. Select file from your computer
4. Fill in details:
   - **Name**: Descriptive name
   - **Alt Text**: For accessibility and SEO
   - **Title**: Display title
   - **Caption**: Optional description
   - **Folder**: Organize into folders
5. Click **Create**

**Bulk Upload**
1. Click **Bulk Upload** button
2. Select multiple files
3. Files are uploaded with default settings
4. Edit individual files afterward to add details

### Organizing Media

**Folders**
- Create folders to organize media
- Examples: "Homepage Images", "Research Covers", "Team Photos"
- Drag and drop files between folders

**Searching Media**
- Use the search bar to find files
- Filter by:
  - File type (images, documents, videos)
  - Upload date
  - Folder
  - Tags

### Using Media in Content

**In Page Editor**
1. Click the image icon in the editor toolbar
2. Select from media library
3. Choose the image
4. Adjust size and alignment
5. Add caption if needed

**For Research Thumbnails**
1. When editing research
2. Click **Thumbnail** field
3. Select from media library or upload new

### Image Optimization

The system automatically:
- Generates multiple sizes (thumbnail, medium, large)
- Converts to WebP format for better performance
- Compresses images to reduce file size
- Maintains original file as backup

### Media Best Practices

**File Naming**
- Use descriptive names: `heac-building-exterior.jpg`
- Avoid spaces: use hyphens or underscores
- Include relevant keywords

**Image Requirements**
- Minimum width: 800px for content images
- Recommended: 1920px for hero/banner images
- Format: JPG for photos, PNG for graphics with transparency
- File size: Keep under 2MB before upload

**Alt Text**
- Describe the image content
- Include relevant keywords naturally
- Keep under 125 characters
- Example: "HEAC headquarters building in downtown"

### Deleting Media

**Before Deleting**
- Check where the media is used
- System shows usage locations
- Deleting may break content that references it

**To Delete**
1. Select the media file
2. Click **Delete**
3. Confirm deletion
4. File is permanently removed

---

## Contact Inquiries

Manage form submissions from the website contact form.

### Viewing Inquiries

1. Go to **Contact Inquiries** in the sidebar
2. View list of all submissions
3. Filter by:
   - Status (New, In Progress, Resolved, Closed)
   - Date range
   - Email address

### Inquiry Details

Click on an inquiry to view:
- Sender name and contact information
- Subject and message
- Submission date and time
- IP address and user agent (for spam detection)
- Current status
- Response history

### Managing Inquiries

**Change Status**
1. Open the inquiry
2. Select new status from dropdown:
   - **New**: Just received, not yet reviewed
   - **In Progress**: Being handled
   - **Resolved**: Issue resolved
   - **Closed**: Completed and archived
3. Save changes

**Responding to Inquiries**
1. Open the inquiry
2. Click **Respond** button
3. Compose your response
4. Email is sent to the inquirer
5. Status automatically updates to "In Progress"

### Spam Protection

The system includes:
- Honeypot fields (invisible to humans)
- Rate limiting (prevents multiple submissions)
- IP tracking for blocking repeat offenders

**Handling Spam**
1. Mark inquiry as spam
2. System learns patterns
3. Future similar submissions may be auto-blocked

### Exporting Inquiries

Export inquiries for reporting:
1. Apply filters if needed
2. Click **Export** button
3. Choose format (CSV, Excel)
4. Download file

---

## User Management

Manage admin panel users and their permissions (Super Admin and Admin only).

### Creating Users

1. Go to **Users** in the sidebar
2. Click **New User**
3. Fill in details:
   - **Name**: Full name
   - **Email**: Login email
   - **Password**: Temporary password
   - **Role**: Select appropriate role
   - **Active**: Enable/disable account
4. Click **Create**
5. User receives email with login instructions

### Editing Users

1. Go to **Users**
2. Click on user to edit
3. Update information
4. Change role if needed
5. Save changes

### User Roles and Permissions

**Super Admin**
- Full system access
- Can manage all users
- Can change system settings
- Can delete any content

**Admin**
- Manage pages, research, and media
- Manage contact inquiries
- View analytics
- Cannot manage users or system settings

**Editor**
- Create and edit pages and research
- Upload media
- Cannot publish content (requires approval)
- Cannot delete content

**Viewer**
- Read-only access
- Can view all content
- Cannot make changes
- Useful for stakeholders and reviewers

### Deactivating Users

Instead of deleting users:
1. Edit the user
2. Toggle **Active** to OFF
3. Save
4. User cannot log in but history is preserved

### Password Management

**Resetting User Passwords**
1. Edit the user
2. Click **Reset Password**
3. New temporary password is generated
4. User receives email with new password
5. User must change password on next login

**Password Requirements**
- Minimum 8 characters
- Must include uppercase and lowercase
- Must include numbers
- Must include special characters

### Two-Factor Authentication

**Enabling 2FA**
1. Go to your profile
2. Click **Enable Two-Factor Authentication**
3. Scan QR code with authenticator app
4. Enter verification code
5. Save recovery codes securely

**Enforcing 2FA**
Super Admins can require 2FA for all users:
1. Go to Settings
2. Enable **Require 2FA**
3. Users must set up 2FA on next login

---

## Analytics & Reports

Track website performance and user engagement.

### Dashboard Analytics

**Page Views**
- Total page views over time
- Most popular pages
- Traffic sources
- Bounce rate

**Research Analytics**
- Total downloads
- Most downloaded papers
- Views per research paper
- Category performance

**User Engagement**
- Average session duration
- Pages per session
- Return visitor rate

### Viewing Reports

**Date Range Selection**
- Today
- Last 7 days
- Last 30 days
- Last 90 days
- Custom date range

**Exporting Reports**
1. Select date range
2. Choose metrics
3. Click **Export**
4. Download as PDF or Excel

### Google Analytics Integration

If Google Analytics is configured:
- View detailed traffic reports
- Track conversion goals
- Analyze user behavior
- Monitor real-time visitors

Access Google Analytics:
1. Click **Analytics** in sidebar
2. View embedded GA4 dashboard
3. Or click link to full Google Analytics

---

## Best Practices

### Content Management

**Writing for the Web**
- Use clear, concise language
- Break content into short paragraphs
- Use headings to structure content
- Include relevant keywords naturally
- Add internal links to related pages

**SEO Optimization**
- Write unique meta descriptions for each page
- Use descriptive page titles (50-60 characters)
- Include target keywords in headings
- Add alt text to all images
- Create descriptive URLs (slugs)

**Accessibility**
- Use proper heading hierarchy (H1 → H2 → H3)
- Provide alt text for images
- Ensure sufficient color contrast
- Use descriptive link text (avoid "click here")
- Test with screen readers

### Research Management

**Organizing Research**
- Use consistent category structure
- Apply relevant tags to all papers
- Write clear, informative abstracts
- Include all author names
- Set accurate publication dates

**File Management**
- Use high-quality PDF files
- Ensure PDFs are searchable
- Include bookmarks for long documents
- Optimize file size (compress if needed)
- Use descriptive filenames

### Media Management

**Image Guidelines**
- Use high-resolution images
- Optimize before uploading
- Add descriptive alt text
- Organize into folders
- Delete unused media regularly

**File Organization**
- Create logical folder structure
- Use consistent naming conventions
- Tag media for easy searching
- Review and clean up quarterly

### Security

**Account Security**
- Use strong, unique passwords
- Enable two-factor authentication
- Log out when finished
- Don't share login credentials
- Report suspicious activity

**Content Security**
- Review content before publishing
- Don't include sensitive information
- Verify external links
- Check file uploads for malware
- Follow data privacy guidelines

### Regular Maintenance

**Weekly Tasks**
- Review new contact inquiries
- Check for broken links
- Monitor analytics
- Respond to pending items

**Monthly Tasks**
- Review and update outdated content
- Clean up unused media
- Check user accounts
- Review security logs
- Update research publications

**Quarterly Tasks**
- Content audit
- SEO review
- Performance optimization
- User training refresher
- Backup verification

---

## Getting Help

### Support Resources

- **Documentation**: Check other guide files in the project
- **Video Tutorials**: Available in the admin panel help section
- **FAQ**: Common questions and answers
- **Technical Support**: Contact your system administrator

### Reporting Issues

If you encounter problems:
1. Note what you were doing when the issue occurred
2. Take a screenshot if possible
3. Check if the issue persists after refreshing
4. Contact your system administrator with details

### Feature Requests

To suggest new features:
1. Document your use case
2. Explain the benefit
3. Submit through the feedback form
4. Discuss with your administrator

---

## Appendix

### Keyboard Shortcuts

**Content Editor**
- `Ctrl/Cmd + B`: Bold
- `Ctrl/Cmd + I`: Italic
- `Ctrl/Cmd + K`: Insert link
- `Ctrl/Cmd + Z`: Undo
- `Ctrl/Cmd + Y`: Redo
- `Ctrl/Cmd + S`: Save (if enabled)

**Navigation**
- `Ctrl/Cmd + /`: Open search
- `Esc`: Close modals

### Glossary

- **Slug**: URL-friendly version of a title (e.g., "about-heac")
- **Meta Description**: Summary shown in search results
- **Alt Text**: Description of an image for accessibility
- **Featured**: Highlighted content shown prominently
- **Draft**: Unpublished content
- **Taxonomy**: System of categories and tags
- **Widget**: Dashboard component showing information
- **2FA**: Two-Factor Authentication for extra security

---

**Last Updated**: October 2025  
**Version**: 1.0
