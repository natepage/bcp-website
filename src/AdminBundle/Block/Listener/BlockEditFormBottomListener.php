<?php

namespace AdminBundle\Block\Listener;

use Sonata\BlockBundle\Model\Block;
use Sonata\BlockBundle\Event\BlockEvent;

class BlockEditFormBottomListener
{
    public function onBlock(BlockEvent $blockEvent)
    {
        $block = new Block();
        $block->setId(uniqid()); // set a fake id
        $block->setSettings($blockEvent->getSettings());
        $block->setType('admin.form_javascripts.block');

        $blockEvent->addBlock($block);
    }
}