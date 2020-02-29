<?php


namespace App\Entity;


class SearchUser
{
    /**
     * @var string|null
     */
    private $userName;

    /**
     * @var string|null
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $LastName;

    /**
     * @param string|null $userName
     */
    public function setUserName(?string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $LastName
     */
    public function setLastName(?string $LastName): void
    {
        $this->LastName = $LastName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->LastName;
    }
}