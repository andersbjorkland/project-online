<?php


namespace App\Controller\Admin;


use http\Env;
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

		$target = '';
		if ($_SERVER['APP_ENV'] !== 'prod') {
			$target = $this->getParameter('kernel.project_dir');
		}

		$destination = $target.'/public/uploads';

		$allowedFiles = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];


		if (!$uploadedFile) {
			return new JsonResponse(array(
				'status' => 'Error',
				'message' => 'No image to upload'),
				Response::HTTP_FORBIDDEN);
		}

		$extension = $uploadedFile->guessExtension();
		if (!in_array($extension, $allowedFiles)) {
			return new JsonResponse(array(
				'status' => 'Error',
				'message' => 'Not allowed filetype: ' . $extension),
				Response::HTTP_FORBIDDEN);
		}


		$originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
		$newFilename = $originalFilename . '-' . uniqid() . '.' . $extension;
		$logger->info("################################################################");
		$logger->info($extension);
		$uploadedFile->move($destination, $newFilename);

		$response = new Response();
		$response->setContent(json_encode([
			'filepath' => '/uploads/'.$newFilename,
		]));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}
