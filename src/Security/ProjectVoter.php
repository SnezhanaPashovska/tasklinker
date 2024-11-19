<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;

class ProjetVoter extends Voter
{

    public function __construct(
        private ProjectRepository $projectRepository,
        private TaskRepository $taskRepository,
    )
    {
        
    }
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'acces_project' || $attribute === 'acces_task';
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if($attribute === 'acces_project') {
            $project = $this->projectRepository->find($subject);
        } else {
            $task = $this->taskRepository->find($subject);
            $project = $task?->getProject();
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface || !$project) {
            return false;
        }

        return $user->isAdmin() || $project->getEmployes()->contains($user);
    }
}