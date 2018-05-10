# App for request TMBD and render list of movies
This is my second lab at Godel Technologies
## Geting started
When you clone repository, create file "config.php"(example you can find in repository as 'config.example.php'). 
In "config.php", please, use constants for normal work:

## Config.php
API_PATH = 'https://api.themoviedb.org/3'

API_KEY = 'your tmdb api key'

REGION = 'Ru' 

LANGUAGE = 'ru'

DB_PATH = 'db/'

DAYS = 7

POSTERS_PATH = 'public/images/posters'

LOG_PATH = 'log/'

##Discription
Querymode works only in CLI.
Listmode works in CLI and WEB

For execute in WEB you must to have Web server My website is accessible by http://mastery.loc address
As a default List mode consider release date 1 week ago from now to today

For execute in CLI you must open "terminal", then open directory lab2, then in "terminal" run "php index.php".

## Versions 2.0
2.0 Added classs Logger, added Interface, optimazed classes. Querymode works only in CLI
1.0 not a final version to be revision....

## Author
Made by D.Hrynko
