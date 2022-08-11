<?php

use App\Scrap;

require 'vendor/autoload.php';

$url = 'https://ieltsliz.com/100-ielts-essay-questions/';
//(new Scrap($url))->run();
//(new \App\FixFormat())->handle();
(new \App\FixFormat())->makeMarkdownFile();