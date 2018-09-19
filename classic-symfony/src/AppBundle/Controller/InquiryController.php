<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Inquiry;

/**
 * @Route("/inquiry")
 */
class InquiryController extends Controller
{
    private function createInquiryForm()
    {
        return $this->createFormBuilder()
            ->add('name', 'text')
            ->add('email', 'text')
            ->add('tel', 'text', [
                'required' => false,
            ])
            ->add('type', 'choice', [
                'choices' => [
                    '公演について',
                    'その他',
                ],
                'expanded' => true,
            ])
            ->add('content', 'textarea')
            ->add('submit', 'submit', [
                'label' => '送信',
            ])
            ->getForm();
    }

    /**
     * @Route("/")
     * @Method("get")
     */
    public function indexAction()
    {
        return $this->render('Inquiry/index.html.twig',
            ['form' => $this->createInquiryForm()->createView()]
        );
    }

    /**
     * @Route("/complete")
     */
    public function completeAction()
    {
        return $this->render('Inquiry/complete.html.twig');
    }

    /**
     * @Route("/")
     * @Method("post")
     */
    public function indexPostAction(Request $request)
    {
        $form = $this->createInquiryForm();
        $form->handleRequest($request);
        if ($form->isValid())
        {
            $data = $form->getData();

            $inquiry = new Inquiry();
            $inquiry->setName($data['name']);
            $inquiry->setEmail($data['email']);
            $inquiry->setTel($data['tel']);
            $inquiry->setType($data['type']);
            $inquiry->setContent($data['content']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($inquiry);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Webサイトからのお問い合わせ')
                ->setFrom('k.tsubaki.2@gmail.com')
                ->setTo('admin@example.com')
                ->setBody(
                    $this->renderView(
                        'mail/inquiry.txt.twig',
                        ['data' => $data]
                    )
                );

            $this->get('mailer')->send($message);

            return $this->redirect(
                $this->generateUrl('app_inquiry_complete'));
        }

        return $this->render('Inquiry/index.html.twig',
            ['form' => $form->createView()]
        );
    }

}
