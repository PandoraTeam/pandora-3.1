<?php
namespace Pandora3\Mailer;

use Pandora3\Contracts\ContainerInterface;
use Pandora3\Contracts\LoggerInterface;
use Pandora3\Mailer\Drivers\Mail;
use Pandora3\Mailer\Drivers\SMTP;
use Pandora3\Mailer\Exceptions\SendEmailFailedException;
use Pandora3\Mailer\Interfaces\MailTransportInterface;

/**
 * Class Mailer
 * @package Pandora3\Mailer
 */
class Mailer {

	public const LogChannel = 'Mailer';

	/** @var ContainerInterface */
	protected $container;
	
	/** @var LoggerInterface */
	protected $logger;

	/** @var string|null */
	protected $defaultDriver;

	/**
	 * @param ContainerInterface $container
	 * @param LoggerInterface $logger
	 * @param string|null $defaultDriver
	 */
	public function __construct(ContainerInterface $container, LoggerInterface $logger, ?string $defaultDriver = 'smtp') {
		$this->container = $container;
		$this->logger = $logger;
		$this->defaultDriver = $defaultDriver;
	}

	/**
	 * @param ContainerInterface $container
	 * @param array $config
	 */
	public static function use(ContainerInterface $container, array $config): void {
		$defaultDriver = $config['driver'] ?? null;
		$container->singleton(Mailer::class, static function(ContainerInterface $container) use ($defaultDriver) {
			return $container->build(Mailer::class, ['defaultDriver' => $defaultDriver]);
		});
		$container->singleton(Mail::class, static function() use ($config) {
			return new Mail($config['mail'] ?? []);
		});
		$container->singleton(SMTP::class, static function() use ($config) {
			$options = $config['smtp'] ?? null;
			if (!$options) {
				throw new \RuntimeException("Mailer config 'smtp' params not set");
			}
			$host = $options['host'] ?? null;
			$username = $options['username'] ?? null;
			$password = $options['password'] ?? null;
			if (!$host) {
				throw new \RuntimeException("Mailer SMTP config 'host' is not set");
			}
			if (!$username) {
				throw new \RuntimeException("Mailer SMTP config 'username' is not set");
			}
			if (!$password) {
				throw new \RuntimeException("Mailer SMTP config 'password' is not set");
			}
			return new SMTP($host, $username, $password, $options);
		});
	}

	/**
	 * @param string|null $driver
	 * @return MailTransportInterface
	 */
	protected function getTransport(?string $driver = null): MailTransportInterface {
		$driver = $driver ?? $this->defaultDriver;
		if ($driver === 'smtp') {
			$className = SMTP::class;
		} else if ($driver === 'mail') {
			$className = Mail::class;
		} else {
			throw new \RuntimeException("Mailer unsupported driver '{$driver}'");
		}
		return $this->container->make($className);
	}

	/**
	 * @param Email $email
	 * @param string|null $driver
	 * @throws SendEmailFailedException
	 */
	public function send(Email $email, ?string $driver = null) {
		$transport = $this->getTransport($driver);
		try {
			$transport->send($email);
			$message = $transport->getSentMIMEMessage();
			$this->logger->info("Email message sent\n".$message, [
				'email' => $email,
				'transport' => get_class($transport)
			], self::LogChannel);
		} catch (SendEmailFailedException $ex) {
			$message = $transport->getSentMIMEMessage();
			$this->logger->log('error', "Failed to send email message\n".$message, [
				'email' => $email,
				'transport' => get_class($transport)
			], self::LogChannel);
			throw $ex;
		}
	}

}