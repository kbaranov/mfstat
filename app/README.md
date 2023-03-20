### To launch the project

#### Up docker containers:
`docker-compose up`

#### Enter to the app docker container:
`docker exec -it mfstat_app bash`

#### Install libraries in the app docker container:
`composer install`

### API Documentation
http://localhost/api/doc

### Tests
You can test API endpoints through HttpClientRequests in PHPStorm:

/app/tests/HttpClientRequests

### Workflow

Endpoint `POST /api/visits` push country code ("AU", "US", ...) to queue.

Worker (command `bin/console app:store-visits`) read the queue and store data to hash-map.

Script (command `bin/console app:cache-visits`) run every one minutes and update cache from hash-map. 

Endpoint `GET /api/visits` getting data from cache.
