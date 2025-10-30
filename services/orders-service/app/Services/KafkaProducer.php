<?php

namespace App\Services;

use RdKafka\Producer;

class KafkaProducer
{
    protected $producer;

    public function __construct()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', env('KAFKA_BROKERS', 'kafka:9092'));

        // Optional: improve reliability
        $conf->set('acks', 'all');

        $this->producer = new Producer($conf);
    }

    public function publish(string $topic, array $payload)
    {
        $topicConf = new \RdKafka\TopicConf();
        $kafkaTopic = $this->producer->newTopic($topic, $topicConf);

        $message = json_encode($payload);

        $kafkaTopic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        // Poll to send
        for ($i = 0; $i < 10; $i++) {
            $this->producer->poll(10);
            if ($this->producer->getOutQLen() == 0) break;
        }
    }
}