<?php

namespace j0k3r\FeedBundle\Formatter;

/**
 * Atom formatter
 *
 * This class provides an Atom formatter
 */
class AtomFormatter extends Formatter
{
    /**
     * @see parent
     */
    public function setItemFields()
    {
        $this->fields = array(
            array(
                'name'        => 'id',
                'method'      => 'getLink',
            ), array(
                'name'        => 'title',
                'method'      => 'getTitle',
                'cdata'       => false,
            ), array(
                'name'        => 'summary',
                'method'      => 'getContent',
                'cdata'       => true,
            ), array(
                'name'        => 'link',
                'method'      => 'getLink',
                'attribute'   => 'href'
            ), array(
                'name'        => 'updated',
                'method'      => 'getPubDate',
                'date_format' => \DateTime::ATOM,
            ),
        );
    }

    /**
     * @see parent
     */
    public function initialize()
    {
        $this->dom = new \DOMDocument('1.0', 'utf-8');

        $root = $this->dom->createElement('feed');
        $root->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $root = $this->dom->appendChild($root);

        $identifier = $this->dom->createElement('id', 'http://'.$this->feed->getHost().'/');
        $title      = $this->dom->createElement('title', htmlspecialchars($this->feed->getName()));
        $subtitle   = $this->dom->createElement('subtitle', htmlspecialchars($this->feed->getDescription()));
        $name       = $this->dom->createElement('name', htmlspecialchars($this->feed->getName()));
        $generator  = $this->dom->createElement('generator', htmlspecialchars($this->generator));

        $self = $this->dom->createElement('link');
        $self->setAttribute('href', $this->url);
        $self->setAttribute('rel', 'self');

        $link = $this->dom->createElement('link');
        $link->setAttribute('href', 'http://'.$this->feed->getHost());

        $hub = $this->dom->createElement('link');
        $hub->setAttribute('href', 'http://pubsubhubbub.appspot.com/');
        $hub->setAttribute('rel', 'hub');

        $date = new \DateTime();
        $updated = $this->dom->createElement('updated', $date->format(\DateTime::ATOM));

        $author = $this->dom->createElement('author');
        $author->appendChild($name);

        $root->appendChild($hub);
        $root->appendChild($self);
        $root->appendChild($title);
        $root->appendChild($subtitle);
        $root->appendChild($link);
        $root->appendChild($updated);
        $root->appendChild($identifier);
        $root->appendChild($author);
        $root->appendChild($generator);

        foreach ($this->items as $item) {
            $this->addItem($root, $item, 'entry');
        }
    }
}
