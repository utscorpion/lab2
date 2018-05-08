<?php
/**
 * Created by PhpStorm.
 * User: utscorpion
 * Date: 5/6/18
 * Time: 3:05 PM
 */
ini_set('display_errors', 1);
include_once 'app/ListMode.php';
if(!isset($argv)) {
    include_once 'templates/main.php';
} else {
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






