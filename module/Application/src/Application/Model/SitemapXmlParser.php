<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 25/3/2014
 * Time: 1:35 πμ
 */

namespace Application\Model;


use Post\Entity\Post;

class SitemapXmlParser
{
    /**
     * @var \XmlWriter
     */
    private $writer;

    private $openElements;

    public function __construct()
    {
        $this->writer = new \XMLWriter('UTF-8');
    }

    public function begin()
    {
        $this->writer->openMemory();
        $this->writer->setIndent(true);
        $this->writer->setIndentString(str_repeat(' ', 4));

        $this->writer->startDocument('1.0', 'UTF-8');
    }

    public function addHeader($type, $hasFeeds = false)
    {
        $this->writer->startElement($type);
        $this->writer->writeAttribute('xmlns', "http://www.sitemaps.org/schemas/sitemap/0.9");
        if ($hasFeeds) {
            $this->writer->writeAttribute('xmlns:image', "http://www.google.com/schemas/sitemap-image/1.1");
            $this->writer->writeAttribute('xmlns:video', "http://www.google.com/schemas/sitemap-video/1.1");
        }
        $this->writer->endAttribute();
        $this->openElements++;
        return $this;
    }

    public function close()
    {
        for ($i = 0; $i < $this->openElements; $i++) {
            $this->writer->endElement();
        }
        $this->openElements = 0;
        $this->writer->endDocument();
    }

    public function addSitemap($value)
    {
        $this->writer->startElement('sitemap');
        $this->writer->writeElement('loc', $value);
        $this->writer->writeElement('lastmod', date(\Datetime::ATOM, time()));
        $this->writer->endElement();
    }

    public function addUrl($value, $priority)
    {
        $this->writer->startElement('url');
        $this->writer->writeElement('loc', $value);
        $this->writer->writeElement('lastmod', date(\Datetime::ATOM, time()));
        $this->writer->writeElement('changefreq', 'monthly');
        $this->writer->writeElement('priority', $priority);
        $this->writer->endElement();
    }

    /**
     * @param \Product\Entity\Product|Post $entity
     */
    public function addEntityInfo($entity)
    {
        $this->writer->startElement('url');
        $this->writer->writeElement('lastmod', date(\Datetime::ATOM, time()));
        $this->writer->startElement('image:image');
        if ($entity instanceof Post) {
            $this->writer->writeElement('image:loc', "http://www.infolightingco.com/images/posts/" . $entity->getThumbnail());
            $this->writer->writeElement('loc', "http://www.infolightingco.com/news/" . $entity->getUrl());
        }else{
            $this->writer->writeElement('image:loc', "http://www.infolightingco.com/images/posts/" . $entity->getThumbnail());
            $this->writer->writeElement('loc', "http://www.infolightingco.com/products/view/" . $entity->getProductNumber());
        }
        $this->writer->endElement();
        $this->writer->endElement();
    }

    public function flush()
    {
        $this->writer->flush();
    }

    public function show()
    {
        echo $this->writer->outputMemory();
    }
} 