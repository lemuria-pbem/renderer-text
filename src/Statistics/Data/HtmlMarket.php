<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

class HtmlMarket extends HtmlCommodity
{
	public readonly string $moreOrLess;

	public readonly string $offerDemand;

	public function setIsOffer(bool $isOffer): HtmlMarket {
		$this->moreOrLess  = $isOffer ? 'less-is-good' : 'more-is-good';
		$this->offerDemand = $isOffer ? 'offer' : 'demand';
		return $this;
	}
}
