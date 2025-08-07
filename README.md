# AI Agents SaaS Platform

A comprehensive SaaS platform for creating, managing, and deploying custom AI chatbots powered by OpenAI's GPT models. Built with Laravel and designed for easy deployment on any hosting provider, especially optimized for Plesk-managed servers.

![AI Agents SaaS](https://via.placeholder.com/800x400/8B5CF6/FFFFFF?text=AI+Agents+SaaS+Platform)

## 🚀 Features

### 🧠 AI Agent Management
- **Custom AI Agents**: Create unlimited AI chatbots with unique personalities
- **Advanced Configuration**: Fine-tune temperature, tokens, penalties, and response styles
- **Model Selection**: Support for GPT-3.5-turbo, GPT-4, and other OpenAI models
- **Avatar & Branding**: Upload custom avatars and set welcome messages
- **Categories & Organization**: Organize agents by category and purpose
- **Multi-language Support**: Configure agents for different output languages

### 💬 ChatGPT-like Interface
- **Real-time Streaming**: Message streaming with typing indicators
- **Message History**: Persistent chat history per user and thread
- **Responsive Design**: Optimized for desktop, tablet, and mobile devices
- **Dark/Light Mode**: Automatic theme switching with user preferences
- **Export Options**: Export conversations in text or JSON format
- **Shareable Chats**: Generate public links for sharing conversations

### 👥 User Management
- **Multi-auth System**: Email/password and Google OAuth authentication
- **Credit System**: Flexible credit-based messaging with free allowances
- **User Dashboard**: Comprehensive overview of agents, chats, and purchases
- **Profile Management**: User preferences and account settings

### 💳 Payment & Billing
- **Multiple Payment Methods**:
  - Stripe (automatic processing)
  - PayPal (automatic processing)  
  - Manual bank transfers (admin approval)
- **Flexible Credit Packages**: Configurable pricing tiers with bonus credits
- **Payment History**: Complete transaction tracking and receipts
- **Admin Approval**: Manual verification for bank transfer payments

### 🔧 Admin Dashboard
- **User Management**: View, edit, and manage user accounts
- **Agent Oversight**: Monitor all AI agents and their usage
- **Payment Processing**: Approve/reject manual payments
- **Analytics**: Revenue, usage, and performance metrics
- **System Settings**: Configure API keys, mail settings, and platform preferences
- **Credit Package Management**: Create and manage pricing tiers

### 🌐 Public & Embedding
- **SEO-Optimized Pages**: Public agent pages with meta tags and structured data
- **Iframe Embedding**: Easily embed agents on external websites
- **Public Agent Discovery**: Browse and interact with public agents
- **Social Sharing**: Share agents across social platforms

### ⚡ Technical Features
- **Easy Installation**: One-click installation wizard for Plesk deployments
- **Scalable Architecture**: Laravel-based with Redis caching support
- **API Integration**: RESTful APIs for external integrations
- **Security**: CSRF protection, SQL injection prevention, XSS protection
- **Performance**: Optimized database queries and caching strategies

## 📋 Requirements

### Server Requirements
- **PHP**: 8.2 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Web Server**: Apache or Nginx
- **Memory**: 512MB RAM minimum (2GB+ recommended)
- **Storage**: 1GB+ available disk space

### PHP Extensions
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- Tokenizer
- XML
- GD (for image processing)
- Redis (optional, for caching)

### External Services
- **OpenAI API** (required)
- **Stripe Account** (optional, for payments)
- **PayPal Developer Account** (optional, for payments)
- **Google OAuth App** (optional, for social login)
- **SMTP Server** (optional, for email notifications)

## 🚀 Quick Installation (Plesk)

### Method 1: Upload & Install (Recommended)

1. **Download & Upload**
   ```bash
   # Download the latest release
   wget https://github.com/your-repo/ai-agents-saas/archive/main.zip
   
   # Upload to your domain directory via Plesk File Manager
   # Extract the zip file in your domain's httpdocs folder
   ```

2. **Set Permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod -R 777 storage/app/public storage/framework storage/logs
   ```

3. **Run Installation Wizard**
   - Navigate to `https://yourdomain.com/install`
   - Follow the step-by-step installation process
   - Configure database, admin user, and API keys
   - Complete the installation

### Method 2: Manual Installation

1. **Clone Repository**
   ```bash
   git clone https://github.com/your-repo/ai-agents-saas.git
   cd ai-agents-saas
   ```

2. **Install Dependencies**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database**
   ```bash
   # Edit .env file with your database credentials
   php artisan migrate
   ```

5. **Create Admin User**
   ```bash
   php artisan make:user --admin
   ```

6. **Set Permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

## ⚙️ Configuration

### Required Environment Variables

```env
# Application
APP_NAME="AI Agents SaaS"
APP_ENV=production
APP_KEY=base64:your-app-key
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# OpenAI (Required)
OPENAI_API_KEY=sk-your-openai-api-key
OPENAI_ORGANIZATION=org-your-organization-id

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com

# Payment Gateways (Optional)
STRIPE_KEY=pk_your_stripe_key
STRIPE_SECRET=sk_your_stripe_secret
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# Google OAuth (Optional)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret

# Platform Settings
DEFAULT_FREE_MESSAGES=3
DEFAULT_CREDITS_PER_MESSAGE=1
INSTALLATION_COMPLETED=true
```

### Webhook Configuration

#### Stripe Webhooks
1. Go to Stripe Dashboard → Webhooks
2. Add endpoint: `https://yourdomain.com/webhooks/stripe`
3. Select events: `payment_intent.succeeded`, `payment_intent.payment_failed`
4. Copy webhook secret to `.env` file

## 🎯 Usage Guide

### Creating Your First AI Agent

1. **Login as Admin**
   - Navigate to `/login`
   - Use credentials created during installation

2. **Create Agent**
   - Go to "My Agents" → "Create Agent"
   - Fill in basic information (name, description, category)
   - Configure AI settings (model, temperature, prompts)
   - Set response style (tone, writing style, language)
   - Choose visibility (public/private)

3. **Test Your Agent**
   - Click "Chat" to test the agent
   - Refine the system prompt based on responses
   - Adjust parameters for optimal performance

### Setting Up Payments

1. **Configure Payment Methods**
   - Admin Panel → Settings
   - Add Stripe/PayPal API keys
   - Configure webhook endpoints

2. **Create Credit Packages**
   - Admin Panel → Credit Packages
   - Set pricing, credit amounts, and bonuses
   - Mark popular packages for highlighting

3. **Test Payment Flow**
   - Create test user account
   - Purchase credits using test payment methods
   - Verify credit allocation and usage

### Embedding Agents

1. **Make Agent Public**
   - Edit agent → Check "Make this agent public"
   - Save changes

2. **Get Embed Code**
   - Visit public agent page
   - Click "Embed Code" button
   - Copy iframe code

3. **Add to Website**
   ```html
   <iframe src="https://yourdomain.com/embed/agent-slug" 
           width="100%" 
           height="600" 
           frameborder="0">
   </iframe>
   ```

## 🔧 Advanced Configuration

### Performance Optimization

1. **Enable Caching**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Queue Configuration**
   ```env
   QUEUE_CONNECTION=redis
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

3. **CDN Setup** (Optional)
   - Configure CDN for static assets
   - Update `APP_URL` for asset serving

### Security Hardening

1. **SSL Certificate**
   - Install SSL certificate via Plesk
   - Force HTTPS redirects

2. **Firewall Rules**
   - Block unnecessary ports
   - Whitelist admin IP addresses

3. **Regular Updates**
   ```bash
   composer update
   php artisan migrate
   ```

### Backup Strategy

1. **Database Backups**
   ```bash
   # Daily backup script
   mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
   ```

2. **File Backups**
   - Include `storage/app` directory
   - Backup `.env` file securely
   - Schedule regular backups via Plesk

## 🐛 Troubleshooting

### Common Issues

1. **Installation Fails**
   - Check PHP version and extensions
   - Verify file permissions
   - Review error logs in `storage/logs`

2. **OpenAI API Errors**
   - Verify API key is correct
   - Check API usage limits
   - Ensure billing is set up

3. **Payment Issues**
   - Verify webhook URLs are accessible
   - Check API keys and secrets
   - Review payment gateway logs

4. **Performance Issues**
   - Enable caching
   - Optimize database queries
   - Consider Redis for sessions

### Log Files

- **Application Logs**: `storage/logs/laravel.log`
- **Web Server Logs**: Check Plesk logs
- **Payment Logs**: Admin Panel → Payments

### Getting Help

1. **Documentation**: Check this README and inline comments
2. **Error Logs**: Always check logs first
3. **Community**: Join our Discord/Slack community
4. **Support**: Contact support with error details

## 📊 Monitoring & Analytics

### Built-in Analytics

- **Revenue Tracking**: Daily, monthly, yearly revenue reports
- **Usage Statistics**: Message counts, popular agents, user engagement
- **Performance Metrics**: Response times, error rates
- **User Analytics**: Registration trends, retention rates

### External Monitoring

- **Google Analytics**: Add tracking code to layout
- **Error Tracking**: Integrate Sentry or similar service
- **Uptime Monitoring**: Use services like Pingdom or UptimeRobot

## 🔄 Updates & Maintenance

### Regular Maintenance

1. **Weekly Tasks**
   - Review error logs
   - Check payment reconciliation
   - Monitor API usage

2. **Monthly Tasks**
   - Update dependencies
   - Review user feedback
   - Analyze performance metrics

3. **Quarterly Tasks**
   - Security audit
   - Backup testing
   - Performance optimization

### Update Process

1. **Backup Everything**
   ```bash
   # Database backup
   mysqldump -u user -p database > backup.sql
   
   # Files backup
   tar -czf files_backup.tar.gz /path/to/project
   ```

2. **Update Code**
   ```bash
   git pull origin main
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **Run Migrations**
   ```bash
   php artisan migrate
   php artisan config:cache
   ```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## 📞 Support

- **Documentation**: This README and inline code comments
- **Issues**: GitHub Issues for bug reports
- **Discussions**: GitHub Discussions for questions
- **Email**: support@yourdomain.com

## 🙏 Acknowledgments

- **Laravel Framework**: The robust PHP framework powering this platform
- **OpenAI**: For providing the AI models that make this possible
- **Tailwind CSS**: For the beautiful, responsive UI components
- **Alpine.js**: For lightweight JavaScript interactions
- **Font Awesome**: For the comprehensive icon library

---

**Built with ❤️ using Laravel, OpenAI, and modern web technologies.**
