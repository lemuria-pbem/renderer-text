<?php
declare (strict_types = 1);

?>
<div class="btn-group">
	<button id="messages-button" type="button" class="btn btn-light" title="Taste: E">
		<span id="messages-button-count"></span>
		<span id="messages-button-text"></span>
	</button>
	<button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
    	<span class="visually-hidden">Einstellungen</span>
  	</button>
	<ul id="messages-button-config" class="dropdown-menu dropdown-menu-end">
		<li>
			<a class="dropdown-item" href="#" data-option="battle">
				<span></span>
				Kämpfe
			</a>
		</li>
		<li>
			<a class="dropdown-item" href="#" data-option="movement">
				<span></span>
				Schiffssichtungen
			</a>
		</li>
		<li>
			<a class="dropdown-item" href="#" data-option="guard">
				<span></span>
				Bewachung/Durchreise
			</a>
		</li>
	</ul>
</div>
