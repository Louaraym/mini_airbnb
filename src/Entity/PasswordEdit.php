<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;


class PasswordEdit
{

    /**
     * @SecurityAssert\UserPassword(
     * message = "Le mot de passe actuel est incorrect !"
     * )
     */
    private $oldPassword;

    /**
     * @Assert\Length(
     *      min = 4,
     *      minMessage = "Votre mot de passe doit avoir au moins {{ limit }} caratères",
     *      allowEmptyString = false
     * )
     */
    private $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Vos mots de passe doivent être identiques ")
     */
    public $confirmPassword;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
