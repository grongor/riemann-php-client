riemann-php-client
==================

[![Build Status](https://travis-ci.org/grongor/riemann-php-client.png)](https://travis-ci.org/grongor/riemann-php-client)

http://riemann.io/quickstart.html

Example client usage:
```php
use Riemann\Client;

require __DIR__ . '/vendor/autoload.php';

$riemannClient = new Client(new Socket());

$event = new Event();
$event->service = 'php stuff';
$event->setMetric(mt_rand(0, 99));
$event->tags = ['some', 'tags'];

$riemannClient->queueEvent($event);

$event = new Event();
$event->service = 'some more stuff';
$event->setMetric(mt_rand(0, 99));
$event->tags = ['another-tag'];

$riemannClient->queueEvent($event);

$event = new Event();
$event->service = 'stuff that skips the queue';
$event->setMetric(mt_rand(0, 99));

$riemannClient->sendEvent($event); // goes before the first two events

$riemannClient->flush();
```

Query the events:
```ruby
$ irb -r riemann/client
ruby-1.9.3 :001 > r = Riemann::Client.new
 => #<Riemann::Client ... >
ruby-1.9.3 :003 > r['service =~ "php%"']
```
