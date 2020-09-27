**Set environment variables**

Edit the following within .env:

```
AKISMET_KEY=abc

ADMIN_EMAIL=admin@localhost
MAILER_DSN=smtp://mailtrapsettingsinyouruseraccount
```

* [Akismet signup](https://akismet.com/signup)
* [Mailtrap signup](https://mailtrap.io/register/signup)

**Download Composer dependencies**

```
composer install
```

**Start Docker containers**

```
docker-compose up -d
```

**Create database and load fixtures**

```
bin/console doctrine:database:create
bin/console doctrine:migration:migrate -n
bin/console doctrine:fixtures:load -n
```
**Login**

You can browse the site as a guest and leave comments. However you will need to login to authorise comments.

```
Email: admin
Password: admin
```