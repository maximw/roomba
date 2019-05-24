<?php


namespace App\Controller;


use App\Service\Runners\JsonRunner;
use App\Service\Runners\ReportSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController
{

    /**
     * @Route("/")
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $jsonRunner = new JsonRunner();
        $report = $jsonRunner->run($request->getContent());

        $serializer = new ReportSerializer($report);
        return new JsonResponse($serializer->asArray(), JsonResponse::HTTP_OK);
    }
}