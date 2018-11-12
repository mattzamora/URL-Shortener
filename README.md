# URL-Shortener

##Installation Guide
1. After the files are on the production server, install the dependencies
cd my-project/  <br>
composer install <br>
2. Update the .env for the MySQL database login credentials used
3. Migrate the database:  <br>
 php bin/console make:migration <br>
 php bin/console doctrine:migrations:migrate <br>
4. Configure Apache or run via  <br>
php bin/console server:run <br>

##Database Schema
Enity/Table: **Url**
short_stub  - varchar[9]/string - not nullable <br>
vanity - varchar[255]/string - nullable <br>
redirect_count integer - not nullable <br>
qr_code_address varchar[255]/string - nullable <br>
created_on - datetime - not nullable <br>
long_url - varchar[2000]/string - not nullable <br>