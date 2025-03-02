# jem-v0.09
 
 multi project based managment system

Laravel 11 v11.22.0
PHP v8.3.10   [eto or pataas ang php version] [if using xampp, need mag download ng specific version netong php then lagay sa php folder ng xampp. if using laragon, you must know what you're doing. hahahaha (mej komplikado pag self learn, pero madali lang)]

hahahhaha

before cloning (balakajan na clone mo na hahaha)

install muna composer galing sa internet HA.
https://getcomposer.org/Composer-Setup.exe
[rember to add to path para di na tyo mag search kung paano yung error]

install muna node js galing internet HA.
https://nodejs.org/dist/v22.8.0/node-v22.8.0-x64.msi
[rember to add to path para di na tyo mag search kung paano yung error](copy paste haha)

after non, clone na from github HA. pero dahil nababasa mo na to, malamang na-clone na HAHA.
{basta clone sa htdocs folder if using xampp, www folder if using laragon.}

commands after ma clone--------

composer install

cp .env.example .env
//configure .env file 

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jem
DB_USERNAME=root
DB_PASSWORD=

APP_URL=http://127.0.0.1:8000

php artisan key:generate

php artisan migrate --seed
** if error(would re migrate) == php artisan migrate:fresh --seed




 php artisan shield:install
 php artisan shield:generate



 setup trello
 ////////////////////vvvvvv
 trello.com/power-ups/admin











php artisan permission:sync

php artisan serve
** click mo po yung link na lalabas ctrl + left click

**if style not working properly, run
npm run build
npm run dev

paalam sa gc pag may gagawin or tanong (nakakatakot magbago ng code, nakakasira ng buong project) HAHAHAHAHAHAHAHAHAHAHAHAHAHA


for new policies
php artisan permissions:sync -P --policies


ADMIN log in account
admin@email.com
password
^^ is the password


other commands [only for development]

php artisan storage:link
php artisan vendor:publish --tag="filament-views"

https://trello.com/power-ups

https://filamentphp.com/plugins/joaopaulolndev-edit-profile


STRICTLY NO COMPOSER UPDATE


newwww
php artisan migrate
php artisan db:seed
php artisan shield:install --fresh
php artisan shield:generate
php artisan db:seed --class=Roles
php artisan db:seed --class=TestDataSeeder

reload
php artisan migrate:fresh
php artisan db:seed
php artisan shield:install --fresh
php artisan shield:generate
php artisan db:seed --class=Roles
php artisan db:seed --class=TestDataSeeder
php artisan db:seed --class=PackageTaskSeeder
php artisan db:seed --class=SkillTask

new reload
    php artisan migrate:fresh
    php artisan db:seed
    php artisan shield:install --fresh
    php artisan shield:generate
    php artisan db:seed --class=Roles
    php artisan db:seed --class=PackageTaskSeeder
    php artisan db:seed --class=SkillTask
    php artisan db:seed --class=FakeUser


run:
    php artisan serve
    php artisan queue:work

if trello not working::
    php artisan config:clear
    php artisan cache:clear




# setting up the project

# step 1:
after cloning, run these commands:
    composer install
    npm install

# step 2:
set up the environment variables. run:
    cp .env.example .env

# step 3:
run these commands to finish the setup

    app key:
        php artisan key:generate

    storage:
        php artisan storage:link

    migrating the database and seeding required data:
        php artisan migrate
        php artisan db:seed
    
    setting up the shield (yes to all):
        php artisan shield:install --fresh
        php artisan shield:generate

    seeding remaining data seeders:
        php artisan db:seed --class=Roles
        php artisan db:seed --class=PackageTaskSeeder
        php artisan db:seed --class=SkillTask

# step 4:
build the app
    npm run build

# step 5:
run the app locally (in different terminals)
    php artisan serve
    php artisan reverb:start
    php artisan queue:work --queue=messages,default