<?php
namespace App\Command;

use App\Entity\Internship;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifCommand extends Command
{
    protected static $defaultName = 'NotifCommand';

    private $em;
    private $mailer;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Envoye un mail de notification aux modérateurs/administrateurs.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->em->getRepository(User::class)->findAll();
        $nb = $this->em->getRepository(Internship::class)->getNbInvisibleInternships();

        for($i = 0; $i < count($users); ++$i) {
            if ($users[$i]->is_modo()){
                $message = (new \Swift_Message("[BDD Stage] Stages en attente"))
                    ->setFrom('no-reply@bdd-stage.com')
                    ->setTo($users[$i]->getEmail())
                    ->setBody("{$users[$i]->getFullName()},

Il y a {$nb} stages en attente de validation.

L'équipe de BDD Stages");

                $this->mailer->send($message);
            }
        }
    }
}