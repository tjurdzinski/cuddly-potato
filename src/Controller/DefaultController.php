<?php

namespace App\Controller;

use App\Service\StatsCalculator;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @Template()
     */
    public function index()
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     *
     * @Route("/parse", name="parse", methods={"POST"})
     * @Template()
     */
    public function parse(Request $request)
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('subtitle');

        if (empty($file)) {
            throw new \InvalidArgumentException('Subtitle file not exists.');
        }

        $stats = $this->container->get(StatsCalculator::class)->getStats($file->getRealPath());

        return [
            'stats' => $stats->getArray(),
            'filename' => $file->getClientOriginalName(),
        ];
    }
}
