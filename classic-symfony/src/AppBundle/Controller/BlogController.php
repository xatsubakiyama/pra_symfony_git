<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function latestListAction()
    {
        $em = $this->getDoctrine()->getManager();   /*エンティティマネージャを取得*/
        $blogArticleRepository = $em->getRepository('AppBundle:BlogArticle');   /*エンティティマネージャからエンティティリポジトリを取得*/
        $blogList = $blogArticleRepository->findBy([], ['targetDate' => 'DESC']);   /*エンティティリポジトリのファインダメソッドを実行して情報を取得*/

        return $this->render('Blog/latestList.html.twig',
            ['blogList' => $blogList]
        );
    }
}
