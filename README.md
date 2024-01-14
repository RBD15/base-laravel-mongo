<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


## Laravel with Mongo

1. Download latest mongodb-php library version from https://github.com/mongodb/mongo-php-driver/releases/
    * Check compatibility among PHP version, Mongodb library, SO version and Thread Safety, https://www.mongodb.com/docs/drivers/php-drivers/
2. In Windows, Move library to PHP folder, e.g: C:\wamp64\bin\php\php8.1.0\ext
3. Set php.init, extension=mongodb
    * If you're using Xamp or Wamp restart services
4. Installed package from composer in project folder
> composer require mongodb/mongodb --ignore-platform-reqs 
* In Laravel: 
> composer require mongodb/laravel-mongodb
5. Start Docker Container
    * You need to have Docker installed and running
    * e.g: docker-compose.yml 
    ```
     version: '3.5'

    services:
        mongo:
            image: mongo:6
            container_name: mongo-laravel
            restart: always
            environment:
            MONGO_INITDB_ROOT_USERNAME: root
            MONGO_INITDB_ROOT_PASSWORD: password
            MONGO_INITDB_DATABASE: laravel-mongo
            ports:
            - 27018:27017
            volumes:
            - './mongodb-local:/data/db'
    ```
    * Run
    > docker-compose up -d
6. With MongoDB Container running, check access to mongodb container through mongo cli or mongodb Atlas
7. Add database driver into config/database.php, modify default connection and add mongodb into connection
```

    'default' => env('DB_CONNECTION', 'mongodb'),

    'connections' => [

        'mongodb' => [
            'driver' => 'mongodb',
            'dsn' => env('DB_URI', 'mongodb://root:password@localhost:27018/laravel-mongo?authSource=admin&retryWrites=true&w=majority'),
            'database' => 'laravel-mongo',
        ],
    ]
```
8. Created Model and Controller for testing
9. E.g for Model
```
    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use MongoDB\Laravel\Eloquent\Model;

    class Customer extends Model
    {
        use HasFactory;
        protected  $connection = 'mongodb';
        protected $fillable = ['name','document','phone'];
    }
```
* Note:  MongoDB\Laravel\Eloquent\Model imported from mongodb/laravel-mongodb packages
10. E.g Code from Controller
```
<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function show($document)
    {
        return response()->json([
            'customer' => Customer::where('document', '=', intval($document))->first()
        ], 200);
    }

    public function store(Request $request)
    {
        $customer = new Customer();

        $customer->name = $request->name;
        $customer->document = $request->document;
        $customer->phone = $request->phone;

        $customer->save();

        return response()->json(["result" => "ok"], 201);
    }
}
```
* Note: There's no differente in controller's code , model encapsule database driver and queries.

11. Modify routes , routes/api.php
```
Route::get('/customer/{document}', [CustomerController::class, 'show']);
Route::post('/customer', [CustomerController::class, 'store']);
```
12. Run laravel
* > php artisan serve
13. Call apis from HTTP client like Postman
> POST localhost:8000/api/customer
* Request's Body
```
{
    "name":"Prueba",
    "document":1000,
    "phone":"320000000"
}
```
> GET localhost:8000/api/customer/1000
*Responde
```
{
    "customer": {
        "_id": "65a430b40d0e21692308c572",
        "name": "Prueba",
        "document": 1000,
        "phone": "320000000",
        "updated_at": "2024-01-14T19:06:28.749000Z",
        "created_at": "2024-01-14T19:06:28.749000Z"
    }
}
```


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
