<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


use App\Entity\Pet;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\Persistence\ManagerRegistry;


#[Route("/api")]
class PetController extends AbstractController
{ 

	#[Route("/")]
	public function index()
	{
		return new Response("hello");
	}


	#[Route("/pet", name:"add_pet", methods:["POST"])]
	public function add( Request $request, ManagerRegistry $doctrine ): JsonResponse
	{

		$datos = $request->getContent();

		$datos = json_decode( $datos, true );


		if( empty( $datos["name"] ) ){
			throw new NotFoundHttpException("objeto no recibido");
		}


		$pet1 = new Pet();

		$pet1->setName( $datos["name"] );
		$pet1->setType( $datos["type"] );
		$pet1->setPhotoUrl( [$datos["photoUrl"]] );

		// $entityManager = $doctrine->getManager();

		// $entityManager->persist( $pet1 );

		// $entityManager->flush();

		$doctrine->getRepository( Pet::class )->add( $pet1 );


		return new JsonResponse([
			"status" => "pet created " . $pet1->getId(),
			Response::HTTP_CREATED
		]);
	}



	#[Route("/pet/{id}", name:"get_pet", methods:["GET"])]
	public function getPet( int $id, ManagerRegistry $doctrine ):Response
	{

		$pet = $doctrine->getRepository( Pet::class )->find( $id );

		$pet1 = [
			"name" => $pet->getName(),
			"type" => $pet->getType(),
			"foto" => $pet->getPhotoUrl()
		];

		$pet1 = json_encode( $pet1 );

		return new Response( $pet1 );
	}


	#[Route("/listado", name: "pet_listado", methods: "GET")]
	public function listado( ManagerRegistry $doctrine ): JsonResponse
	{

	    $manager = $doctrine->getRepository( Pet::class );

	    $listado = $manager->findAll();

	    // var_dump( $listado );

	    $datos = array();

	    // foreach ($listado as $key => $value) {
	        
	    //     // $datos["nombre"] = $listado[$key]->getName();
	    //     // $datos["tipo"] = $listado[$key]->getType();
	    //     // array_push( $datos, $listado[$key]->getName(), $listado[$key]->getType() );

	    //     $datos[] = [

	    //         "nombre" => $value->getName(),
	    //         "tipo" => $value->getType(),
	    //         // "fecha_nacimiento" => $value->getFechaNacimiento()
	    //     ];
	    // }


	    foreach( $listado as $key => $value ){

	    	// print_r( $value );

	    	// array[0]["nombre" => nombre, "type" => type]
	    	// array[1]["nombre" => nombre, "type" => type]

	    	$datos[$key] = array(

	    		"nombre" => $value->getName(),
	    		"type" => $value->getType(),

	    	);
	    }

	    // print_r( $datos );
	    
	    // $datos = array_chunk( $datos, 2 );

	    $datos = json_encode( $datos );

	    var_dump($datos);

	}

}