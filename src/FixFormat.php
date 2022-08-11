<?php

namespace App;

class FixFormat
{
    private string $questions;
    private string $mdFile;

    public function __construct()
    {
        $this->questions = __DIR__ . '/../Files/questions.json';
        $this->mdFile    = __DIR__ . '/../Files/questions.md';
    }

    public function handle()
    {
        $questions = json_decode(file_get_contents($this->questions), true);
        $result    = [];
        foreach ($questions as $title => $item) {
            foreach ($item as $question) {
                $result[strip_tags($title)][] = strip_tags($question);
            }
        }
        file_put_contents($this->questions, json_encode($result));
    }

    public function makeMarkdownFile()
    {
        file_put_contents($this->mdFile, '');
        $mdFile    = fopen($this->mdFile, 'a+');
        $questions = json_decode(file_get_contents($this->questions), true);
        fwrite($mdFile, '# Questions Per Topic' . PHP_EOL);
        foreach ($questions as $title => $item) {
            fwrite($mdFile, '## ' . $title . PHP_EOL);
            foreach ($item as $question) {
                fwrite($mdFile, '- ' . $question . PHP_EOL);
            }
        }
        fclose($mdFile);
    }
}