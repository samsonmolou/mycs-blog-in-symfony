<?php

namespace AppBundle;

final class Events
{
    /**
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     * 
     * @var string
     */
    const COMMENT_CREATED = 'comment.created';
}