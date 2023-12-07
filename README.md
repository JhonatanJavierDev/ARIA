# AriaRouter



AriaRouter is a powerful and simple PHP router, ideal for small-scale projects. Its glorious simplicity makes it easy to use and integrate into your projects.

## Examples

```php
<?php

Router::get('/', function () {
    echo 'Hello, World!';
});

Router::get('/user/{id}', function ($id) {
    echo 'User ID: ' . $id;
});

Router::post('/submit-form', function () {
    // Logic to handle form submissions
});

Router::any('/fallback', 'fallback_page.php');

Router::run();
