<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Engine;

use Lemuria\Engine\Fantasya\Message\Announcement as AnnouncementInterface;
use Lemuria\Engine\Fantasya\Message\LemuriaMessage;
use Lemuria\Exception\LemuriaException;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Domain;

class Announcement
{
	private ?string $from = null;

	private string $sender;

	private string $to;

	private string $recipient;

	private string $message;

	public function From(): ?string {
		return $this->from;
	}

	public function Sender(): string {
		return $this->sender;
	}

	public function To(): string {
		return $this->to;
	}

	public function Recipient(): string {
		return $this->recipient;
	}

	public function Message(): string {
		return $this->message;
	}

	public function __construct(LemuriaMessage $message, Dictionary $dictionary) {
		$type = $message->MessageType();
		if ($type instanceof AnnouncementInterface) {
			$domain = $type->Report();
			$sender = $type->Sender();
			if (empty($sender)) {
				$this->sender = 'Anonyme Einheit';
			} elseif ($domain === Domain::UNIT) {
				$this->sender = $this->setFrom($type->Sender());
			} else {
				$this->sender = $dictionary->get('domain.' . Domain::PARTY->name) . ' ' . $this->setFrom($type->Sender());
			}
			if ($domain === Domain::UNIT) {
				$this->recipient = $this->setTo($type->Recipient());
			} else {
				$this->recipient = $dictionary->get('domain.' . $domain->name) . ' ' . $this->setTo($type->Recipient());
			}
			$this->message = $type->Message();
			return $this;
		}
		throw new LemuriaException('No announcement message.');
	}

	protected function setFrom(string $sender): string {
		$start      = strrpos($sender, '[');
		$end        = strrpos($sender, ']');
		$this->from = substr($sender, $start + 1, $end - $start - 1);
		return substr($sender, 0, $start - 1);
	}

	protected function setTo(string $recipient): string {
		$start    = strrpos($recipient, '[');
		$end      = strrpos($recipient, ']');
		$this->to = substr($recipient, $start + 1, $end - $start - 1);
		return substr($recipient, 0, $start - 1);
	}
}
