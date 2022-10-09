<?php
namespace DataLayer;

class MovieData
{
	private \PDO $db;

	public function __construct(\PDO $db)
	{
		$this->db = $db;
	}

    public function searchMovieWithCritera($titlePrefix, $rating, $category, int $limit): array
    {
        $baseQuery = 'SELECT FID, title, description, category, rating FROM film_list';
        $constraints = array();
        $params = array();
        if (!is_null($titlePrefix)) {
            array_push($constraints, 'title like :title');
            $params[':title'] = $titlePrefix . '%';
        }
        if (!is_null($rating)) {
            array_push($constraints, 'rating = :rating');
            $params[':rating'] = $rating;
        }
        if (!is_null($category)) {
            array_push($constraints, 'category = :category');
            $params[':category'] =  $category;
        }
        if (count($constraints) == 0) {
            $query = $baseQuery . ' LIMIT ' . $limit;
        } else {
            $query = $baseQuery . ' WHERE ' . implode(' and ', $constraints) . ' LIMIT ' . $limit;
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getMovieById(int $filmId) 
    {
        $stmt = $this->db->prepare('SELECT * FROM sakila.film 
                                INNER JOIN film_category ON film.film_id = film_category.film_id 
                                INNER JOIN category ON category.category_id = film_category.category_id 
                                WHERE film.film_id =:filmId');
        $stmt->execute(array(':filmId' => $filmId));

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public function getLanguageId()
    {
        $stmt = $this->db->prepare('SELECT language_id FROM language');
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function addMovie($title, $description, $language_id, $release_year, $rental_duration, 
    $length, $replacement_cost,  $rating,$special_features) 
    {
        $stmt = $this->db->prepare('INSERT INTO film (title, description, language_id, release_year, 
                                  rental_duration, length, replacement_cost, rating, special_features) 
                                   VALUES(:title, :description, :language_id, :release_year, :rental_duration,
                                   :length, :replacement_cost, :rating, :special_features)');
                              
        $stmt->bindparam(":title", $title);                           
        $stmt->bindparam(":description", $description);                           
        $stmt->bindparam(":language_id", $language_id);                           
        $stmt->bindparam(":release_year", $release_year);                           
        $stmt->bindparam(":rental_duration", $rental_duration);                           
        $stmt->bindparam(":length", $length);                           
        $stmt->bindparam(":replacement_cost", $replacement_cost);                           
        $stmt->bindparam(":rating", $rating);                           
        $stmt->bindparam(":special_features", $special_features);                           
        $stmt->execute();  
       
        return;                      
    }
}
