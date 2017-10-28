<?php
/**
 * Created by PhpStorm.
 * User: sanya
 * Date: 28.10.17
 * Time: 12:38
 */
namespace Netpeak;

class Parser
{
    private $url;
    private $domain;
    private $protocol;
    private $file;
    private $opts;
    public $links;

    function __construct(string $url)
    {
        $this->url = $url;
        $this->domain = parse_url($url,PHP_URL_HOST);
        #TODO check if url without 'http://'
        $this->protocol = parse_url($url, PHP_URL_SCHEME);
        $this->opts = stream_context_create(array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                    "Cookie: foo=bar\r\n"
            )
        ));
    }

    public function parse(): string
    {
        if($content = $this->getContent($this->url)) {
            $this->file = fopen("{$this->domain}.csv", "w");

            #TODO format base url
            $this->parseImages($content, $this->url);
            $this->parseSubUrls($content);

            fclose($this->file);
            return "{$this->domain}.csv";
        } else {
            return "Failed to parse url : {$this->url}";
        }
    }

    private function parseSubUrls(string $content)
    {
        preg_match_all('/(href)=("\/[a-zA-Z^"?]*")/i', $content, $links, PREG_SET_ORDER);

        if(!empty($links)) {
            foreach ($links as $link) {
                $subPage = trim($link[2], '"');
                $content = $this->getContent("{$this->protocol}://{$this->domain}{$subPage}");
                $this->parseImages($content, $this->domain.$subPage);

                #TODO create recursion with mapping parsed urls and exit
            }
        }
    }

    private function parseImages(string $content, string $domain)
    {
        preg_match_all('/<img[^>]+>/i', $content, $result);

        foreach($result[0] as $img_tag) {
            preg_match_all('/(src)=("[^"]*")/i',$img_tag, $img, PREG_SET_ORDER);
            $this->saveImageUrl($domain, $img);
        }
    }

    private function saveImageUrl(string $domain, array $images)
    {
        foreach ($images as $image) {
            if(!empty($image)) {
                fputcsv($this->file, [$domain, trim($image[2],'"')], ";");
            }
        }
    }

    private function getContent(string $url): string
    {
        return file_get_contents($url, false, $this->opts);
    }


//1. Команда parse - запускает парсер, принимает обязательный параметр url (как с протоколом, так и без).
//1.1. При переходе по переданному url, приложение должно найти все картинки, расположенные на странице,
// и сохранить их полные пути и страницу источник.
//1.2. На анализируемой странице должны быть найдены все ссылки, ведущие на другие страницы данного домена,
// для каждой из этих страниц должен быть выполнен пункт 1.1. и 1.2.
//1.3. В конце выполнения команда должна выдать ссылку на CSV файл с результатами анализа.
}