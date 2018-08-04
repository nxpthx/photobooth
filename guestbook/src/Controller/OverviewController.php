<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OverviewController extends Controller
{

    public function index()
    {
        $files = $this->getFiles();
        $guestBookData = json_decode(file_get_contents($this->container->getParameter('guestbook.fotoPath') . 'guestBookData.json'), true);
        return $this->render('overview/index.html.twig', [
           'number' => count($files),
            'files' => $files,
            'guestBookData' => $guestBookData,
        ]);
    }

    protected function getFiles() {
        $path = $this->container->getParameter('guestbook.fotoPath');
        $files = glob($path . '*.[jJ][pP][gG]');
        $files = array_map('basename', $files);
        return $files;
    }

    public function fileCount()
    {
        return new Response(count($this->getFiles()));
    }

}