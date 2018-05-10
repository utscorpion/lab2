<?php
include_once 'config.php';
include_once 'QueryMode.php';

class ListMode
{

    protected $dbPath;
    protected $days;

    public function __construct()
    {
        $this->dbPath = Configurator::DB_PATH;
        $this->days = Configurator::DAYS;
    }

    public function getMovieByFilters ()
    {
        $movies = [];
        $date = date('Y-m-d');
        $query= new QueryMode();
        $movieFile =  $this->dbPath . $date . '-movie.json';
        $genreFile =  $this->dbPath . $date . '-genres.json';

        if (!file_exists($genreFile)) {
            $query->getGenres();
        }

        if (!file_exists($movieFile)) {
            $query->getMovies();
        }

        $moviesFromFile = json_decode(file_get_contents($movieFile), true);
        $genresFromFile = json_decode(file_get_contents($genreFile), true);

        foreach ($moviesFromFile as $movie) {
            foreach ($movie['results'] as $item => $value) {
                if (strtotime($value['release_date']) >= strtotime("$date - $this->days day")) {
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