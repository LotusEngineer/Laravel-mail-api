git lfs pull
COMPOSER_DISCARD_CHANGES=true composer install --optimize-autoloader --no-dev --prefer-source --ignore-platform-reqs
composer upgrade
cp .env.example .env
php artisan key:generate
if [ -f 'package.json' ]; then
    yarn install
fi
if [ -f 'gulpfile.js' ]; then
    gulp build
fi
