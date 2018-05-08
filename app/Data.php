<?php

include_once 'config.php';


interface iData
{
    public function getGenres ();
    public function getMovies ();
}

class Data implements iData
{
    protected $apiPath;
    protected $apiKey;
    protected $region;
    protected $language;
    protected $dbPath;
    protected $days;
    protected $postersPath;

    public function __construct()
    {
        $this->apiPath = Configurator::API_PATH;
        $this->apiKey = Configurator::API_KEY;
        $this->region = Configurator::REGION;
        $this->language = Configurator::LANGUAGE;
        $this->dbPath = Configurator::DB_PATH;
        $this->days = Configurator::DAYS;
        $this->postersPath = Configurator::POSTERS_PATH;
    }

    public function getGenres()
    {
        $genres = '';
        $genreFile = $this->dbPath . date('Y-m-d') . '-genres.json';
        if (!file_exists($genreFile)) {
            $genresFromApi = file_get_contents("$this->apiPath/genre/movtie/list?api_key=$this->apiKey&language=$this->language&region=$this->region", false);
            if ($this->parseHeaders($http_response_header) == '200') {
                file_put_contents($genreFile, $genresFromApi);
                $genres = $genresFromApi;
            } else {
               foreach (glob('-genres.json') as $file)  {
                   var_dump($file);

                   if (fnmatch("-genres.json", $file)) {
                       $genres = file_get_contents($this->dbPath.$file);
                       var_dump($genres);
                   }
                }
                die;
            }

        } else {
            $genres = file_get_contents($genreFile);
        }

        return $genres;
    }

    public function getMovies()
    {
        $movies = [];
        $movieFile = $this->dbPath . date('Y-m-d') . '-movie.json';
        $apiMoviePath = "$this->apiPath/movie/now_playing?api_key=$this->apiKey&language=$this->language&region=$this->region";

        if (!file_exists($movieFile)) {
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
            $this->updatePosters($this->postersPath, $movieFile);

        } else {
            $movies = file_get_contents($movieFile);
        }

        return $movies;
    }

    protected function updatePosters($dir, $file)
    {
        $pictures = [];
        $posters = json_decode(file_get_contents($file), true);

        if (file_exists($dir)) {
            foreach (glob($dir) as $oldPoster) {
                @unlink($oldPoster);
            }
        }

        foreach ($posters as $poster) {
            foreach ($poster['results'] as $data) {
                @file_put_contents("$dir/" . $data['poster_path'], file_get_contents("https://image.tmdb.org/t/p/w500/" . $data['poster_path']));
            }
        }
    }

    protected function parseHeaders( $headers )
    {
        $head = array();
        foreach( $headers as $k=>$v )
        {
            $t = explode( ':', $v, 2 );
            if( isset( $t[1] ) )
                $head[ trim($t[0]) ] = trim( $t[1] );
            else
            {
                $head[] = $v;
                if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                    $head['reponse_code'] = intval($out[1]);
            }
        }
        return $head['reponse_code'];
    }

}

