<?php

namespace j0k3r\FeedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * FeedLog controller.
 */
class FeedLogController extends Controller
{
    /**
     * Lists all FeedLog documents.
     *
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $dm       = $this->getDocumentManager();
        $feedlogs = $dm->getRepository('j0k3rFeedBundle:FeedLog')->findAllOrderedById(100);

        return array(
            'menu'     => 'log',
            'feedlogs' => $feedlogs,
        );
    }

    /**
     * Lists all FeedLog documents related to a given feed
     *
     * @Template()
     *
     * @return array
     */
    public function feedAction($slug)
    {
        $dm   = $this->getDocumentManager();
        $feed = $dm->getRepository('j0k3rFeedBundle:Feed')->findOneBySlug($slug);

        if (!$feed) {
            throw $this->createNotFoundException('Unable to find Feed document.');
        }

        $feedlogs = $dm->getRepository('j0k3rFeedBundle:FeedLog')->findByFeedId($feed->getId());

        $deleteAllForm = $this->createDeleteAllForm($feed->getSlug());

        return array(
            'menu'            => 'log',
            'feed'            => $feed,
            'feedlogs'        => $feedlogs,
            'delete_all_form' => $deleteAllForm->createView(),
        );
    }

    public function deleteAllAction(Request $request, $slug)
    {
        $dm   = $this->getDocumentManager();
        $feed = $dm->getRepository('j0k3rFeedBundle:Feed')->findOneBySlug($slug);

        if (!$feed) {
            throw $this->createNotFoundException('Unable to find Feed document.');
        }

        $form = $this->createDeleteAllForm($slug);
        $form->bind($request);

        if ($form->isValid()) {
            $res = $dm->getRepository('j0k3rFeedBundle:FeedLog')->deleteAllByFeedId($feed->getId());

            $this->get('session')->getFlashBag()->add('notice', $res['n'].' documents deleted!');
        }

        return $this->redirect($this->generateUrl('feed_edit', array('slug' => $slug)));
    }

    private function createDeleteAllForm($slug)
    {
        return $this->createFormBuilder(array('slug' => $slug))
            ->add('slug', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Returns the DocumentManager
     *
     * @return DocumentManager
     */
    private function getDocumentManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
    }
}
