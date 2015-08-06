<?php

namespace Riemann;

use DrSlump\Protobuf\AnnotatedMessage;

class Event extends AnnotatedMessage
{

    /** @protobuf(tag=1, type=int64, optional) */
    public $time;

    /** @protobuf(tag=2, type=string, optional) */
    public $state;

    /** @protobuf(tag=3, type=string, optional) */
    public $service;

    /** @protobuf(tag=4, type=string, optional) */
    public $host;

    /** @protobuf(tag=5, type=string, optional) */
    public $description;

    /** @protobuf(tag=7, type=string, repeated) */
    public $tags;

    /** @protobuf(tag=8, type=float, optional) */
    public $ttl;

    /** @protobuf(tag=9, type=message, reference=Riemann\Attribute, repeated) */
    public $attributes;

    /** @protobuf(tag=13, type=sint64, optional) */
    public $metric_sint64;

    /** @protobuf(tag=14, type=double, optional) */
    public $metric_d;

    /** @protobuf(tag=15, type=float, optional) */
    public $metric_f;

    /**
     * @param string|null $data
     */
    public function __construct($data = null)
    {
        $this->time = time();
        $this->host = php_uname('n');

        parent::__construct($data);
    }

    /**
     * @param int|float $metric
     */
    public function setMetric($metric)
    {
        $floatMetric = (float)$metric;
        $this->metric_f = $floatMetric;
        if (is_int($metric)) {
            $this->metric_sint64 = $metric;
        } else {
            $this->metric_d = $floatMetric;
        }
    }

}
