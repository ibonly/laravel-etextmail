
To get the latest version of laravel-etextmail, simply 

```php

	composer require ibonly\laravel-etextmail
```
Or include 
```php
	"ibonly/laravel-etextmail: 1.0.*"
```
to your composer.json file and run `composer update` or `composer install`

Once Laravel EtextMail is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

* `Ibonly\EtextMail\EtextMailServiceProvider::class`

Also, register the Facade like so:

```php
'aliases' => [
    ...
    'EtextMail' => Unicodeveloper\EtextMail\Facades\EtextMail::class,
    ...
]
```

#configuration

```php
	
	php artisan vendor:publish --provider="Ibonly\EtextMail\EtextMailServiceProvider"
```


EtextMail::getSMSBalance()
EtextMail::sendMessage($destination, $message)
EtextMail::getMessageCount($message)
EtextMail::getCharacterCount($message)