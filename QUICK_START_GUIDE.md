# ⚡ Quick Start Guide - LokalKu 1.0

Get up and running with LokalKu in just 5 minutes!

## 🚀 5-Minute Setup

### Step 1: Database Configuration (2 minutes)

Connect to your MySQL database and run the following SQL commands:

```sql
-- Add new columns to users table
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user';
ALTER TABLE users ADD COLUMN is_banned BOOLEAN DEFAULT FALSE;

-- Create your first admin user (replace 'your_username' with your actual username)
UPDATE users SET role = 'admin' WHERE username = 'your_username';
```

### Step 2: Browser Refresh (30 seconds)

Refresh your browser to load the updated system:

```
1. Clear browser cache (Ctrl+Shift+Delete on Windows/Linux or Cmd+Shift+Delete on Mac)
2. Log out from the application
3. Log back in with your account
```

### Step 3: Feature Testing (2 minutes)

Verify all features are working correctly:

```
1. Globe icon in navbar → Switch language (EN/ID)
2. Look for "Admin Panel" in navbar → Click to open admin dashboard
3. View dashboard → Confirm modern card design is displaying
```

---

## 🎯 Core Features Overview

### 🌍 Multi-Language System
- **Access**: Click the globe icon in the navbar
- **Options**: English (EN) or Indonesian (ID)
- **Storage**: Preference automatically saved in session
- **Feature**: Works across all pages

### 👨‍💼 Admin Panel
The admin panel provides three main sections:

```
1. Dashboard Tab
   - Total users count
   - Total memes count
   - Total votes count
   - Recent meme activity

2. Users Management Tab
   - Ban/unban users
   - Promote users to admin
   - Remove admin privileges
   - View user details

3. Memes Management Tab
   - View all memes
   - Delete inappropriate content
   - Monitor vote counts
```

### 🎨 Modern Interface
- **Navbar**: Gradient background with smooth transitions
- **Meme Cards**: Clean, minimal design without descriptions
- **Leaderboard**: Badges for top 3 contributors (#1, #2, #3)
- **Forms**: Modern, responsive input styles
- **Mobile**: Fully responsive on all devices

---

## 📂 File Structure Reference

```
LokalKu/
├── admin.php                      ← Admin dashboard page
├── dashboard.php                  ← Main meme feed (modern cards)
├── login.php, register.php        ← Modern authentication forms
├── leaderboard.php                ← User rankings with badges
├── upload.php                     ← Drag-and-drop meme upload
├── user.php                       ← User profile page
├── settings.php                   ← User settings page
│
├── includes/
│   ├── db.php                    ← Database connection
│   ├── auth.php                  ← Authentication functions
│   └── i18n.php                  ← Multi-language system
│
├── lang/
│   ├── en.php                    ← English translations
│   └── id.php                    ← Indonesian translations
│
├── templates/
│   ├── header.php                ← Navigation and header
│   └── footer.php                ← Footer section
│
└── Documentation/
    ├── CHANGELOG.md              ← Version history
    ├── FEATURES.md               ← Feature documentation
    ├── IMPLEMENTATION_SUMMARY.md  ← Technical details
    ├── TESTING_GUIDE.md          ← Testing procedures
    ├── VISUAL_SUMMARY.md         ← UI/UX changes
    └── README.md                 ← Main documentation
```

---

## 🔧 Common Administrative Tasks

### Adding a New Language

1. Create a new language file:
   ```bash
   cp lang/en.php lang/es.php
   ```

2. Edit the new file and translate all strings

3. Register the language in `includes/i18n.php`:
   ```php
   $supportedLanguages = ['en', 'id', 'es'];
   ```

### Making a User an Admin

```sql
-- Via database
UPDATE users SET role = 'admin' WHERE username = 'desired_username';

-- Or via admin panel
1. Log in as current admin
2. Go to Admin Panel → Users
3. Click "Make Admin" next to the user
```

### Banning a User

**Method 1: Admin Panel**
```
1. Go to Admin Panel → Users
2. Find the user
3. Click "Ban User"
```

**Method 2: Database**
```sql
UPDATE users SET is_banned = TRUE WHERE username = 'username_to_ban';
```

### Unbanning a User

**Method 1: Admin Panel**
```
1. Go to Admin Panel → Users
2. Find the banned user
3. Click "Unban User"
```

**Method 2: Database**
```sql
UPDATE users SET is_banned = FALSE WHERE username = 'username_to_unban';
```

### Deleting Inappropriate Memes

```
1. Log in as admin
2. Go to Admin Panel → Memes
3. Find the meme to delete
4. Click the trash icon or "Delete" button
```

---

## 📋 Verification Checklist

Before considering the setup complete, verify:

- [ ] Database columns added (`role`, `is_banned`)
- [ ] i18n system working (language switcher responds)
- [ ] Admin panel accessible and visible in navbar
- [ ] Modern UI displaying correctly
- [ ] Meme cards showing without descriptions
- [ ] Language switcher visible and functional
- [ ] Mobile responsiveness working
- [ ] User can upload memes
- [ ] Leaderboard displaying rankings with badges
- [ ] Admin can manage users and memes

---

## 🎨 Common Translation Keys

When working with the i18n system, these are the most frequently used keys:

```php
// Page titles
t('dashboard')              // Dashboard title
t('admin_title')           // Admin panel title
t('leaderboard_title')     // Leaderboard title

// Navigation
t('nav_home')              // Home link
t('nav_upload')            // Upload link
t('nav_admin')             // Admin link

// Buttons
t('login_submit')          // Login button
t('register_submit')       // Register button
t('upload_submit')         // Upload button

// Actions
t('upvote')                // Upvote action
t('downvote')              // Downvote action
t('comment')               // Comment action

// Table headers
t('leaderboard_rank')      // Rank column
t('leaderboard_votes')     // Votes column
```

For a complete list of all available keys, refer to:
- `lang/en.php` for English translations
- `lang/id.php` for Indonesian translations

---

## 🚨 Troubleshooting Guide

### Problem: Admin Panel Not Showing

**Solution:**
- Check the database to ensure your user has `role = 'admin'`
- Log out completely and log back in
- Clear browser cache (Ctrl+Shift+Delete)
- Verify the `admin.php` file exists in the root directory

### Problem: Language Doesn't Switch

**Solution:**
- Clear all cookies for the site (Ctrl+Shift+Delete)
- Close the browser tab and open a new one
- Check that language files exist in the `lang/` folder
- Ensure JavaScript is enabled in the browser

### Problem: File Upload Not Working

**Solution:**
- Verify the `uploads/` and `uploads/avatars/` folders exist
- Check folder permissions: they should be writable (755)
- Ensure file size is under 5MB
- Only image formats are allowed (JPG, PNG, GIF, WebP)

### Problem: Styles/CSS Not Loading

**Solution:**
- Hard refresh (Ctrl+F5 on Windows or Cmd+Shift+R on Mac)
- Check internet connection (Tailwind CSS loads from CDN)
- Clear browser cache
- Check browser console for error messages

### Problem: Can't Create Database Connection

**Solution:**
- Verify MySQL is running on your server
- Check credentials in `includes/db.php`
- Ensure the database has been created
- Verify user has necessary permissions

---

## 📖 Next Steps

After completing the quick start setup:

1. **Explore Features**: Browse through all features to understand functionality
2. **Test All Pages**: Visit each page and test responsiveness
3. **Read Full Documentation**:
   - `README.md` - Complete project overview
   - `FEATURES.md` - Detailed feature documentation
   - `TESTING_GUIDE.md` - Comprehensive testing procedures
4. **Customize**: Modify colors, text, and settings to match your needs
5. **Deploy**: Move to production environment

---

## 📚 Additional Resources

| Document | Purpose |
|----------|---------|
| `README.md` | Complete project documentation |
| `FEATURES.md` | Detailed feature list and usage |
| `CHANGELOG.md` | Version history and updates |
| `TESTING_GUIDE.md` | Testing procedures and checklist |
| `IMPLEMENTATION_SUMMARY.md` | Technical implementation details |
| `VISUAL_SUMMARY.md` | UI/UX and design changes |

---

## 💡 Tips & Best Practices

1. **Regular Backups**: Back up your database regularly
2. **Monitor Admin Panel**: Regularly check for inappropriate content
3. **Update Users**: Keep user roles and permissions up to date
4. **Security**: Change default passwords and use strong credentials
5. **Performance**: Monitor upload folder size and clean old files

---

## 🎉 You're Ready!

LokalKu 1.0 is now ready to use with:

- ✅ Multi-language support (English, Indonesian)
- ✅ Fully functional admin panel
- ✅ Modern, responsive user interface
- ✅ Enhanced user experience
- ✅ Secure authentication system

**Enjoy your meme sharing platform!** 🚀

---

**Version**: 1.0.0  
**Last Updated**: January 2026  
**Status**: No Release
