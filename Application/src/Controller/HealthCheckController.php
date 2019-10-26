<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthCheckController
{
    /**
     * @Rest\Get(path="/check", name="health_check")
     * @return JsonResponse
     */
    public function check(): JsonResponse {
        return JsonResponse::create(["OK"]);
    }
}
