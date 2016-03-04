<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SharedFile;
use AppBundle\Form\SharedFileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;

/**
 * @Route("/shared_files")
 */
class SharedFileController extends Controller
{
    const DIRECTORY_NAME = "/../web/shared_files";
    const DIRECTORY_MAX_SIZE_NUMBER = 500;
    const DIRECTORY_MAX_SIZE_TEXT = '500 Mo';

    /**
     * @Route("/create", name="admin_shared_file_create")
     * @Security("has_role('ROLE_SHARED_FILE_CREATE')")
     */
    public function createAction(Request $request)
    {
        $sharedFile = new SharedFile();
        $form = $this->createForm(new SharedFileType(), $sharedFile);
        $from = $this->getUser()->getEmail();

        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();

            $sharedFile->setFrom($this->getUser()->getEmail());
            $sharedFile->setToken($this->get('fos_user.util.token_generator')->generateToken());

            $em->persist($sharedFile);
            $em->flush();

            $flash = sprintf("Le fichier partagé \"%s\"a bien été créé.", $sharedFile->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'sharedFile')));
        }

        return $this->render('@App/Admin/SharedFile/create.html.twig', array(
            'form' => $form->createView(),
            'sharedFile' => $sharedFile
        ));
    }

    /**
     * @Route("/update/{id}", name="admin_shared_file_update", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_SHARED_FILE_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $sharedFile = $em->getRepository('AppBundle:SharedFile')->find($id)){
            throw $this->createNotFoundException(sprintf("Le fichier partagé à l'id %s n'existe pas.", $id));
        }

        $form = $this->createForm(new SharedFileType(), $sharedFile);

        if($form->handleRequest($request)->isValid()){
            $em = $this->getDoctrine()->getManager();

            $em->persist($sharedFile);
            $em->flush();

            $flash = sprintf("Le fichier partagé \"%s\"a bien été modifié.", $sharedFile->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'sharedFile')));
        }

        return $this->render('@App/Admin/SharedFile/update.html.twig', array(
            'form' => $form->createView(),
            'sharedFile' => $sharedFile
        ));
    }

    /**
     * @Route("/remove/{id}", name="admin_shared_file_remove", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_SHARED_FILE_REMOVE')")
     */
    public function removeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(null === $sharedFile = $em->getRepository('AppBundle:SharedFile')->find($id)){
            throw $this->createNotFoundException(sprintf("Le fichier partagé à l'id %s n'existe pas.", $id));
        }

        $form = $this->createFormBuilder()->getForm();

        if($form->handleRequest($request)->isValid()){
            $em->remove($sharedFile);
            $em->flush();

            $flash = sprintf("Le fichier partagé \"%s\"a bien été supprimé.", $sharedFile->getTitle());
            $request->getSession()->getFlashBag()->add('success', $flash);

            return $this->redirect($this->generateUrl('admin_entity_list', array('entity' => 'sharedFile')));
        }

        return $this->render('@App/Admin/SharedFile/remove.html.twig', array(
            'form' => $form->createView(),
            'sharedFile' => $sharedFile
        ));
    }

    /**
     * @Security("has_role('ROLE_SHARED_FILE_SEE')")
     */
    public function quotaAction()
    {
        $path = $this->get('kernel')->getRootDir() . self::DIRECTORY_NAME;
        $exception = null;
        $fs = new Filesystem();

        if(!$fs->exists($path)) {
            try {
                $fs->mkdir($path);
            } catch (IOExceptionInterface $e) {
                $exception = "An error occurred while creating your directory at ".$e->getPath();
            }
        }

        $bytes = $exception !== null ? 0 : $this->getDirectorySize($path);
        $sizeInMo = $this->formatSize($bytes, 'Mo');
        $percent = $this->getUsePercentage($sizeInMo);
        $class = $this->getProbressBarClass($percent);

        return $this->render('@App/Admin/SharedFile/quota.html.twig', array(
            'exception' => $exception,
            'sizeInMo' => $sizeInMo,
            'formattedSize' => $this->formatSize($bytes, 'auto', true),
            'maxSizeText' => self::DIRECTORY_MAX_SIZE_TEXT,
            'maxSizeNumber' => self::DIRECTORY_MAX_SIZE_NUMBER,
            'percent' => $percent,
            'class' => $class
        ));
    }

    /**
     * Get directory size in bytes.
     *
     * @param $path
     * @param int $size
     * @return float|int
     */
    private function getDirectorySize($path, $size = 0)
    {
        $finder = new Finder();

        foreach($finder->files()->in($path) as $file){
            $size += $this->getFileSize($path . '/' . $file->getRelativePathname());
        }

        return $size;
    }

    /**
     * Get file size in bytes.
     *
     * @param $size
     * @param int $precision
     * @return float
     */
    private function getFileSize($filename)
    {
        return filesize($filename);
    }

    /**
     * Get use percentage of shared files directory.
     * The size parameter must be in Mo.
     *
     * @param $size
     * @return float
     */
    private function getUsePercentage($size)
    {
        return round(($size * 100)/self::DIRECTORY_MAX_SIZE_NUMBER, 0);
    }

    /**
     * Get progressbar class.
     *
     * @param $percent
     * @return string
     */
    private function getProbressBarClass($percent)
    {
        $class = 'success';

        if($percent >= 60 && $percent <= 80){
            $class = 'warning';
        } elseif($percent > 80) {
            $class = 'danger';
        }

        return $class;
    }

    /**
     * Get formatted size.
     *
     * @param $bytes
     * @param string $format
     * @param bool|false $suffixed
     * @return float|int|string
     */
    private function formatSize($bytes, $format = 'auto', $suffixed = false)
    {
        $formattedSize = 0;
        $suffix = '';
        $formats = array(
            'Go' => 1073741824,
            'Mo' => 1048576,
            'Ko' => 1024,
            'o' => 1
        );

        if($format != 'auto' && array_key_exists($format, $formats)){
            $formattedSize = round($bytes / $formats[$format] * 100) / 100;
            $suffix = $format;
        } else {
            foreach($formats as $k => $v){
                if($bytes >= $v){
                    $formattedSize = round($bytes / $v * 100) / 100;
                    $suffix = $k;
                    break;
                }
            }
        }

        return $suffixed ? $formattedSize . ' ' . $suffix : $formattedSize;
    }
}