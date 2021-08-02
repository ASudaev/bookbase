<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ControllerJsonResponse
{
    /**
     * @param mixed $data Response data
     * @param int $status Response HTTP status
     *
     * @return JsonResponse
     */
    private function response($data, int $status = 200): JsonResponse
    {
        return $this->json($data, $status);
    }
}