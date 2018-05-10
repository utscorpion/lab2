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
        $movieFile = $this->checkData( '-movie.json');
        $genreFile = $this->checkData( '-genres.json');
        $moviesFromFile = json_decode(file_get_contents($movieFile), true);
        $genresFromFile = json_decode(file_get_contents($genreFile), true);

        foreach ($moviesFromFile as $movie) {
            foreach ($movie['results'] as $item => $value) {
                if (strtotime($value['release_date']) >= strtotime(date('Y-m-d') . "- $this->days days")) {
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

    protected function checkData($dataType) {
        $dayBeforeDate = 0;

        do {
            $date = date('Y-m-d', strtotime("$dayBeforeDate days"));
            $returnedFile =  $this->dbPath . $date . $dataType;
            $dayBeforeDate--;
        } while(!file_exists($returnedFile));

        return $returnedFile;
    }

}