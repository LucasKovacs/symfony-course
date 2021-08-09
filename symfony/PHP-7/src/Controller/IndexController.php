<?php

namespace App\Controller;

use App\Service\Serializer;

class IndexController
{
    public function __constructor(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function index()
    {
        return $this->serializer->serialize([
            'Action' => 'Index',
            'Time' => time(),
        ]);
    }
}
