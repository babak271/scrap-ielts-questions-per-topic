<?php

namespace App;

use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;

class Scrap
{
    /**
     * @var string
     */
    private $fileDir;
    /**
     * @var string
     */
    private $page;
    /**
     * @var string
     */
    private $addressFile;
    private string $questions;
    /**
     * @var string
     */
    private $pageUrl;
    private string $dummyPage;

    public function __construct(string $url)
    {
        $this->fileDir     = __DIR__ . '/../Files';
        $this->page        = $this->fileDir . '/page.html';
        $this->dummyPage   = $this->fileDir . '/dummyPage.html';
        $this->addressFile = $this->fileDir . '/addressFile.json';
        $this->questions   = $this->fileDir . '/questions.json';
        $this->pageUrl     = $url;
    }

    public function run()
    {
        $pageContent = $this->getPage();
        $this->getAddresses($pageContent);
        $this->getQuestions();
        echo 'done' . PHP_EOL;
    }

    protected function getPage(): bool|string
    {
        if (empty(file_get_contents($this->page))) {
            file_put_contents($this->page, file_get_contents($this->pageUrl));
        }
        return file_get_contents($this->page);
    }

    protected function getAddresses($html): bool|string
    {
        if (!empty($file = file_get_contents($this->addressFile))) {
            return $file;
        }
        $crawler   = new Crawler($html);
        $addresses = [];
        $crawler->filterXPath((new CssSelectorConverter())->toXPath('.entry-content>ul>li>a'))
            ->reduce(function (Crawler $node) use (&$addresses) {
                $addresses[$node->html()] = $node->attr('href');
            });
        $content = json_encode($addresses);
        file_put_contents($this->addressFile, $content);
        return $content;
    }

    protected function getQuestions()
    {
        $questions = [];
        foreach (json_decode(file_get_contents($this->addressFile), true) as $address) {
            $contents = file_get_contents($address);

            $title = (new Crawler($contents))
                ->filterXPath((new CssSelectorConverter())->toXPath('h1.entry-title'))
                ->first()
                ->html();
            (new Crawler($contents))
                ->filterXPath((new CssSelectorConverter())->toXPath('.entry-content > blockquote:not(:last-child)'))
                ->reduce(function (Crawler $node) use (&$questions, $title) {
                    $questions[$title][] = $node->html();
                });
        }

        $content = json_encode($questions);
        file_put_contents($this->questions, $content);
        return $content;
    }
}