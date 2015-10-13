<?php

namespace Gitiki\Git\Controller;

use Gitiki\Gitiki,
    Gitiki\Page;

use Gitonomy\Git\Exception\ProcessException;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

class DiffController
{
    public function historyAction(Gitiki $gitiki, $path)
    {
        return $gitiki['twig']->render('history.html.twig', [
            'page' => new Page($path),
            'commits' => $gitiki['git.repository']->getLog('--all', $path)->getCommits(),
        ]);
    }

    public function diffAction(Gitiki $gitiki, Request $request, $path)
    {
        $commitNum = $request->query->get('history');
        $commit = $gitiki['git.repository']->getCommit($commitNum);

        try {
            $fileDiff = $commit->getDiffFile($path)->getFiles();
        } catch (ProcessException $e) {
            $gitiki->abort(404, sprintf('The commit "%s" was not found', $commitNum));
        }

        if (empty($fileDiff)) {
            $gitiki->abort(404, sprintf('The commit "%s" does not concern the file "%s"', $commitNum, $path));
        }

        return $gitiki['twig']->render('diff.html.twig', [
            'page' => new Page($path),
            'commit' => $commit,
            'diff' => $fileDiff[0],
        ]);
    }

    public function sourceAction(Gitiki $gitiki, Request $request, $path)
    {
        return new Response($gitiki['git.repository']->getFile($path, $request->query->get('history')), 200, [
            'content-type' => 'text/plain',
        ]);
    }
}
