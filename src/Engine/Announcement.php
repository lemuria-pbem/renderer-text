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
	private string $sender;

	private string $recipient;

	private string $message;

	public function Sender(): string {
		return $this->sender;
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
				$this->sender = $type->Sender();
			} else {
				$this->sender = $dictionary->get('domain.' . Domain::PARTY->name) . ' ' . $type->Sender();
			}
			if ($domain === Domain::UNIT) {
				$this->recipient = $type->Recipient();
			} else {
				$this->recipient = $dictionary->get('domain.' . $domain->name) . ' ' . $type->Recipient();
			}
			$this->message   = $type->Message();
			return $this;
		}
		throw new LemuriaException('No announcement message.');
	}
}
