<?php

namespace AdminBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Response;

class FormJavascriptsBlock implements BlockServiceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var EngineInterface
     */
    private $templating;

    public function __construct($name, EngineInterface $templating)
    {
        $this->name = $name;
        $this->templating = $templating;
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response              $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->templating->renderResponse($blockContext->getTemplate(), array(), $response);
    }

    /**
     * Define the default options for the block.
     *
     * @param OptionsResolverInterface $resolver
     *
     * @deprecated since version 2.3, to be renamed in 3.0.
     *             Use the method configureSettings instead.
     *             This method will be added to the BlockServiceInterface with SonataBlockBundle 3.0.
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title' => 'Le titre du block',
            'template' => 'AdminBundle:Block:form_javascripts.html.twig'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
    }

    /**
     * @param $media
     * @return array
     */
    public function getJavascripts($media)
    {
        return array();
    }

    /**
     * @param $media
     * @return array
     */
    public function getStylesheets($media)
    {
        return array();
    }

    /**
     * @param BlockInterface $block
     * @return array
     */
    public function getCacheKeys(BlockInterface $block)
    {
        return array(
            'block_id' => $block->getId(),
            'updated_at' => $block->getUpdatedAt() ? $block->getUpdatedAt()->format('U') : strtotime('now'),
        );
    }
}