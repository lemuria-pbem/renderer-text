<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\PartyUnica;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party    = $this->party;
$spells   = $party->SpellBook()->count();
$herbs    = $party->HerbalBook()->count();
$unica    = new PartyUnica($party);
$treasury = $unica->Treasury();
$hasLinks = $spells + $herbs + $treasury->count();
$i        = $spells;

?>
<?php if ($hasLinks): ?>
	<p>
		<?php if ($spells > 0): ?>
			<a id="spell-book" href="<?= $this->spellBookPath() ?>" title="Taste: Z" target="spell-book">Zauberbuch anzeigen</a>
		<?php endif ?>
		<?php if ($herbs > 0): ?>
			<?= $i++ > 0 ? '·' : '' ?>
			<a id="herbal-book" href="<?= $this->herbalBookPath() ?>" title="Taste: K" target="herbal-book">Kräutervorkommen anzeigen</a>
		<?php endif ?>
		<?php foreach ($treasury as $unicum): ?>
			<?= $i++ > 0 ? '·' : '' ?>
			<a href="<?= $this->unicumPath($unicum) ?>" target="unicum-<?= $unicum->Id() ?>"><?= $this->composition($unicum->Composition()) ?> „<?= $unicum->Name() ?>”</a>
		<?php endforeach ?>
	</p>

	<div class="btn-group">
		<button id="documents-button" type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modal-documents" title="Taste: B">Berichte anschauen</button>
		<div class="modal" id="modal-documents" tabindex="-1" aria-labelledby="modal-map-label" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h1 id="modal-documents-label" class="modal-title fs-5">Berichte</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
					</div>
					<div class="modal-body">
						<ul class="nav nav-tabs" id="documents-tabs" role="tablist">
							<?php if ($spells > 0): ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link active" id="spell-book-tab" data-bs-toggle="tab" data-bs-target="#spell-book-tab-pane" type="button" role="tab" aria-controls="spell-book-tab-pane" aria-selected="true">Zauberbuch</button>
								</li>
							<?php endif ?>
							<?php if ($herbs > 0): ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link" id="herbal-book-tab" data-bs-toggle="tab" data-bs-target="#herbal-book-tab-pane" type="button" role="tab" aria-controls="herbal-book-tab-pane" aria-selected="false">Kräutervorkommen</button>
								</li>
							<?php endif ?>
							<?php foreach ($treasury as $unicum): ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link" id="unicum-<?= $unicum->Id() ?>-tab" data-bs-toggle="tab" data-bs-target="#unicum-<?= $unicum->Id() ?>-tab-pane" type="button" role="tab" aria-controls="unicum-<?= $unicum->Id() ?>-tab-pane" aria-selected="false"><?= $unicum->Name() ?></button>
								</li>
							<?php endforeach ?>
							<?php foreach ($this->hostilities() as $battle): ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link" id="battle-<?= $battle->Location()->Id() . '-' . $battle->Battle()->counter ?>-tab" data-bs-toggle="tab" data-bs-target="#battle-<?= $battle->Location()->Id() . '-' . $battle->Battle()->counter ?>-tab-pane" type="button" role="tab" aria-controls="battle-<?= $battle->Location()->Id() . '-' . $battle->Battle()->counter ?>-tab-pane" aria-selected="false">Kampf in <?= $battle->Location()->Name() ?></button>
								</li>
							<?php endforeach ?>
						</ul>
						<div class="tab-content" id="documents-tab-content">
							<?php if ($spells > 0): ?>
								<div class="tab-pane fade show active" id="spell-book-tab-pane" role="tabpanel" aria-labelledby="spell-book-tab" tabindex="0">
									<iframe src="<?= $this->spellBookPath() ?>" width="760" height="500"></iframe>
								</div>
							<?php endif ?>
							<?php if ($herbs > 0): ?>
								<div class="tab-pane fade" id="herbal-book-tab-pane" role="tabpanel" aria-labelledby="herbal-book-tab" tabindex="0">
									<iframe src="<?= $this->herbalBookPath() ?>" width="760" height="500"></iframe>
								</div>
							<?php endif ?>
							<?php foreach ($treasury as $unicum): ?>
								<div class="tab-pane fade" id="unicum-<?= $unicum->Id() ?>-tab-pane" role="tabpanel" aria-labelledby="unicum-<?= $unicum->Id() ?>-tab" tabindex="0">
									<iframe src="<?= $this->unicumPath($unicum) ?>" width="760" height="500"></iframe>
								</div>
							<?php endforeach ?>
							<?php foreach ($this->hostilities() as $battle): ?>
								<div class="tab-pane fade" id="battle-<?= $battle->Location()->Id() . '-' . $battle->Battle()->counter ?>-tab-pane" role="tabpanel" aria-labelledby="battle-<?= $battle->Location()->Id() . '-' . $battle->Battle()->counter ?>-tab" tabindex="0">
									<iframe src="<?= $this->battleLogPath($battle) ?>" width="760" height="500"></iframe>
								</div>
							<?php endforeach ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif ?>
