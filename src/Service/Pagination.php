<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Pagination
 * @package App\Service
 */
class Pagination
{
    private $className;
    private $limit = 10;
    private $currentPage = 1;
    private $entityManager;
    private $environmentTwig;
    private $templatePath;
    private $route;


    public function __construct($templatePath,RequestStack $requestStack,EntityManagerInterface $entityManager, Environment $environmentTwig)
    {
        $this->route = $requestStack->getCurrentRequest()->attributes->get('_route');
        $this->entityManager = $entityManager;
        $this->environmentTwig = $environmentTwig;
        $this->templatePath = $templatePath;
    }

    /**
     * @return false|float|void
     * @throws Exception
     */
    public function getPages()
    {
        if(empty($this->className)) {
            throw new \RuntimeException("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! 
            Utilisez la méthode setClassName() de votre objet Pagination !");
        }
        //Get repository
        $repository = $this->entityManager->getRepository($this->className);
        //Get total page
        $total = count($repository->findAll());
        //return pages number
        return ceil($total/$this->limit);
    }

    /**
     * @return object[]
     * @throws Exception
     */
    public function getData(): array
    {
        if(empty($this->className)) {
            throw new \RuntimeException("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! 
            Utilisez la méthode setClassName() de votre objet Pagination !");
        }

        //Get offset
        $offset = ($this->currentPage * $this->limit) - $this->limit;
        //Get repository
        $repository = $this->entityManager->getRepository($this->className);
        // Find and return data
       return $repository->findBy([],[],$this->limit,$offset);
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function display(): void
    {
        $this->environmentTwig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route,
        ]);
    }

    /**
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param mixed $templatePath
     * @return Pagination
     */
    public function setTemplatePath($templatePath): self
    {
        $this->templatePath = $templatePath;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     * @return Pagination
     */
    public function setRoute($route): self
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return Pagination
     */
    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return Pagination
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $className
     * @return Pagination
     */
    public function setClassName($className): self
    {
        $this->className = $className;

        return $this;
    }

}