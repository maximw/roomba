# Roomba

If you dont have it installed yet
<a href="https://www.php.net/manual/en/install.php">Install PHP 7.1 or later </a>
<a href="https://getcomposer.org/download/">Install composer</a>

## Console command

```
git clone https://github.com/maximw/roomba.git
```

```
cd ./roomba
```

```
composer update
```

Run console command:

```
php ./bin/console run <input.json> <output.json>
```

## HTTP microservice

```
php bin/console server:start
```

Send HTTP POST request to http://127.0.0.1:8000 with JSON string in request body

For example
```
POST  HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json
Accept: */*
Cache-Control: no-cache
Host: 127.0.0.1:8000
accept-encoding: gzip, deflate
content-length: 237
Connection: keep-alive
cache-control: no-cache

{
  "map": [
    ["S", "S", "S", "S"],
    ["S", "S", "C", "S"],
    ["S", "S", "S", "S"],
    ["S", "null", "S", "S"]
  ],
  "start": {"X": 3, "Y": 0, "facing": "N"},
  "commands": [ "TL","A","C","A","C","TR","A","C"],
  "battery": 80
}
```