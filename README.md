# codeburner-router-example
A simple implementation of blog api with codeburner router, container and zend diactoros.

## Usage

Just drop the files in one directory in your server and access the index, to avoid server configuration the router will match the path defined in query string, so in order to get all categories just request:

```php
"GET" "/index.php?path=/v1/category"
```

All the paths are prefixed with `/v1/` and will always return a json response.
