# Server timing middleware

Middleware to calculate the response time (in milliseconds) and save it into the
[Server Timing](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Server-Timing) header.

## Requirements
* PHP >= 7.4
* A PSR-7 http library
* A PSR-15 middleware dispatcher

## Installation
```sh
composer require middlewares/response-time
```

## License
The MIT License
