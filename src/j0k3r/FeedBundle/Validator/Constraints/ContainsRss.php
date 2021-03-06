<?php

namespace j0k3r\FeedBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsRss extends Constraint
{
    public $message = 'Feed "%string%" is not valid.';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
