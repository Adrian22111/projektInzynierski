<?php

namespace App\Security\Voter;

use App\Entity\Documents;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class DocumentsVoter extends Voter
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
        return in_array($attribute, [Documents::EDIT, Documents::VIEW, Documents::DELETE])
            && $subject instanceof \App\Entity\Documents;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // dd($user);
        // if the user is anonymous, do not grant access
        // if (!$user instanceof UserInterface) {
        //     return false;
        // }
        $isAuth = $user instanceof UserInterface;
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case Documents::EDIT:
                // logic to determine if the user can EDIT
                // return true or 
                // sprawdzić do jakiej sprawy jest przypisany dokument 
                // potem sprawdzić czy id sprawy z dokumewntem odpowiada jakiemus id sprawy przypisanej do pracownika
                if($this->security->isGranted('ROLE_ADMIN'))
                {
                    return true;
                }
                if($subject->getAdoptionCase())
                {
                    $documentAdoptionCaseId = $subject->getAdoptionCase()->getId();// id sprawy adopcji przypisanej do dokumentu
                    if($this->security->isGranted('ROLE_PRACOWNIK'))
                    {
                        $employeeAdoptionCases = $user->getEmployeeAdoptionCases();
                        foreach($employeeAdoptionCases as $employeeAdoptionCase)
                        {
                            if($employeeAdoptionCase->getId() == $documentAdoptionCaseId)
                            {
                                return true;
                                //$documentInUserCase = true;
                                // break;
                            }
                            else
                            {
                                return false;
                                //$documentInUserCase = false; 
                            }
                            
                        }  
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }

                
            case Documents::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                if($this->security->isGranted('ROLE_ADMIN'))
                {
                    return true;
                }
                if($subject->getAdoptionCase())
                {
                    $documentAdoptionCaseId = $subject->getAdoptionCase()->getId();// id sprawy adopcji przypisanej do dokumentu
                    if($this->security->isGranted('ROLE_PRACOWNIK'))
                    {
                        $employeeAdoptionCases = $user->getEmployeeAdoptionCases();
                        foreach($employeeAdoptionCases as $employeeAdoptionCase)
                        {
                            if($employeeAdoptionCase->getId() == $documentAdoptionCaseId)
                            {
                                return true;
                                //$documentInUserCase = true;
                                // break;
                            }
                            else
                            {
                                return false;
                                //$documentInUserCase = false; 
                            }
                            
                        }
                        
                    }
                    elseif($this->security->isGranted('ROLE_CLIENT'))
                    {
                        $clientAdoptionCases = $user->getClientAdoptionCases();
                        if($clientAdoptionCases->getId() == $documentAdoptionCaseId)
                        {
                            // $documentInUserCase = true;
                            return true;
                            // ($isAuth && $this->security->isGranted('ROLE_ADMIN'))
                            // ||($isAuth && $this->security->isGranted('ROLE_PRACOWNIK'))
                            // ||($isAuth && $this->security->isGranted('ROLE_CLIENT'));
                        }
                        else
                        {
                            return false;
                            // $documentInUserCase = false; 
                        }
                        
                    }   
                }
            case Documents::DELETE:
                    // logic to determine if the user can EDIT
                    // return true or 
                    // sprawdzić do jakiej sprawy jest przypisany dokument 
                    // potem sprawdzić czy id sprawy z dokumewntem odpowiada jakiemus id sprawy przypisanej do pracownika
                if($this->security->isGranted('ROLE_ADMIN'))
                {
                    return true;
                }
                elseif($subject->getAdoptionCase())
                {
                    $documentAdoptionCaseId = $subject->getAdoptionCase()->getId();// id sprawy adopcji przypisanej do dokumentu
                    if($this->security->isGranted('ROLE_PRACOWNIK'))
                    {
                        $employeeAdoptionCases = $user->getEmployeeAdoptionCases();
                        foreach($employeeAdoptionCases as $employeeAdoptionCase)
                        {
                            if($employeeAdoptionCase->getId() == $documentAdoptionCaseId)
                            {
                                return true;
                                //$documentInUserCase = true;
                                // break;
                            }
                            else
                            {
                                return false;
                                //$documentInUserCase = false; 
                            }
                            
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
        }

        return false;
    }
}
