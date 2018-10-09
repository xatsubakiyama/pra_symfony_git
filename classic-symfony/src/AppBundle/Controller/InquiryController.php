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
        return $this->createFormBuilder(new Inquiry())
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
            $inquiry = $form->getData();   /*フォームからエンティティが取得できる*/

            $em = $this->getDoctrine()->getManager();   /*エンティティマネージャを取得*/
            $em->persist($inquiry);   /*エンティティをdoctrine管理下へ*/
            $em->flush();   /*変更分をデータベースへ適用*/

            $message = \Swift_Message::newInstance()
                ->setSubject('Webサイトからのお問い合わせ')
                ->setFrom('webmaster@example.com')
                ->setTo('k.tsubaki.2@gmail.com')
                ->setBody(
                    $this->renderView(
                        'mail/inquiry.txt.twig',
                        ['data' => $inquiry]   /*エンティティを渡す*/
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
