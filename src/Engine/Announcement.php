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

	private string $linkAnchor;

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

	public function LinkAnchor(): string {
		return $this->linkAnchor;
	}

	public function __construct(LemuriaMessage $message, Dictionary $dictionary) {
		$type = $message->MessageType();
		if ($type instanceof AnnouncementInterface) {
			$type->init($message);
			$domain = $type->Report();
			$sender = $type->Sender();
			if (empty($sender)) {
				$this->sender = 'Anonyme Einheit';
			} elseif ($domain === Domain::Unit) {
				$this->sender = $this->setFrom($type->Sender());
			} else {
				$this->sender = $dictionary->get('domain.' . Domain::Party->name) . ' ' . $this->setFrom($type->Sender());
			}
			if ($domain === Domain::Unit) {
				$this->recipient  = $this->setTo($type->Recipient());
				$this->linkAnchor = '#unit-';
			} else {
				$this->recipient  = $dictionary->get('domain.' . $domain->name) . ' ' . $this->setTo($type->Recipient());
				$this->linkAnchor = $domain === Domain::Location ? '#location-' : '#unit-';
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
