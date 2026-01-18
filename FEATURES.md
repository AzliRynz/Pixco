# 🎉 Pixco - Feature Documentation

A modern platform for sharing local memes with a vibrant community. This document provides comprehensive information about all features available in Pixco.

## ✨ Main Features

### 🌍 Multi-Language Support (Internationalization - i18n)
**Description**: The entire platform interface is available in multiple languages.

**Supported Languages**:
- **English (EN)** - Complete English translations
- **Indonesian (ID)** - Complete Indonesian translations
- Easy to add more languages without modifying code

**How It Works**:
- Language preference is stored in session and browser cookies
- Users can switch languages anytime using the globe icon in the navbar
- All UI strings are managed through language files in `lang/` directory
- Preference persists across sessions

**Usage in Code**:
```php
<?= t('key_name') ?>           // Display translated string
<?= getLang() ?>               // Get current language code
```

---

### 👨‍💼 Admin Panel
**Description**: Comprehensive administration interface for managing users, content, and platform statistics.

**Access Requirements**: User must have `admin` role

**Dashboard Tab - Statistics**:
- Total number of registered users
- Total memes uploaded to platform
- Total votes cast by all users
- Recent meme activity feed
- Quick statistics overview

**Users Management Tab - User Control**:
- View all registered users in a table
- Ban users (prevent them from accessing the platform)
- Unban users (restore their access)
- Promote regular users to admin status
- Remove admin privileges from users
- View user details (ID, username, email, join date)

**Memes Management Tab - Content Moderation**:
- View all memes with creator information
- Delete inappropriate or policy-violating memes
- Monitor vote counts for each meme
- Quick delete functionality with confirmation

**Navigation**:
- Accessible via "Admin Panel" link in navbar (admin users only)
- Three tabs for different management areas
- Role-based access control

---

### 🎨 Modern User Interface
**Description**: Contemporary, responsive design with smooth interactions and professional aesthetics.

**Design Elements**:
- **Navbar**: Gradient background with smooth transitions and hover effects
- **Cards**: Clean, minimal card-based layouts for content
- **Forms**: Modern form inputs with focus states and validation feedback
- **Buttons**: Gradient buttons with transform effects on hover
- **Colors**: Professional color scheme with brand consistency
- **Icons**: FontAwesome 6 icons for visual enhancement
- **Animations**: Smooth transitions and hover effects throughout

**Responsive Design**:
- Mobile-first approach
- Fully responsive on all screen sizes
- Tablet-optimized layouts
- Desktop enhancements

---

### 📱 Dashboard & Meme Feed
**Description**: Central hub for viewing and interacting with memes shared by the community.

**Features**:
- **Grid Display**: Memes displayed in a responsive grid layout
- **Meme Cards**: Clean cards showing:
  - Meme image
  - Creator username with avatar
  - Vote count badge
  - Vote buttons (upvote/downvote)
  - Comment count
  - Share/interact options
- **Sorting**: Organize memes by latest, trending, or most voted
- **Filtering**: Filter by category, creator, or date range (optional)
- **Interactions**:
  - Upvote/downvote memes
  - Comment on memes
  - View creator profile
  - Report inappropriate content

**User Feedback**:
- Real-time vote count updates
- Animation feedback on interactions
- Notification of successful actions

---

### 🚀 Meme Upload Interface
**Description**: User-friendly interface for uploading and sharing new memes.

**Upload Features**:
- **Drag & Drop**: Intuitive drag-and-drop interface
- **File Selection**: Traditional file browser option
- **Preview**: See selected file before upload
- **Validation**:
  - File type checking (JPG, PNG, GIF, WebP)
  - File size limit (max 5MB)
  - Dimension validation
  - Format verification

**Submission Process**:
1. Select or drag-drop image file
2. Enter catchy meme title (required)
3. Submit for upload
4. Optional: Add tags or category
5. View uploaded meme in dashboard

**Error Handling**:
- Clear error messages for invalid uploads
- Success confirmation with redirect
- File validation before processing

---

### 🏆 Leaderboard & Rankings
**Description**: Competitive ranking system showing top meme creators and contributors.

**Display Options**:
- **Desktop View**: Professional table format
- **Mobile View**: Card-based responsive layout
- **Automatic Switching**: Responsive design adapts to screen size

**Ranking Criteria**:
- **Primary**: Total votes received (descending)
- **Secondary**: Number of memes created (descending)
- **Excluded**: Banned users are not displayed

**Featured Badges**:
- **#1 Position**: Gold badge
- **#2 Position**: Silver badge
- **#3 Position**: Bronze badge
- **Other Rankings**: Standard blue badge

**Information Displayed**:
- Rank position
- User avatar/profile picture
- Username (clickable link to profile)
- Total memes created
- Total votes received
- Join date

**Interactions**:
- Click username to view user profile
- View user's meme collection
- See detailed user statistics

---

### 👤 User Profile Pages
**Description**: Dedicated profile pages for each user showing their contributions and statistics.

**Profile Information**:
- User avatar (profile picture)
- Username
- Email address
- Join date
- Account status

**User Statistics**:
- Total memes created
- Total votes received
- Most popular meme
- Average votes per meme

**User's Memes Display**:
- Grid of all memes uploaded by user
- Vote counts for each meme
- Creation date
- Interactive elements (vote, comment)

**Profile Actions**:
- Follow user (optional feature)
- Contact user (optional)
- View user's profile
- See user statistics

---

### ⚙️ User Settings
**Description**: Personalization and account management interface.

**Profile Settings**:
- **Avatar Upload**: Upload and update profile picture
  - Supported formats: JPG, PNG, GIF, WebP
  - Preview before saving
  - Automatic cropping (optional)

**Account Settings**:
- **Email Management**: Update email address
- **Password Change**: Change account password securely
  - Current password verification
  - New password requirements
  - Password confirmation

**Preferences**:
- **Language Preference**: Set default language
- **Notification Settings**: Control notifications (if applicable)
- **Privacy Settings**: Manage profile visibility
- **Theme Selection**: Light/dark mode (if applicable)

**Data Management**:
- Download personal data (GDPR compliance)
- Delete account option
- Export memes

---

### 💬 Comment System
**Description**: Interactive comment functionality for community discussion on memes.

**Features**:
- Add comments to memes
- View all comments on a meme
- Display commenter username and avatar
- Comment creation timestamp
- Real-time comment loading
- Comment count badge on memes

**Comment Moderation**:
- Delete own comments
- Admin can delete any comment
- Report inappropriate comments (optional)

**Comment Interactions**:
- Modal/popup for writing comments
- Comment thread display
- Nested replies (optional)
- Like/reaction on comments (optional)

---

### 🔐 Authentication System
**Description**: Secure user authentication and session management.

**Registration**:
- Email verification
- Username validation (unique, min 3 characters)
- Strong password requirements (min 6 characters)
- Email confirmation (optional)
- Duplicate account prevention

**Login**:
- Username/email authentication
- Secure password verification
- Session management
- Remember me option (optional)
- Account lockout after failed attempts (optional)

**Session Management**:
- Secure PHP sessions
- Session timeout (30 minutes default)
- CSRF protection
- XSS prevention

**Security Features**:
- Password hashing (bcrypt)
- SQL injection prevention
- Input validation and sanitization
- Secure cookie handling

---

### 🌐 Voting System
**Description**: Interactive voting mechanism for rating memes.

**Vote Types**:
- **Upvote**: Indicate you like a meme
- **Downvote**: Indicate you dislike a meme

**Vote Tracking**:
- One vote per user per meme
- Vote changes are tracked
- Vote counts update in real-time
- Total vote display on meme cards

**Vote Display**:
- Vote count badge showing net votes
- Color-coded vote indicators
- Vote history (admin only)

---

### 📊 Analytics & Monitoring
**Description**: Data collection and platform monitoring (admin only).

**Metrics Tracked**:
- User registration trends
- Meme upload frequency
- Engagement metrics
- Vote patterns
- Comment activity
- Platform health indicators

**Admin Reports**:
- User growth statistics
- Content moderation metrics
- Most popular memes
- Active users list
- Platform performance data

---

## 🛠️ Technology Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| **Backend Framework** | PHP | 7.4+ |
| **Database** | MySQL | 5.7+ |
| **Frontend Framework** | Tailwind CSS | 3.0+ |
| **Icons** | FontAwesome | 6.0 |
| **JavaScript** | Vanilla JS | ES6+ |
| **Authentication** | PHP Sessions | Built-in |
| **Authentication (Optional)** | JWT | Firebase PHP-JWT |

---

## 📋 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    is_banned BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Memes Table
```sql
CREATE TABLE memes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    votes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Votes Table
```sql
CREATE TABLE votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    meme_id INT NOT NULL,
    vote_type ENUM('upvote', 'downvote') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (meme_id) REFERENCES memes(id)
);
```

### Comments Table
```sql
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    meme_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (meme_id) REFERENCES memes(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## 🔒 Security Features

- **Password Hashing**: bcrypt algorithm
- **Input Validation**: All user inputs validated
- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Protection**: HTML entity encoding
- **CSRF Protection**: Token validation on forms
- **Session Security**: Secure cookie handling
- **File Upload Security**: Type and size validation
- **Admin Authentication**: Role-based access control

---

## 🚀 Performance Optimizations

- **Lazy Loading**: Images load on demand
- **Database Indexing**: Optimized queries
- **Caching**: Browser and server-side caching
- **Compression**: CSS and JS minification
- **CDN**: External resources from CDN (Tailwind, FontAwesome)
- **Query Optimization**: Efficient database queries

---
