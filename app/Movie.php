<?php

include_once 'config.php';

class Movie
{
    protected $apiPath;
    protected $apiKey;
    protected $region;
    protected $language;
    protected $dbPath;

    public  function __construct()
    {
       $this->apiPath = Configurator::API_PATH;
       $this->apiKey = Configurator::API_KEY;
       $this->region = Configurator::REGION;
       $this->language = Configurator::LANGUAGE;
       $this->dbPath = Configurator::DB_PATH;
    }

    public function getGenres ()
    {
        $genres = '';
        $genreFile =  $this->dbPath . date('Y-m-d') . '-genres.json';

        if(!file_exists($genreFile)) {
            $genresFromApi = file_get_contents("$this->apiPath/genre/movie/list?api_key=$this->apiKey&language=$this->language&region=$this->region", false);
            file_put_contents($genreFile, $genresFromApi);
            $genres = $genresFromApi;
        } else {
            $genres =  file_get_contents($genreFile);
        }

        return $genres;
    }

    public function getMovies ()
    {
        $movies = [];
        $movieFile =  $this->dbPath . date('Y-m-d') . '-movie.json';
        $apiMoviePath = "$this->apiPath/movie/now_playing?api_key=$this->apiKey&language=$this->language&region=$this->region";

        if(!file_exists($movieFile)) {
            $moviesFromApi = json_decode(file_get_contents("$apiMoviePath&page=1", false), true);
            $movies [] = $moviesFromApi;
            $totalPages = $movies[0]["total_pages"];
            if ($totalPages > 1) {
                for ($page = 2; $page <= $totalPages; $page++) {
                    $moviesFromApi = json_decode(file_get_contents("$apiMoviePath&page=$page", false), true);
                    $movies [] = $moviesFromApi;
                }
            }

            file_put_contents($movieFile, json_encode($movies));
            $this->updatePosters('public/images/posters/', $movieFile);

        } else {
            $movies = file_get_contents($movieFile);
        }

        return $movies;
    }

    protected function updatePosters ($dir, $file)
    {
        $pictures = [];
        $posters = json_decode(file_get_contents($file), true);

        if (file_exists($dir)) {
            foreach (glob($dir) as $oldPoster) {
                unlink($oldPoster);
            }
        }

        foreach ($posters as $poster) {

            foreach ($poster['results'] as $data) {
                file_put_contents("$dir/" . $data['poster_path'], file_get_contents("https://image.tmdb.org/t/p/w500/" . $data['poster_path']));
            }
        }
    }

    public function getMovieByFilters ($days)
    {
        $movies = [];
        $date = date('Y-m-d');
        $movieFile =  $this->dbPath . $date . '-movie.json';
        $genreFile =  $this->dbPath . $date . '-genres.json';

        if (!file_exists($genreFile)) {
            $this->getGenres();
        }

        if (!file_exists($movieFile)) {
            $this->getMovies();
        }

        $moviesFromFile = json_decode(file_get_contents($movieFile), true);
        $genresFromFile = json_decode(file_get_contents($genreFile), true);

        foreach ($moviesFromFile as $movie) {
            foreach ($movie['results'] as $item => $value) {
                if (strtotime($value['release_date']) >= strtotime("$date - $days day")) {
                    $value['genres'] = [];
                    foreach ($genresFromFile["genres"] as $genre) {
                        if (in_array($genre['id'], $value['genre_ids'])) {
                            $value['genres'][] = $genre['name'];
                        }
                    }
                    $value['genres'] = implode(', ', $value['genres']);
                    $movies[] = $value;
                }
            }
        }

        return $movies;
    }

}