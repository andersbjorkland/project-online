<?php


namespace App\Controller\Admin;


use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileUploadController extends AbstractController
{
	/**
	 * @Route("/admin/file-upload", name="admin_upload", methods={"post", "put"})
	 */
	public function addImage(Request $request, LoggerInterface $logger) {

		$uploadedFile = $request->files->get('file');
		$destination = $this->getParameter('kernel.project_dir').'/public/uploads';


		if (!$uploadedFile) {
			return new JsonResponse(array(
				'status' => 'Error',
				'message' => 'No image to upload'),
				Response::HTTP_NOT_FOUND);
		}
		$originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
		$newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
		$uploadedFile->move($destination, $newFilename);

		$response = new Response();
		$response->setContent(json_encode([
			'filepath' => '/uploads/'.$newFilename,
		]));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}
