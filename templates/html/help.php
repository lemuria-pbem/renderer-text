<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

?>
<div class="modal" id="modal-help" tabindex="-1" aria-labelledby="modal-help-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 id="modal-help-label" class="modal-title fs-5">Tastenfunktionen</h1>
			</div>
			<div class="modal-body">
				<p>Die interaktiven Funktionen in dieser Auswertung können auch über einen
					Tastendruck aufgerufen werden.
				</p>
				<ul>
					<li>
						<span class="font-monospace">A</span>
						<strong>Bekannte Völker und Allianzen</strong> aufrufen
					</li>
					<li>
						<span class="font-monospace">E</span>
						<strong>Weitere Ereignisse</strong> - zur nächsten Meldung springen
					</li>
					<li>
						<span class="font-monospace">G</span>
						<strong>Gehe zu...</strong> aufrufen
					</li>
					<li>
						<span class="font-monospace">I</span>
						<strong>Inhalt</strong> einblenden
					</li>
					<li>
						<span class="font-monospace">K</span>
						<strong>Kräutervorkommen anzeigen</strong> aufrufen
					</li>
					<li>
						<span class="font-monospace">S</span>
						<strong>Statistik</strong> aufrufen
					</li>
					<li>
						<span class="font-monospace">Z</span>
						<strong>Zauberbuch anzeigen</strong> aufrufen
					</li>
					<li>
						<span class="font-monospace">#</span>
						<strong>Ansicht umschalten</strong> (Schriftart, Spaltenansicht)
					</li>
					<li>
						<span class="font-monospace">?</span>
						Dieses Hilfefenster aufrufen
					</li>
				</ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>
