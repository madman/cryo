<?php

namespace Users\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Users\Entity\User;

class UsersCommand
{
    protected $console;
    protected $app;

    public function __construct($console)
    {
        $this->console = $console;
        $this->app = $console->app;

        $console->register('user:admin:create')
            ->setDescription('Add ADMIN.')
            ->addArgument(
                'username',
                InputArgument::OPTIONAL,
                'Default value `admin@admin.loc`',
                'admin@admin.loc'
            )
            ->addArgument(
                'password',
                InputArgument::OPTIONAL,
                'Default value `admin`',
                'admin'
            )
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($console){
                if(User::manager()->findOne(['email'=>$input->getArgument('username')])){
                    throw new \Exception('User {'.$input->getArgument('username').'} already exists');
                }
                $user = new User;
                $user->setUsername($input->getArgument('username'));
                $user->setPassword($input->getArgument('password'));
                $user->addRole(\Users\Roles::ROLE_ADMIN);
                $user->encodePassword();
                $user->save();
                $output->writeln('<info>Admin {'.$input->getArgument('username').'} successfully created</info>');
            });

        $console->register('user:admin:allow')
            ->setDescription('Add ADMIN role to user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'User email')
            ])
            ->setCode(function (InputInterface $input, OutputInterface $output) {
                $email = $input->getArgument('email');

                $this->makeAdmin($email);
                $output->writeln('Successfully added admin role to user');
            });

        $console->register('user:admin:restrict')
            ->setDescription('Removes ADMIN role from user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'User email')
            ])
            ->setCode(function (InputInterface $input, OutputInterface $output) {
                $email = $input->getArgument('email');

                $this->rmRole($email);
                $output->writeln('Successfully removed admin role from user');
            });

        $console->register('user:changepassword')
            ->setDescription('Change user password')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'User email'),
                new InputArgument('password', InputArgument::REQUIRED, 'New user password'),
            ])
            ->setCode(function (InputInterface $input, OutputInterface $output) {
                $email = $input->getArgument('email');
                $password = $input->getArgument('password');

                $this->changePassword($email, $password);
                $output->writeln('Successfully changed password of user');
            });
    }

    protected function findUser($email)
    {
        $user = User::manager()->findByEmail($email);
        if (!$user) {
            throw new \Exception('User not found');
        }

        return $user;
    }

    protected function makeAdmin($email)
    {
        $user = $this->findUser($email);
        $user->addRole(\Users\Roles::ROLE_ADMIN);
        $user->save();
    }

    protected function rmRole($email)
    {
        $user = $this->findUser($email);
        $user->rmRole(\Users\Roles::ROLE_ADMIN);
        $user->save();
    }

    protected function changePassword($email, $password)
    {
        $user = $this->findUser($email);
        $user->password = $password;
        $user->encodePassword();
        $user->save();
    }
}