<?php
/**
 * Created by PhpStorm.
 * User: utscorpion
 * Date: 5/6/18
 * Time: 3:05 PM
 */
ini_set('display_errors', 1);
include_once 'app/Movie.php';
if(!isset($argv)) {
    include_once 'templates/main.php';
} else {
    $errorMassage = "Error: Файл должен быть запущен с количеством дней (php index.php -days:7)\n";
    if($argc <= 1) {
        echo $errorMassage;
    } else {
        $dayInfo = explode(':', $argv[1]);
        if($dayInfo[0] == '-days') {
            $days = $dayInfo[1];
            $movie = new Movie();
            $movies = $movie->getMovieByFilters($days);
            if ($movies) {
                foreach ($movies as $data) {
                    echo "-----------------------------------------------------------------\n";
                    echo $data['title'] . " (" . $data['original_title'] . ")\n" .$data['genres'] .
                        "\n" .$data['release_date'] . "\n" .$data['overview'] . "\n";
                }
            }
        } else {
            echo $errorMassage;
        }
    }
}






