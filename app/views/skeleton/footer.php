		</div> <!-- content end -->
		<div class='page-footer' id="footer">
			Strona cały czas jest w rozwoju. Postaram się tego uniknąć, ale istnieje zawsze szansa na utratę danych.<br>
            Przed zapisaniem treningu zróbcie dla pewności screenshota lub wypełnijcie najpierw inny dzienniczek treningowy.<br>
            <br>
            Znane błędy:<br>
            - strona siłowni się rozwala i nie jest za funkcjonalna<br>
            - nie ma wszystkich ćwiczeń (napiszcie do mnie to dodam)<br>
            - popsuta ilość komentarzy w newsfeedzie<br>
            <br>
		</div>

		<?php foreach ($scripts_on_end as $script): ?>
			<script src="``auto_version($script)``"></script>
		<?php endforeach ?>
	</body>
</html>
