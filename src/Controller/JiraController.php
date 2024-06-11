<?php

namespace App\Controller;

use GuzzleHttp\Exception\GuzzleException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\JiraService;

class JiraController extends AbstractController
{
    private $jiraService;
    private PaginatorInterface $paginator;

    public function __construct(JiraService $jiraService, PaginatorInterface $paginator)
    {
        $this->jiraService = $jiraService;
        $this->paginator = $paginator;
    }


    #[Route('/jira/issue/show', name: 'jira_issue_show')]
    public function showIssues(Request $request): Response
    {
        $data = $this->jiraService->getUserIssues($this->getUser()->getEmail());

        $pagination = $this->paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('jira/show.html.twig', [
            'pagination' => $pagination,
            'issue_url' => 'https://bondarkov.atlassian.net/browse/'
        ]);
    }


    /**
     * @throws GuzzleException
     */
    #[Route('/jira/ticket', name: 'jira_ticket')]
    public function createTicket(Request $request): Response
    {
        $user = $this->jiraService->getUserByEmail($this->getUser()->getEmail());

        if(!$user)
        {
            $user = $this->jiraService->createUser();
        }

        $url = $request->server->get('HTTP_REFERER');
        $collection = $request->request->get('collection');

        $response = $this->jiraService->createTicket($request->request->all(), $url, $collection, $user);
        $key = $this->jiraService->getJiraKey($response);
        $link = $this->jiraService->generateLink($key);

        $this->addFlash('success', $link);
        return $this->redirectToRoute('jira_issue_show');
    }

}