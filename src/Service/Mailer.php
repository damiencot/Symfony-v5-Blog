<?php


namespace App\Service;


use App\Entity\Article;
use Symfony\Contracts\Translation\TranslatorInterface;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    private $translator;
    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }
    public function sendNbViews(Article $article)
    {
        $message = new \Swift_Message($this->translator->trans('mailer.article.subject'),
            $this->translator->trans('mailer.article.nb_views', ['nb_views' => $article->getNbViews()])
        );
        $message->addTo('damien.cot@gmail.com')
            ->addFrom('damien.cot@gmail.com');
        $this->mailer->send($message);
    }
}