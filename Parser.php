<?php

namespace Netpeak;

/**
 * Class Parser
 * @package Netpeak
 */
class Parser
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var mixed
     */
    private $domain;

    /**
     * @var mixed|string
     */
    private $protocol;

    /**
     * @var resource
     */
    private $file;

    /**
     * @var array
     */
    private $opts;

    /**
     * @var array
     */
    public $links;

    /**
     * Parser constructor.
     * @param string $url
     */
    function __construct(string $url)
    {
        $this->url = $url;
        $this->domain = parse_url($url,PHP_URL_HOST);
        $this->protocol = parse_url($url, PHP_URL_SCHEME) ?? "http";
        $this->opts = stream_context_create([
            'http'=>[
                'method'=>"GET",
                'header'=> implode('\r\n',["Accept-language: en", "Cookie: foo=bar"])
            ]
        ]);
    }

    /**
     * @return string
     */
    public function parse(): string
    {
        if($content = $this->getContent($this->url)) {
            $this->file = fopen("reports/{$this->domain}.csv", "w");

            #TODO format base url
            $this->parseImages($content, $this->url);
            $this->parseSubUrls($content);

            fclose($this->file);
            return "reports/{$this->domain}.csv";
        } else {
            return "Failed to parse url : {$this->url} " . PHP_EOL;
        }
    }

    /**
     * @param string $content
     */
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

    /**
     * @param string $content
     * @param string $domain
     */
    private function parseImages(string $content, string $domain)
    {
        preg_match_all('/<img[^>]+>/i', $content, $result);

        foreach($result[0] as $img_tag) {
            preg_match_all('/(src)=("[^"]*")/i',$img_tag, $img, PREG_SET_ORDER);
            $this->saveImageUrl($domain, $img);
        }
    }

    /**
     * @param string $domain
     * @param array $images
     */
    private function saveImageUrl(string $domain, array $images)
    {
        foreach ($images as $image) {
            if(!empty($image)) {
                fputcsv($this->file, [$domain, trim($image[2],'"')], ";");
            }
        }
    }

    /**
     * @param string $url
     * @return string
     */
    private function getContent(string $url): string
    {
        return file_get_contents($url, false, $this->opts);
    }

}