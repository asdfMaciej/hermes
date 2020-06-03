<div class="landing-page">
	<!--<img src="``PATH_PREFIX``/static/img/logo.png" class="landing-page__logo">-->
	<div class="landing-page__text">
		<h1>
			Dziennik treningowy dla Ciebie
		</h1>

		<br>
		<h3>
			Śledź swoje postępy w gronie znajomych
		</h3>
		<p>
			Hermes to rozbudowana platforma do prowadzenia dzienniczka treningów.
			<br>Umożliwia dzielenie się swoimi wynikami z wybraną grupą osób.
		</p>

		<br>
		<h3>
			Poznaj kluczowe funkcje:
		</h3>
		
		<ul>
			<li>Tablica z obserwowanymi osobami</li>
			<li>Rekordy ćwiczeń na siłowni</li>
			<li>Grupy wiekowe oraz wagowe</li>
			<li>Porównywanie się z innymi</li>
		</ul>
		
		<br>
		<h4>
			Hermes jest cały czas rozwijany. Nie wszystkie funkcje zostały jeszcze zaimplementowane.
		</h4>
		<br><hr><br>

		<p>
			<b>Hermes to projekt open-source.</b>
			<br>Chcesz zobaczyć kod źródłowy, zgłosić błąd lub dodać jakąś funkcję?
			<br><a href="https://github.com/asdfMaciej/hermes">Wejdź na repozytorium projektu na Githubie.</a>
		</p>
	</div>
	
	<div class="landing-page__register">
		<h2>Zarejestruj się:</h2>
		<form action="``PATH_PREFIX``/" method="post">
			<input type="hidden" name="action" value="register">
			<input type="text" name="login" placeholder="Login"><br>
			<input type="password" name="password" placeholder="Hasło"><br>
			<input type="text" name="name" placeholder="Imię i nazwisko"><br>
			<input type="submit" value="Zarejestruj">
		</form>
	</div>	
</div>