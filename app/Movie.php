<?php
include_once 'Data.php';

class Movie extends Data
{
    public function getMovieByFilters ()
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