install:
	/usr/local/bin/composer install
	cp .env.example .env
	chmod -R 777 storage
	php artisan migrate
	php artisan db:seed
	php artisan serve > /dev/null 2>&1 &
