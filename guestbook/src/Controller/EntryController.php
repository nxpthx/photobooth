<?php
namespace App\Controller;

use App\Entity\Entry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EntryController extends Controller
{
    protected $data = [];
    protected $browsing = [];

    public function newEntry(Request $request, string $fileName)
    {
        $entry = new Entry();
        $entry->fileName = $fileName;

        $form = $this->createFormBuilder($entry)
            ->add('author', TextType::class, array('label' => 'Wer bist du'))
            ->add('message', TextareaType::class, array('label' => 'Deine Nachricht'))
            ->add('save', SubmitType::class, array('label' => 'Gästebuch-Eintrag erstellen'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entry = $form->getData();
            $this->readData();
            if (!array_key_exists($fileName, $this->data)) {
                $this->data[$fileName] = [];
            }

            $id = count($this->data[$fileName]) + 1;
            $this->data[$fileName][$id] = [
                'message' => $entry->message,
                'author' => $entry->author
            ];
            $this->writeData();
            return $this->redirectToRoute('app_index');
        }
        return $this->render('entry/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    public function deleteEntry(string $fileName, int $number)
    {
        $this->readData();
        if (isset($this->data[$fileName]) && isset($this->data[$fileName][$number])) {
            unset($this->data[$fileName][$number]);
        }
        $this->writeData();
    }

    public function first() {
        $this->readData();
        $data = explode('=', $this->browsing[0]);
        return $this->view($data[0], $data[1]);
    }

    public function view(string $fileName, int $number) {
        $this->readData();
        if (!isset($this->data[$fileName][$number])) {
            return new Response('', 404);
        }
        return $this->render('entry/view.html.twig', array(
            'fileName' => $fileName,
            'number' => $number,
            'entries' => $this->data,
            'next' => $this->getNext($fileName, $number),
            'previous' => $this->getPevious($fileName, $number)
        ));
    }

    public function editEntry(Request $request, string $fileName, int $number)
    {
        $this->readData();
        if (!isset($this->data[$fileName][$number])) {
            return new Response('', 404);
        }

        $entry = new Entry();
        $entry->fileName = $fileName;
        $entry->number = $number;
        $entry->message = $this->data[$fileName][$number]['message'];
        $entry->author = $this->data[$fileName][$number]['author'];

        $form = $this->createFormBuilder($entry)
            ->add('author', TextType::class, array('label' => 'Wer bist du'))
            ->add('message', TextareaType::class, array('label' => 'Deine Nachricht'))
            ->add('save', SubmitType::class, array('label' => 'Eintrag aktualisieren'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entry = $form->getData();

            $this->data[$fileName][$number] = [
                'message' => $entry->message,
                'author' => $entry->author
            ];
            $this->writeData();
            return $this->redirectToRoute('app_index');
        }
        return $this->render('entry/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    protected function readData()
    {
        $this->data = json_decode(file_get_contents($this->container->getParameter('guestbook.fotoPath') . 'guestBookData.json'), true);
        $this->browsing = [];
        foreach ($this->data as $file => $entries) {
            foreach (array_keys($entries) as $number) {
                $this->browsing[] = $file . '=' . $number;
            }
        }
    }

    protected function writeData()
    {
        file_put_contents($this->container->getParameter('guestbook.fotoPath') . 'guestBookData.json', json_encode($this->data));
    }

    protected function getNext($fileName, $number) {
        $position = array_search($fileName . '=' . $number, $this->browsing);
        $count = count($this->browsing);

        if ($position == $count - 1) {
            $next = 0;
        } else {
            $next = $position + 1;
        }

        $data = explode('=', $this->browsing[$next]);
        return [
            'fileName' => $data[0],
            'number' => $data[1]
        ];
    }

    protected function getPevious($fileName, $number) {
        $position = array_search($fileName . '=' . $number, $this->browsing);
        $count = count($this->browsing);

        if ($position == 0) {
            $previous = $count - 1;
        } else {
            $previous = $position - 1;
        }

        $data = explode('=', $this->browsing[$previous]);
        return [
            'fileName' => $data[0],
            'number' => $data[1]
        ];
    }
}