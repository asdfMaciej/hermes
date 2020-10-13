		</div> <!-- content end -->
		<div class='page-footer' id="footer">
			Nie ma ćwiczenia? Piszcie - dodam.<br>
			<br>
            Przed zapisaniem treningu zróbcie dla pewności screenshota lub wypełnijcie najpierw inny dzienniczek treningowy.<br>Na 99% się to nie zdarzy, ale może nastąpić utrata danych. Better safe than sorry
            <br>
		</div>

		<?php foreach ($scripts_on_end as $script): ?>
			<script src="``auto_version($script)``"></script>
		<?php endforeach ?>
	</body>
</html>
