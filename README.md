### To launch the project

#### Up docker containers:
`docker-compose up`

#### Enter to the app docker container:
`docker exec -it mfstat_app bash`

#### Install libraries in the app docker container:
`composer install`

### API Documentation
http://localhost/api/doc

There are "health-check" endpoints and "visits" endpoints.

"Health-check" endpoint `http://localhost/api/readiness` use for probe application for readiness to work (Redis availability check included).

"Visits" endpoints `POST http://localhost/api/visits/` and `GET http://localhost/api/visits` store and return statistics in respectively.

Supported countries list is hardcoded as constant in `/app/src/Api/Controller/VisitsController.php`

### Tests 
You can test API endpoints by HTTP requests directly in the PhpStorm code editor (with the [HTTP Client plugin](https://www.jetbrains.com/help/phpstorm/http-client-in-product-code-editor.html)).

Located on directory `/app/tests/HttpClientRequests`.

### Workflow

Endpoint `POST http://localhost/api/visits` push country code ("AU", "US", ...) to Redis-Queue.

Worker (command `bin/console app:store-visits`) read the Redis-Queue and put data to Redis-HashMap: create new "country-visits" pair or increment visits for exists country-key.

Script (command `bin/console app:cache-visits`) run every one minutes and copy Redis-HashMap value to Redis-Cache. 

Endpoint `GET http://localhost/api/visits` getting data from Redis-Cache.
