<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/uploads/courses/{filename}', name: 'serve_course_image')]
    public function serveCourseImage(string $filename): Response
    {
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/courses/' . $filename;
        
        if (!file_exists($imagePath)) {
            throw $this->createNotFoundException('Image not found');
        }
        
        return new BinaryFileResponse($imagePath);
    }
}
