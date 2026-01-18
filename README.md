# Pixco - Local Meme Sharing Platform

**Pixco** is a modern web application designed for sharing local memes with an interactive community. Built with a focus on user experience, the platform features multi-language support, an admin panel, and a modern, responsive interface.

**Project Created**: December 27, 2024 (LokalKu)

## 🌟 Key Features

### 🌍 Multi-Language Support (i18n)
- **English (EN)** and **Indonesian (ID)** support
- Language switcher available in the navbar
- User preferences stored in session and browser cookies
- Easy to add more languages without code modifications

### 👨‍💼 Comprehensive Admin Panel
- **Dashboard**: Real-time statistics (total users, memes, votes)
- **User Management**: Ban/unban users, promote to admin status
- **Content Moderation**: View and delete inappropriate memes
- **Role-Based Access Control**: Only admins can access the admin panel

### 🎨 Modern, Responsive UI
- Built with **Tailwind CSS** for beautiful, responsive design
- Smooth transitions and hover effects
- Works perfectly on mobile, tablet, and desktop devices
- Modern card-based layouts
- Gradient backgrounds and contemporary color schemes

### 📱 Core Functionality
- **User Authentication**: Secure login and registration system
- **Dashboard**: Browse and interact with memes (upvote/downvote/comment)
- **Meme Upload**: Drag-and-drop interface with file validation
- **Leaderboard**: Ranking system showing top contributors with badges
- **User Profiles**: View user information and their meme contributions
- **Settings**: Change email, password, and profile avatar

### 🚀 Advanced Features
- JWT-style authentication with session management
- RESTful API for comments and votes
- Real-time leaderboard rankings
- Avatar upload and image processing
- Responsive comment section with modal interface

## 🛠️ Technology Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL |
| **Frontend** | HTML5, CSS3, JavaScript |
| **Framework/Library** | Tailwind CSS, FontAwesome 6 |
| **Authentication** | PHP Sessions |
| **Internationalization** | Custom i18n System |

## 📋 Database Schema

### Users Table
```sql
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user';
ALTER TABLE users ADD COLUMN is_banned BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL;
```

### Core Tables
- **users**: User accounts and authentication
- **memes**: Meme posts with metadata
- **votes**: User votes (upvote/downvote)
- **comments**: User comments on memes

## 📁 Project Structure

```
Pixco/
├── index.php                    # Home page / Dashboard
├── admin.php                    # Admin dashboard
├── dashboard.php                # Modern meme feed
├── login.php                    # Login page
├── register.php                 # Registration page
├── leaderboard.php              # User rankings
├── upload.php                   # Meme upload interface
├── settings.php                 # User settings
├── user.php                     # User profile page
├── comment.php                  # API for comments
├── logout.php                   # Logout action
│
├── includes/                    # Backend logic
│   ├── db.php                  # Database connection
│   ├── auth.php                # Authentication functions
│   ├── i18n.php                # Internationalization system
│   └── google_auth.php         # Google OAuth (optional)
│
├── lang/                        # Language files
│   ├── en.php                  # English translations
│   └── id.php                  # Indonesian translations
│
├── templates/                   # Reusable templates
│   ├── header.php              # Navigation and head
│   └── footer.php              # Footer section
│
├── assets/
│   ├── css/
│   │   └── style.css           # Custom styles
│   ├── js/                     # JavaScript files
│   └── img/                    # Images and assets
│
├── uploads/                     # User-generated content
│   └── avatars/                # User profile pictures
│
└── Documentation
    ├── README.md               # This file
    ├── CHANGELOG.md            # Version history
    ├── FEATURES.md             # Feature documentation
    ├── QUICK_START_GUIDE.md    # Setup instructions
    ├── TESTING_GUIDE.md        # Testing procedures
    ├── IMPLEMENTATION_SUMMARY.md # Technical details
    └── PROJECT_COMPLETION_REPORT.md # Project report
```

## 🚀 Quick Start Guide

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)
- Composer (for dependency management)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Pixco
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database setup**
   ```sql
   -- Create database
   CREATE DATABASE Pixco;
   USE Pixco;
   
   -- Run schema from templates/mysql.sql
   SOURCE templates/mysql.sql;
   
   -- Add new columns
   ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user';
   ALTER TABLE users ADD COLUMN is_banned BOOLEAN DEFAULT FALSE;
   ```

4. **Configure database connection**
   - Edit `includes/db.php`
   - Set your database credentials

5. **Create admin user**
   ```sql
   UPDATE users SET role = 'admin' WHERE username = 'your_username';
   ```

6. **Set file permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/avatars/
   ```

## 📖 Usage Guide

### For Users
1. **Register**: Create a new account with email and password
2. **Login**: Access your account
3. **Browse**: View memes in the dashboard
4. **Interact**: Upvote, downvote, and comment on memes
5. **Upload**: Share your own memes using the upload page
6. **Leaderboard**: Check rankings and see top contributors
7. **Settings**: Update profile information and preferences
8. **Language**: Switch between English and Indonesian

### For Administrators
1. **Access Admin Panel**: Click "Admin Panel" in navbar (admin-only)
2. **Dashboard**: View platform statistics
3. **Manage Users**: 
   - Ban/unban users
   - Promote users to admin status
   - Remove admin privileges
4. **Manage Content**: Delete inappropriate memes
5. **Monitor**: Track user activity and platform health

## 🌐 Internationalization (i18n)

### Using Translations in Code
```php
// In PHP files
<?= t('dashboard') ?>           // Display translated string
<?= getLang() ?>                 // Get current language code

// Language switcher (in navbar)
<a href="?lang=en">English</a>
<a href="?lang=id">Indonesian</a>
```

### Adding New Language
1. Create `lang/new_lang.php`
2. Copy structure from `lang/en.php`
3. Translate all strings
4. Update supported languages in `includes/i18n.php`:
   ```php
   $supportedLanguages = ['en', 'id', 'new_lang'];
   ```

## 🔑 Key API Endpoints

- `GET /comment.php?meme_id=ID` - Fetch comments for a meme
- `POST /dashboard.php?id=ID&type=upvote` - Vote on a meme
- `POST /upload.php` - Submit new meme
- `POST /settings.php` - Update user settings

## 🔒 Security Features

- **Password Hashing**: bcrypt algorithm for secure password storage
- **Session Management**: Secure PHP sessions
- **Input Validation**: Sanitize user inputs
- **XSS Protection**: HTML entity encoding
- **CSRF Protection**: Form validation
- **File Upload Validation**: Check file types and sizes

## 📦 Dependencies

```json
{
  "require": {
    "firebase/php-jwt": "^5.3",
    "google/apiclient": "^2.0",
    "monolog/monolog": "^2.0",
    "guzzlehttp/guzzle": "^7.0"
  }
}
```

## 🐛 Troubleshooting

### Common Issues

**1. Database Connection Error**
- Check database credentials in `includes/db.php`
- Ensure MySQL server is running
- Verify database exists

**2. Language Not Switching**
- Clear browser cache (Ctrl+Shift+Delete)
- Check cookies are enabled
- Verify language files exist in `lang/` folder

**3. Admin Panel Not Showing**
- Verify user has admin role in database
- Re-login after role change
- Check admin.php exists and is readable

**4. File Upload Issues**
- Check `uploads/` folder permissions
- Verify file size is under 5MB
- Ensure only image formats are uploaded

## 📞 Support & Contribution

For issues, suggestions, or contributions:
1. Create an issue in the repository
2. Submit a pull request with improvements
3. Contact the development team

## 📄 License

This project is licensed under the MIT License. See LICENSE file for details.

## 🙏 Acknowledgments

- Built with [Tailwind CSS](https://tailwindcss.com)
- Icons by [FontAwesome](https://fontawesome.com)
- Database management with [PHP PDO](https://www.php.net/manual/en/class.pdo.php)

---

**Version**: 1.0
**Status**: Active Development
