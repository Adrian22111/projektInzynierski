<?php

namespace App\Security\Voter;

use Exception;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
    public function __construct(private Security $security)
    {
        
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [User::EDIT, User::VIEW, User::CHANGE_PASSWORD])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        $isAuth = $user instanceof UserInterface;
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case User::EDIT:
                if($this->security->isGranted('ROLE_ADMIN'))
                {
                    $roles = $subject->getRoles();
                    foreach($roles as $role)
                    {
                        if( $role == 'ROLE_ADMIN')
                        {
                            if($user->getId() == $subject->getId())
                            {
                                return true;
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                    return true;
                }
                return ($isAuth && $this->security->isGranted('ROLE_PRACOWNIK') && $user->getId() == $subject->getId());
                        // ||($isAuth && $this->security->isGranted('ROLE_ADMIN' $$ $user));
            case User::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                return true;
            case User::CHANGE_PASSWORD:
                if($this->security->isGranted('ROLE_ADMIN'))
                {
                    $roles = $subject->getRoles();
                    foreach($roles as $role)
                    {
                        if( $role == 'ROLE_ADMIN')
                        {
                            if($user->getId() == $subject->getId())
                            {
                                return true;
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                    return true;
                }
                return ($isAuth && $this->security->isGranted('ROLE_PRACOWNIK') && $user->getId() == $subject->getId())||
                       ($isAuth && $this->security->isGranted('ROLE_CLIENT')&& $user->getId() == $subject->getId());
        }

        return false;
    }
}
