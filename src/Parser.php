<?php

namespace App;

use DOMDocument;

class Parser
{
    private $url;
    private $trimmedUrl;
    private $urlHost;
    private $visitedUrls = [];

    public function __construct($url)
    {
        $this->url = $this->isUrlValid($url);
        $this->trimmedUrl = rtrim($this->url, '/');
        $this->urlHost = parse_url($this->url, PHP_URL_HOST);
    }

    public function start()
    {
        if ($this->url) {
            echo 'parsing this url -> ' . $this->url . "\n";
            $this->parse($this->url, 'img', 'src');
            echo "Your result you can see in this file -> " . $this->getFilePath();
        } else {
            echo "You typed incorrect url";
        }
    }

    public function report()
    {
        if (file_exists($this->getFilePath())) {
            echo "Url уже обрабатывался. Посмотрите результаты здесь - " . $this->getFilePath();
        } else {
            echo "Url еще не обрабатывался, вызовите команду parse.";
        }
    }

    private function parse($url, $searchElement, $attribute)
    {
        $this->visitedUrls[] = $url;
        $html = $this->getPage($url);
        $searchedImages = $this->find($html, $searchElement, $attribute);
        $file = new FileGenerator($this->urlHost, $url);
        $file->writeToFile($searchedImages);

        $searchedHref = $this->find($html, 'a', 'href');
        foreach ($searchedHref as $href) {
            if ($this->urlHost == parse_url($href, PHP_URL_HOST)) {
                if (!in_array($href, $this->visitedUrls)) {
                    echo $href . "\n";
                    $this->parse($href, 'img', 'src');
                }
            }
        }
    }

    private function isUrlValid($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        } else {
            return filter_var($this->makeProtocol($url), FILTER_VALIDATE_URL);
        }
    }

    private function makeProtocol($url)
    {
        return 'http://' . $url;
    }

    private function getPage($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }

    private function find($html, $searchElement, $attribute)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $arrayOfImagesSrc = [];

        foreach ($dom->getElementsByTagName($searchElement) as $link) {
            $resource = $link->getAttribute($attribute);
            if (filter_var($resource, FILTER_VALIDATE_URL)) {
                $arrayOfImagesSrc[] = $resource;
            } else {
                $arrayOfImagesSrc[] = $this->trimmedUrl . $resource;
            }
        }

        return $arrayOfImagesSrc;
    }

    private function getFilePath()
    {
        return __DIR__ . "/../files/" . $this->urlHost . '.csv';
    }
}