<?php

ini_set('display_errors', 1);
include_once 'app/ListMode.php';
include_once 'app/Logger.php';
if(!isset($argv)) {
    include_once 'templates/main.php';
} else {
    Logger::getLogger()->log('Осуществлен вход через терминал, началось обновление данных_______________________________');
    $query = new QueryMode();
    $genresList = $query->getGenres();
    $moviesList = $query->getMovies();

    $movie = new ListMode();
    $movies = $movie->getMovieByFilters();
    if ($movies) {
        foreach ($movies as $data) {
            echo "-----------------------------------------------------------------\n";
            echo $data['title'] . " (" . $data['original_title'] . ")\n" .$data['genres'] .
                "\n" .$data['release_date'] . "\n" .$data['overview'] . "\n";
        }
    }
}






