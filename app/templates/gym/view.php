<div class="gym-details">
	<div class="gym-details__name">
		{{$gym["name"]}}
	</div>
	<div class="gym-details__map">
		Lokalizacja siłowni: {{$gym["lat"] . ", " . $gym["long"]}}
	</div>
	<div class="gym-details__picture">
		<img src="{{PATH_PREFIX}}/{{$album[0]['path']}}" alt="">
	</div>
</div>