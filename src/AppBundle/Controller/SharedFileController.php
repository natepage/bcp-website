<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;

class SharedFileController extends Controller
{
    const DIRECTORY_NAME = "/../web/shared_files";
    const DIRECTORY_MAX_SIZE = 10000;

    /**
     * @Route("/create", name="admin_shared_file_create")
     * @Security("has_role('ROLE_SHARED_FILE_CREATE')")
     */
    public function createAction(Request $request)
    {

    }

    /**
     * @Route("/update", name="admin_shared_file_update")
     * @Security("has_role('ROLE_SHARED_FILE_UPDATE')")
     */
    public function updateAction(Request $request)
    {

    }

    /**
     * @Route("/remove", name="admin_shared_file_remove")
     * @Security("has_role('ROLE_SHARED_FILE_REMOVE')")
     */
    public function removeAction(Request $request)
    {

    }

    /**
     * @Security("has_role('ROLE_SHARED_FILE_SEE')")
     */
    public function quotaAction()
    {
        $path = $this->get('kernel')->getRootDir() . self::DIRECTORY_NAME;
        $exception = null;
        $fs = new Filesystem();
        $finder = new Finder();
        $size = 0;
        $class = 'success';

        if(!$fs->exists($path)) {
            try {
                $fs->mkdir($path);
            } catch (IOExceptionInterface $e) {
                $exception = "An error occurred while creating your directory at ".$e->getPath();
            }
        }

        if($exception === null){
            foreach($finder->files()->in($path) as $file){
                $size += $this->getFileSize($path . '/' . $file->getRelativePathname());
            }
        }

        $percent = $this->getPercent($size);

        if($percent >= 60 && $percent <= 80){
            $class = 'warning';
        } elseif($percent > 80) {
            $class = 'danger';
        }

        return $this->render('@App/Admin/SharedFile/quota.html.twig', array(
            'exception' => $exception,
            'size' => $size,
            'maxSize' => self::DIRECTORY_MAX_SIZE,
            'percent' => $percent,
            'class' => $class
        ));
    }

    /**
     * Format file size in GB.
     *
     * @param $size
     * @param int $precision
     * @return float
     */
    private function getFileSize($filename)
    {
        $sizeInGb = (filesize($filename) * .0009765625) * .0009765625; // bytes to MB

        return $sizeInGb >= 1 ? round($sizeInGb, 1) : 0;
    }

    private function getPercent($size)
    {
        return round(($size * 100)/self::DIRECTORY_MAX_SIZE, 0);
    }
}