<?php

final class PhabricatorRepositoryMercurialCommitMessageParserWorker
  extends PhabricatorRepositoryCommitMessageParserWorker {

  public function parseCommit(
    PhabricatorRepository $repository,
    PhabricatorRepositoryCommit $commit) {

    $ref = id(new DiffusionLowLevelCommitQuery())
      ->setRepository($repository)
      ->withIdentifier($commit->getCommitIdentifier())
      ->execute();

    $this->updateCommitData($ref);

    if ($this->shouldQueueFollowupTasks()) {
      PhabricatorWorker::scheduleTask(
        'PhabricatorRepositoryMercurialCommitChangeParserWorker',
        array(
          'commitID' => $commit->getID(),
        ));
    }
  }

}
