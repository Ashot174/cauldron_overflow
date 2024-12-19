<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sentry\State\HubInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class QuestionController extends AbstractController
{
    private LoggerInterface $logger;
    private bool $isDebug;

    public function __construct(LoggerInterface $logger, bool $isDebug)
    {

        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }

    /**
     * @Route("/{page<\d+>}", name="app_homepage")
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function homepage(
        Environment $twigEnvironment,
        EntityManagerInterface $entityManager,
        HttpClientInterface $httpClient,
        Request $request, int $page = 1
    ): Response
    {

//        $response = $httpClient->request('GET', 'http://127.0.0.1:8000/api/administrative-district/list', [
//            'headers' => [
//                'Origin' => 'https://example.com', // Custom Origin header
//                'Referer' => 'https://example.com/page', // Custom Referer header
//            ],
//        ])->toArray();
//        dd($response);
        throw new RuntimeException();
        $repository = $entityManager->getRepository(Question::class);
        $queryBuilder = $repository->createAskedOrderByNewestQueryBuilder();

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );

        $pagerfanta->setMaxPerPage(5);
        //$pagerfanta->setCurrentPage($request->query->get('page', 1));
        $pagerfanta->setCurrentPage($page);

        $html = $twigEnvironment->render('question/homepage.html.twig', ['pager' => $pagerfanta]);

        return new Response($html);
        //return $this->render('question/homepage.html.twig');
    }

    /**
     * @Route("/questions/new") void
     * @IsGranted("ROLE_USER")
     * @throws \Exception
     */
    public function new(EntityManagerInterface $entityManager)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');

//        if (!$this->isGranted('ROLE_ADMIN')) {
//            throw $this->createAccessDeniedException('No access for you');
//        }

        return new Response('Sounds like a great feature for V2');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     * @param Question $question
     * @return Response
     */
    public function show(Question $question): Response
    {
        if ($this->isDebug) {
            $this->logger->info('we are in debug mode');
        }

        //dump($this->getParameter('cache_adapter'));
        //throw new \Exception('bad stuff happened!');

        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }

    /**
     * @Route("/question/{slug}/vote", name="app_question_vote", methods="POST")
     */
    public function questionVote(Question $question, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $direction = $request->request->get('direction');

        if ($direction === 'up') {
            $question->upVote();
        } elseif ($direction === 'down') {
            $question->downVote();
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_question_show', ['slug' => $question->getSlug()]);
    }
}