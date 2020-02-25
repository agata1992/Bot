<?php

require_once 'vendor/autoload.php';

use Mpociot\BotMan\Botman;
use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\DriverManager;
use Mpociot\BotMan\Question;
use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Cache\DoctrineCache;
use Mpociot\BotMan\Button;

$config = [
    'facebook_token' => 'AALPuBTfkooBADoEZAsLgzbxc3vol8btIHr2ZBFSWZA2lG7AZC76JvbwMnqbwvl7KzBxSZBZBUHa3zKLh1BVCP4zeYEbL65cV4DJybqvUGxJcVt768ZCPAh0WLYvSWjGYEX7A0f6XCbhdIINDhFEQAgGwJuEFZCzHOomYzIRZBnTk5IhwKm1koAqeYcvvSKXNhHsZD',
    'facebook_app_secret' => '1bbb590ce1d311c20bdf66b870b9867'
];

function ask_age($title,$bot){ 
	$error = 0;
	$bot->ask($title,function(Answer $answer, $bot){
		$age = $answer->getText();
	
		$error = 0;
		if(is_numeric($age)){
			if(!($age >= 13 && $age <= 100))
				$error =1;
		}
		else
			$error = 1;
	
		if($error == 1)
			ask_age('Proszę o podanie wieku w zakresie 13 do 100 lat',$bot);
		else{
			$current_year = date('Y'); 
			$birth = $current_year - $age;
	
			$question = Question::create('Dziękuje.Twój rok urodzenia to '.$birth.'?')
			->addButtons([
				Button::create('Tak')->value('yes'),
				Button::create('Nie')->value('no'),
			]);
		
			$bot->ask($question, function (Answer $answer,$bot) {
		
				if($answer->getValue() == 'yes')
					$bot->say('Świetnie.Dziękuje za odpowiedź.');
				else
					ask_age('Ile masz lat?',$bot);
			});
		}
	});
	}

$doctrineCacheDriver = new \Doctrine\Common\Cache\PhpFileCache('cache');
$botman = BotManFactory::create($config,new DoctrineCache($doctrineCacheDriver));
$botman->verifyServices('123abcd');

$botman->hears('.*.', function(Botman $bot) {
    $bot->reply('Cześć '.$bot->getUser()->getFirstName().'!');
	ask_age('Ile masz lat?',$bot);
});

$botman->listen();

?>