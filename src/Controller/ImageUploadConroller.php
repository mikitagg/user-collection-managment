<?php

namespace App\Controller;

use App\Form\UploadType;
use App\Service\GoogleCloudStorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ImageUploadConroller extends AbstractController
{

    public function __construct(
        private readonly GoogleCloudStorageService $googleCloudStorageService
    )
    {
    }

    #[Route("/upload", name: "upload_image")]
    public function uploadImage(Request $request)
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            $fileName = uniqid() . '.' . $file->guessExtension();
            $this->googleCloudStorageService->uploadImage($file, $fileName);
            $this->addFlash('success', 'Image uploaded successfully!');

        }
        return $this->render('upload/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


