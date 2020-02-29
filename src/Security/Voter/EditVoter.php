<?php

namespace App\Security\Voter;

use App\Entity\Internship;
use App\Entity\User;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EditVoter extends Voter
{
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT]))
        {
            return false;
        }

        if (!$subject instanceof Internship)
        {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User)
        {
            return false;
        }

        return $user === $subject->getAuthor();
    }
}