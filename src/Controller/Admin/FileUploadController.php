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

		if (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
		    || strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false) {
			$target = $this->getParameter('kernel.project_dir') . '/public';
		} else {
			$target = dirname(__DIR__).'/../httpd.www/playground';
		}

		$destination = $target.'/uploads';

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
