<?php
namespace Riemann;

class EventBuilder
{
    const DEFAULT_METRIC = 1;

    private $dateTimeProvider;
    private $host;
    private $tags;
    private $service;
    private $metric = 1;

    public function __construct(
        DateTimeProvider $dateTimeProvider,
        $host,
        array $initialTags = array()
    ) {
        $this->dateTimeProvider = $dateTimeProvider;
        $this->host = $host;
        $this->tags = $initialTags;
    }

    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    public function setMetric($metric)
    {
        $this->metric = $metric;
        return $this;
    }

    public function addTag($tag)
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function build()
    {
        if (!$this->service) {
            throw new \RuntimeException('A service has to be set.');
        }
        $event = new Event();
        $event->host = $this->host;
        $event->time = $this->dateTimeProvider->now()->getTimestamp();
        $event->service = $this->service;
        $event->tags = $this->tags;

        $floatMetric = (float)$this->metric;
        $event->metric_f = $floatMetric;
        if (is_int($this->metric)) {
            $event->metric_sint64 = $this->metric;
        } else {
            $event->metric_d = $floatMetric;
        }

        return $event;
    }

}
