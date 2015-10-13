<?php

namespace Gitiki\Git\Gitonomy;

use Gitonomy\Git\Commit as BaseCommit,
    Gitonomy\Git\Diff\Diff;

class Commit extends BaseCommit
{
    /**
     * @return string
     */
    public function getHashAuthorEmail()
    {
        return md5($this->getAuthorEmail());
    }

    /**
     * @param string $file Path to file
     *
     * @return Diff
     */
    public function getDiffFile($file)
    {
        $args = [
            '-r', '-p', '-m', '-M', '--no-commit-id', '--full-index', $this->revision,
            '--', $this->repository->getWikiDir().$file,
        ];

        $diff = Diff::parse($this->repository->run('diff-tree', $args));
        $diff->setRepository($this->repository);

        return $diff;
    }
}
