<script type="text/x-template" id="reaction-button-template">
	<a href="#" @click.prevent='react' class='reaction-button' :class="{'liked': reacted == 1}">
		💪
		<span class="reaction-count">{{reactions}}</span>
	</a>
</script>