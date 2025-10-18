# Logo Setup Guide

## Current Status
Placeholder SVG logos have been created in `public/images/`:
- `logo.svg` - Blue background with white text (for light backgrounds)
- `logo-white.svg` - White background with blue text (for dark backgrounds)
- `og-default.jpg` - Placeholder for social media sharing

## How to Add Your Custom Logos

### 1. Prepare Your Logo Files
You need two versions of your logo:
- **Standard Logo**: For use on light backgrounds (header, etc.)
- **White/Light Logo**: For use on dark backgrounds (footer, etc.)

### 2. Recommended Specifications
- **Format**: SVG (preferred) or PNG with transparency
- **Dimensions**: 
  - Height: 40-50px for standard display
  - Width: Proportional to your logo design
- **File Size**: Keep under 50KB for optimal performance

### 3. Replace the Placeholder Files

#### Option A: Using SVG (Recommended)
1. Export your logo as SVG from your design software
2. Replace these files:
   - `public/images/logo.svg` - Your standard logo
   - `public/images/logo-white.svg` - Your white/light version

#### Option B: Using PNG
1. Export your logo as PNG with transparency
2. Replace the files with PNG versions:
   - `public/images/logo.png`
   - `public/images/logo-white.png`
3. Update the layout file (`resources/views/layouts/app.blade.php`):
   - Change `logo.svg` to `logo.png`
   - Change `logo-white.svg` to `logo-white.png`

### 4. Add Social Media Image
Replace `public/images/og-default.jpg` with your social media sharing image:
- **Dimensions**: 1200x630px (Facebook/LinkedIn standard)
- **Format**: JPG or PNG
- **Content**: Should include your logo and a tagline

### 5. Clear Cache
After replacing the files, run:
```bash
php artisan optimize:clear
```

## Logo Usage in Templates

The logos are used in:
- **Header**: `resources/views/layouts/app.blade.php` (line ~52)
- **Footer**: `resources/views/layouts/app.blade.php` (line ~180)

## Tips for Best Results
1. Use SVG format for crisp display on all screen sizes
2. Ensure your logo has proper padding/margins in the design
3. Test on both light and dark backgrounds
4. Optimize file size using tools like SVGOMG or TinyPNG
5. Keep a backup of your original logo files

## Need Help?
If you need assistance with logo design or optimization, consider:
- Using online tools like Canva or Figma
- Hiring a designer on Fiverr or Upwork
- Using AI tools like DALL-E or Midjourney for initial concepts
