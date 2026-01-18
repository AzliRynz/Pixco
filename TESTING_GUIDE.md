# 🧪 TESTING GUIDE - Pixco Platform

## Pre-Testing Setup

### 1. Database Update
```sql
-- Run these queries first
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user';
ALTER TABLE users ADD COLUMN is_banned BOOLEAN DEFAULT FALSE;

-- Make a test admin
UPDATE users SET role = 'admin' WHERE id = 1;
```

### 2. Restart/Refresh
- Restart PHP server (if using local)
- Clear browser cache (Ctrl+Shift+Delete)
- Clear cookies for the domain
- Re-login to the application

---

## 📋 Test Checklist

### SECTION 1: Multi-Language (i18n) Testing ✅

#### 1.1 Language Switcher Visibility
- [ ] Login to dashboard
- [ ] Look for globe icon in navbar (desktop)
- [ ] Look for language button in mobile menu
- [ ] Hover shows dropdown with EN and ID options

#### 1.2 Language Switching (Desktop)
- [ ] Current language: Click on globe icon
- [ ] Select EN → Page refreshes
- [ ] All text changes to English
- [ ] Navigate to different pages → Text remains in EN
- [ ] Switch back to ID → All text in Indonesian

#### 1.3 Language Switching (Mobile)
- [ ] Resize browser to mobile size (< 768px)
- [ ] Open menu (hamburger icon)
- [ ] Find language buttons (EN, ID)
- [ ] Click EN → Page refreshes
- [ ] All text in English
- [ ] Repeat test for ID

#### 1.4 Language Persistence
- [ ] Set language to EN
- [ ] Close browser tab
- [ ] Open new tab and navigate to site
- [ ] **Expected**: Language should still be EN (from cookie/session)
- [ ] Test with ID as well

#### 1.5 Key UI Elements Translated
- [ ] Dashboard: "Dashboard" heading in correct language
- [ ] Login: Form labels translated
- [ ] Register: Form labels translated
- [ ] Upload: Button text translated
- [ ] Leaderboard: Column headers translated
- [ ] Admin Panel: Menu items translated

---

### SECTION 2: Admin Panel Testing ✅

#### 2.1 Admin Access Control
- [ ] Logout
- [ ] Try to access `/admin` directly
- [ ] **Expected**: Redirected to login or dashboard
- [ ] Login as regular user
- [ ] Try to access `/admin`
- [ ] **Expected**: Redirected to dashboard (not admin)

#### 2.2 Admin Login & Access
- [ ] Login with admin account (role = 'admin' in DB)
- [ ] Look for "Admin Panel" link in navbar
- [ ] **Expected**: Link should appear
- [ ] Click "Admin Panel"
- [ ] **Expected**: Redirected to /admin

#### 2.3 Admin Dashboard Tab
- [ ] Open admin panel
- [ ] Check "Dashboard" tab is selected
- [ ] **Expected** to see:
  - [ ] Total Users card with count
  - [ ] Total Memes card with count
  - [ ] Total Votes card with count
  - [ ] Recent Memes list (max 10)
  - [ ] Delete button for each recent meme

#### 2.4 Admin Users Tab
- [ ] Click "Manage Users" tab
- [ ] **Expected** to see:
  - [ ] Table with all users
  - [ ] Columns: ID, Username, Email, Role, Joined, Actions
  - [ ] User list populated

#### 2.5 Admin User Actions - Ban/Unban
- [ ] Find a test user in users list
- [ ] Click "Ban" button
- [ ] **Expected**: Page refreshes, user is_banned = TRUE
- [ ] Next to user, click "Unban"
- [ ] **Expected**: User is_banned = FALSE

#### 2.6 Admin User Actions - Promote to Admin
- [ ] Find regular user in list (role = 'user')
- [ ] Click "Make Admin" button
- [ ] **Expected**: Role changes to admin badge
- [ ] Refresh page
- [ ] **Expected**: User still has admin role
- [ ] Click "Remove Admin"
- [ ] **Expected**: Role changes back to user

#### 2.7 Admin User Actions - Self Protection
- [ ] Try to promote yourself to admin (if already admin)
- [ ] Try to ban yourself
- [ ] **Expected**: Actions should be disabled or ignored for your own account

#### 2.8 Admin Memes Tab
- [ ] Click "Manage Memes" tab
- [ ] **Expected** to see:
  - [ ] Table with all memes
  - [ ] Columns: ID, Title, Creator, Date, Votes, Actions
  - [ ] Meme list populated

#### 2.9 Admin Meme Delete
- [ ] Find a test meme
- [ ] Click delete button
- [ ] **Expected**: Confirmation prompt
- [ ] Confirm deletion
- [ ] **Expected**: Meme removed from table and database
- [ ] Verify in dashboard: total memes decreased

---

### SECTION 3: Modernized UI Testing ✅

#### 3.1 Navbar (Desktop)
- [ ] Navigate to any page
- [ ] Navbar shows:
  - [ ] Pixco logo with smile icon
  - [ ] Gradient blue background
  - [ ] Navigation links (Leaderboard, Upload, Profile, etc)
  - [ ] Language switcher (globe icon)
  - [ ] Hover effects on links (underline animation)

#### 3.2 Navbar (Mobile)
- [ ] Resize to mobile (< 768px)
- [ ] Navbar shows hamburger menu
- [ ] Click hamburger
- [ ] Menu slides down with all links
- [ ] Close menu (click again or click link)
- [ ] Language buttons in mobile menu

#### 3.3 Footer
- [ ] Scroll to bottom of any page
- [ ] Footer shows:
  - [ ] Gradient background
  - [ ] Column layout (3 columns on desktop, 1 on mobile)
  - [ ] Pixco branding
  - [ ] Navigation links
  - [ ] Social media icons
  - [ ] Copyright text

#### 3.4 Dashboard Cards
- [ ] Visit dashboard
- [ ] Each meme card shows:
  - [ ] Image on top (no description)
  - [ ] Title (bold)
  - [ ] Vote counter badge (top right)
  - [ ] Creator avatar and name
  - [ ] Action buttons: Upvote, Downvote, Comment
  - [ ] NO description text (this was removed!)

#### 3.5 Dashboard Cards - Mobile
- [ ] Resize to mobile
- [ ] Cards stack vertically (1 column)
- [ ] All elements still visible
- [ ] Buttons responsive and clickable

#### 3.6 Dashboard Cards - Hover Effects
- [ ] On desktop, hover over meme card
- [ ] **Expected**: Shadow increases, card scales slightly
- [ ] Smooth animation (not jarring)

#### 3.7 Login Form
- [ ] Navigate to /login
- [ ] Form shows:
  - [ ] Pixco logo centered
  - [ ] Gradient background
  - [ ] Modern input fields with icons
  - [ ] Gradient submit button
  - [ ] "Don't have account? Register" link

#### 3.8 Register Form
- [ ] Navigate to /register
- [ ] Form shows:
  - [ ] Multiple input fields with icons
  - [ ] Password and confirm password fields
  - [ ] Helper text ("Minimum 3 characters", etc)
  - [ ] Gradient submit button
  - [ ] Modern styling

#### 3.9 Upload Form
- [ ] Navigate to /upload
- [ ] Form shows:
  - [ ] Modern title with icon
  - [ ] Title input field
  - [ ] Drag-and-drop zone (with cloud icon)
  - [ ] Help text: "Click or drag image here"
  - [ ] File size and format info
  - [ ] 3 tip cards below form

#### 3.10 Upload Drag-Drop
- [ ] Drag image file to drop zone
- [ ] **Expected**: Zone highlights on drag
- [ ] Drop file
- [ ] **Expected**: File selected, filename shown

#### 3.11 Leaderboard Design
- [ ] Navigate to /leaderboard
- [ ] Desktop view shows:
  - [ ] Table with rank badges (#1, #2, #3 special colors)
  - [ ] User avatars
  - [ ] Total memes badge
  - [ ] Total votes badge
  - [ ] Hover effects on rows

#### 3.12 Leaderboard Mobile
- [ ] Resize to mobile
- [ ] View changes to card layout
- [ ] Each user in card with:
  - [ ] Rank badge
  - [ ] Avatar
  - [ ] Username
  - [ ] Meme count
  - [ ] Vote count

---

### SECTION 4: Remove Description Testing ✅

#### 4.1 Meme Cards No Description
- [ ] Go to dashboard
- [ ] View any meme card
- [ ] **Expected**: Card shows ONLY:
  - [ ] Image
  - [ ] Title
  - [ ] Vote count
  - [ ] Creator info
  - [ ] Action buttons
- [ ] **NOT visible**: Description/content text

#### 4.2 Cleaner UI
- [ ] Cards should look cleaner and more compact
- [ ] Focus is on image and title
- [ ] No scrolling needed for card content
- [ ] Better visual hierarchy

#### 4.3 Multiple Cards
- [ ] View dashboard with multiple memes
- [ ] All meme cards consistent (no description anywhere)
- [ ] Cards align nicely in grid
- [ ] No gaps or empty space from missing description

---

### SECTION 5: Functionality Testing ✅

#### 5.1 Language + Admin
- [ ] Set language to ID
- [ ] Login as admin
- [ ] Go to Admin Panel
- [ ] All admin text in Indonesian
- [ ] Switch to EN
- [ ] All admin text in English

#### 5.2 Language + Upload
- [ ] Set language to EN
- [ ] Try to upload without title
- [ ] **Expected**: Error message in English
- [ ] Set language to ID
- [ ] Refresh page
- [ ] Try to upload
- [ ] **Expected**: Error message in Indonesian

#### 5.3 Admin + Ban Feature
- [ ] Ban a user via admin panel
- [ ] Try to login with that user
- [ ] **Expected**: Login fails or redirects (if implemented)
- [ ] Unban user
- [ ] Can login again

#### 5.4 Vote System
- [ ] Upvote a meme
- [ ] Check vote count increases
- [ ] Check in admin dashboard
- [ ] Total votes increases
- [ ] Downvote
- [ ] Verify vote decreases

#### 5.5 Comment System
- [ ] Click comment button on meme
- [ ] Modal opens
- [ ] Add comment
- [ ] Submit
- [ ] Comment appears in list
- [ ] Modal closes

---

## 🎯 Performance Testing

#### 6.1 Page Load Time
- [ ] Dashboard: Should load quickly (< 2s)
- [ ] Admin panel: Should load quickly (< 2s)
- [ ] No console errors (F12 → Console)

#### 6.2 Mobile Responsiveness
- [ ] Chrome DevTools: Test at 320px, 768px, 1024px widths
- [ ] No horizontal scrolling
- [ ] All content readable
- [ ] Buttons clickable

#### 6.3 Browser Compatibility
- [ ] Chrome ✅
- [ ] Firefox ✅
- [ ] Safari (if available) ✅
- [ ] Mobile browsers ✅

---

## ⚠️ Common Issues & Solutions

### Issue: Language not switching
**Solution**: 
- Clear browser cookies
- Check lang files exist in /lang/ directory
- Check require statement in header.php

### Issue: Admin panel not visible
**Solution**:
- Verify role = 'admin' in users table
- Logout and login again
- Check session is set correctly

### Issue: Styles not loading
**Solution**:
- Hard refresh (Ctrl+F5)
- Check Tailwind CDN is accessible
- Check FontAwesome CDN is accessible

### Issue: Upload not working
**Solution**:
- Check /uploads folder exists and writable
- Check file size (max 5MB)
- Check file type (jpg, png, gif, webp only)

---

## ✅ Sign-Off Checklist

After completing all tests above, confirm:

- [ ] Multi-language works (EN & ID)
- [ ] Language switcher in navbar works
- [ ] Admin panel accessible
- [ ] Admin can manage users
- [ ] Admin can manage memes
- [ ] Modern UI looks professional
- [ ] No descriptions on meme cards
- [ ] Mobile responsive
- [ ] No console errors
- [ ] All features working

**Test Date**: _____________
**Tester Name**: _____________
**Status**: PASS ☑️ / FAIL ☐

---

## 📸 Screenshot Checklist

For documentation, capture:
- [ ] Dashboard with new cards
- [ ] Admin panel dashboard
- [ ] Admin users tab
- [ ] Admin memes tab
- [ ] Login form (modern)
- [ ] Register form (modern)
- [ ] Upload form with drag-drop
- [ ] Leaderboard with badges
- [ ] Mobile view of dashboard
- [ ] Mobile menu with language selector

---

**Happy Testing! 🚀**
