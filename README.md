# E-Khairat - Web-based platform to manage funeral welfare for Masjid Taman Sutera, Kajang

<div align="center">
  <img src="public/images/logo.png" alt="E-Khairat Logo" width="150" height="150">
</div>


## 📖 About the Project

E-Khairat is a comprehensive digital platform designed to manage the death benefit (khairat kematian) services for Masjid Taman Sutera community in Kajang. This web application streamlines the registration process, payment management, and administrative tasks for the mosque's death benefit bureau (Biro Khairat Kematian).

### 🎯 Mission
To provide fast, efficient, and respectful death management services to the Taman Sutera community and surrounding areas, following Islamic teachings and principles.

### 🌟 Key Features

- **Member Registration**: Complete online registration system for new members
- **Dependent Management**: Add and manage family members under coverage
- **Online Payments**: Secure payment processing via ToyyibPay integration
- **Admin Dashboard**: Comprehensive admin panel built with Filament
- **Member Portal**: Personal dashboard for members to manage their accounts
- **Infaq/Donation System**: Digital donation platform for community support
- **Receipt Generation**: Automated PDF receipt generation
- **Notification System**: Email notifications for important updates
- **Multi-language Support**: Malay and English language options

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **Authentication**: Laravel Jetstream with Livewire
- **Database**: SQLite (development)
- **Admin Panel**: Filament 3.3
- **PDF Generation**: DomPDF
- **Payment Gateway**: ToyyibPay

### Frontend
- **UI Framework**: Livewire 3.x
- **CSS Framework**: Tailwind CSS 3.4
- **Components**: Flowbite
- **Icons**: Heroicons
- **Build Tool**: Vite 6.x

### Development Tools
- **Testing**: PestPHP
- **Code Style**: Laravel Pint
- **Debugging**: Laravel Debugbar
- **Local Development**: Laravel Herd

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite or MySQL database
- ToyyibPay merchant account (for payment processing)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/e-khairat.git
cd e-khairat
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Environment Variables
Edit the `.env` file with your configuration:

```env
APP_NAME="E-Khairat"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

# ToyyibPay Configuration
TOYYIBPAY_CATEGORY_CODE_YURAN_KHAIRAT=your_category_code
TOYYIBPAY_CATEGORY_CODE_INFAQ_KHAIRAT=your_infaq_category_code
TOYYIBPAY_SECRET_KEY=your_secret_key

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@khairatsutera.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 6. Database Setup
```bash
# Create database file (for SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed
```

### 7. Build Assets
```bash
npm run build
# or for development
npm run dev
```

### 8. Start the Application
```bash
php artisan serve
```

Visit `http://localhost:8000` to access the application.

## 🔧 Configuration

### ToyyibPay Setup
1. Register for a ToyyibPay merchant account
2. Obtain your category codes and secret key
3. Update the `.env` file with your ToyyibPay credentials
4. Configure the callback URLs in your ToyyibPay dashboard

### Admin Account
Create an admin user:
```bash
php artisan tinker
```
```php
$user = \App\Models\User::create([
    'No_Ahli' => '0001',
    'ic_number' => '123456789012',
    'name' => 'Admin User',
    'email' => 'admin@khairatsutera.com',
    'password' => bcrypt('password'),
    'phone_number' => '0123456789',
    'address' => 'Admin Address',
    'age' => 30,
]);

$adminRole = \App\Models\Role::create(['name' => 'admin']);
$user->roles()->attach($adminRole->id);
```

## 📱 Usage

### For Members
1. **Registration**: Visit the homepage and click "Daftar Sekarang"
2. **Fill Details**: Complete personal information and dependent details
3. **Payment**: Pay the registration fee via ToyyibPay
4. **Access Dashboard**: Login to manage your account and dependents

### For Admins
1. **Access Admin Panel**: Visit `/admin` and login
2. **Manage Members**: View, edit, and manage member accounts
3. **Payment Records**: Track all payments and transactions
4. **Generate Reports**: Export member lists and payment reports
5. **Manage Categories**: Configure payment categories and amounts

## 🏗️ Project Structure

```
app/
├── Actions/Fortify/          # Jetstream authentication actions
├── Filament/                 # Admin panel resources
│   ├── Pages/               # Custom admin pages
│   ├── Resources/           # Model resources
│   └── Widgets/             # Dashboard widgets
├── Http/
│   ├── Controllers/         # Application controllers
│   └── Livewire/           # Livewire components
├── Models/                  # Eloquent models
└── Notifications/          # Email notifications

resources/
├── views/
│   ├── livewire/           # Livewire blade templates
│   ├── pdf/                # PDF templates
│   └── components/         # Blade components
├── css/                    # Stylesheets
└── js/                     # JavaScript files

database/
├── migrations/             # Database migrations
└── seeders/               # Database seeders
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
# or
./vendor/bin/pest
```

## 📊 Key Models

- **User**: Member accounts and authentication
- **Dependent**: Family members under coverage
- **Payment**: Payment records and transactions
- **PaymentCategory**: Different types of payments
- **Role**: User role management
- **Infaq**: Donation records

## 🔐 Security Features

- CSRF protection on all forms
- Password hashing with bcrypt
- Session-based authentication
- Input validation and sanitization
- Secure payment processing via ToyyibPay

## 🌐 API Integration

### ToyyibPay Integration
- **Registration Payments**: Automated bill creation for member registration
- **Infaq Donations**: Flexible donation amounts
- **Callback Handling**: Secure payment verification
- **Receipt Generation**: Automatic PDF receipts

## 📈 Performance Optimization

- **Asset Compilation**: Vite for optimized CSS/JS bundling
- **Database Indexing**: Proper indexing on frequently queried fields
- **Lazy Loading**: Efficient relationship loading
- **Caching**: Session and configuration caching

## 🔄 Deployment

### Production Deployment
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure production database
4. Set up proper mail configuration
5. Run optimization commands:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.


## 🙏 Acknowledgments

- **Masjid Taman Sutera** - For their trust and support
- **Laravel Community** - For the amazing framework
- **ToyyibPay** - For payment gateway services
- **Filament** - For the excellent admin panel

---

## 📋 Changelog

### Version 1.0.0
- Initial release
- Member registration system
- Payment integration with ToyyibPay
- Admin dashboard with Filament
- PDF receipt generation
- Email notifications
- Infaq donation system

---

**Biro Khairat Kematian Masjid Taman Sutera © 2025. All rights reserved.**

*"Sebaik-baik manusia adalah yang paling bermanfaat bagi manusia lain"*
