<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\AdoptionCase;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AdoptionCaseVoter extends Voter
{
    public function __construct(
        private Security $security
    )
    {
        
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [AdoptionCase::EDIT, AdoptionCase::VIEW, AdoptionCase::DELETE])
            && $subject instanceof \App\Entity\AdoptionCase;
    }

    /**
     * @param AdoptionCase $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        // if (!$user instanceof UserInterface) {
        //     return false;
        // }
        $isAuth = $user instanceof UserInterface;
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case AdoptionCase::EDIT:
                foreach($subject->getEmployee() as $employee)
                {
                    if($employee->getId() == $user->getId())
                    {
                        $hisCase = true;
                        break;
                    }
                    else
                    {
                        $hisCase = false;
                    }
                }
                return ($isAuth && $this->security->isGranted('ROLE_ADMIN'))
                ||($isAuth && $this->security->isGranted('ROLE_PRACOWNIK')&& $hisCase == true);

            case AdoptionCase::VIEW:
                foreach($subject->getEmployee() as $employee)
                {
                    if($employee->getId() == $user->getId())
                    {
                        $hisCase = true;
                        break;
                    }
                    else
                    {
                        $hisCase = false;
                    }
                }
                return ($isAuth && $this->security->isGranted('ROLE_ADMIN'))
                ||($isAuth && $this->security->isGranted('ROLE_PRACOWNIK')&& $hisCase == true)
                ||($isAuth && $this->security->isGranted('ROLE_CLIENT')&& $subject->getClient()==$user);

                case AdoptionCase::DELETE:
                    foreach($subject->getEmployee() as $employee)
                    {
                        if($employee->getId() == $user->getId())
                        {
                            $hisCase = true;
                            break;
                        }
                        else
                        {
                            $hisCase = false;
                        }
                    }
                    return ($isAuth && $this->security->isGranted('ROLE_ADMIN'))
                    ||($isAuth && $this->security->isGranted('ROLE_PRACOWNIK')&& $hisCase == true);
        }

        return false;
    }
}
