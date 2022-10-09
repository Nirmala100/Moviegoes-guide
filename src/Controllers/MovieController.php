<?php
namespace Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \DataLayer\MovieData;

class MovieController
{
    const QUERY_LIMIT = 20;
	private MovieData $movieData;

	public function __construct(ContainerInterface $container)
	{
		$this->movieData = $container->get('movieData');
	}

    public function searchByCriteria(Request $request, Response $response, array $args)
    {
        $title = $request->getQueryParams()["title"];
        $rating = $request->getQueryParams()["rating"];
        $category = $request->getQueryParams()["category"];

        $movies = $this->movieData->searchMovieWithCritera($title, $rating, $category, self::QUERY_LIMIT);
        // Results from db may contain fields that is not necessary to be shown. Below we are extracting fields of interest to clients.
        // Also renaming the field name to be different than internal db implementation
        $movies = array_map(function($m) {
            return array(
                "filmId" => $m["FID"],
                "title" => $m["title"],
                "description" => $m["description"],
                "category" => $m["category"],
                "rating" => $m["rating"]
            );
        }, $movies);
		return $response->withJson($movies); 
    }

    public function getMovie(Request $request, Response $response, array $args)
    {   
        $id = $args['id'];
        if(!is_numeric($id)){
            return $response->withStatus(400)->withJson(array(
                'message' => 'Invalid input'
            ));
        }
        $movie = $this->movieData->getMovieById($id);
        $movie = array(
            "filmId" =>$movie["film_id"],
            "title" => $movie["title"],
            "description" => $movie["description"],
            "releaseYear" => $movie["release_year"],
            "duration" => $movie["length"],
            "specialFeatures" => $movie["special_features"],
            "rentalDuration" => $movie["rental_duration"],
            "replacementCost" => $movie["replacement_cost"],
            "category" => $movie["name"],
            "rating" => $movie["rating"]
        );
        return $response->withJson($movie);
    }

    public function addMovie(Request $request, Response $response, array $args)
    {
        $data = $request->getParsedBody();
        $title = $data['title'];
        $description = $data['description'];
        $release_year = $data['release_year'];
        $language_id = $data['language_id'];
        $original_language_id = $data['original_language_id'];
        $rental_duration = $data['rental_duration'];
        $rental_rate = $data['rental_rate'];
        $length = $data['length'];
        $replacement_cost = $data['replacement_cost'];
        $rating = $data['rating'];
        $special_features = $data['special_features'];

        $languageId = $this->movieData->getLanguageId();
        
        $values = array();
        foreach($languageId as $key => $value) { 
            array_push($values,$value['language_id']);
        } 
        
        //Check if the language from POST is valid or exist in language table
        if(!in_array($language_id,$values)) {
            return $response->withStatus(400)->withJson(array(
                'message' => 'Invalid language!'
            ));
        }

       if(is_null($title) || is_null($language_id)){
            return $response->withStatus(400)->withJson(array(
                'message' => 'Title/Language  is required'
            ));
        }
        

        $movie = $this->movieData->addMovie($title, $description, $language_id, $release_year, $rental_duration, 
                 $length, $replacement_cost,  $rating,$special_features);

        echo "Success!";
        
    }


}
