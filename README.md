## Meeting Room Booking

This is the API backend of the Meeting Room Booking application.

### Stacks used

-   PHP v8.4
-   Laravel v12.x
-   MySQL v8.x

### Installation

```bash
git clone git@github.com:MehulBawadia/meeting-room-booking-backend.git
cd meeting-room-booking-backend
cp .env.example .env ## Don't forget to update the DB_* credentials in the .env file
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=localhost
```

#### For Frontend

For the frontend in Vue.js 3, [click here](https://github.com/MehulBawadia/meeting-room-booking-frontend)

#### License

This project is an open-sourced software licensed under the [MIT License](https://opensource.org/license/mit)
