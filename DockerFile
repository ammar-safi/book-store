# استخدم صورة PHP مع Apache
FROM php:8.2-apache

# تثبيت التبعيات
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql zip

# تفعيل Apache mod_rewrite
RUN a2enmod rewrite

# نسخ المشروع إلى الصورة
COPY . /var/www/html/

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تثبيت الاعتماديات
RUN composer install --no-dev --optimize-autoloader

# إعداد صلاحيات المجلدات
RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# تعيين DocumentRoot لـ Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf