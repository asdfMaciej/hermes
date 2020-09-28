<div class="profile-details">
	<div class="profile-details__container">
        <img class="profile-details__avatar avatar" src="``PATH_PREFIX``/``$user['avatar']``">
		<h2 class="profile-details__name">
			``$user["name"]``
		</h2>

        <?php if ($user["user_id"] != $account["user_id"]): ?>
            <div class="profile-details__follow">
                <?php if ($user["following"]): ?>
                    <a href="``PATH_PREFIX``/profile/``$user['user_id']``/unfollow">Odobserwuj</a>
                <?php else: ?>
                    <a href="``PATH_PREFIX``/profile/``$user['user_id']``/follow" class="unfollowed">Zaobserwuj</a>
                <?php endif ?>
            </div>
        <?php else: ?>
            <a href="``PATH_PREFIX``/settings">Zmień swój avatar</a>
        <?php endif ?>
        
		<div class="profile-details__following">
            <div>
                <span>``$user["followers_count"]``</span>
                <span>Obserwujący</span>
            </div>
            <div>
                <span>``$user["following_count"]``</span>
                <span>Obserwuje</span>
            </div>
            <div>
                <span>``$user['workout_count']``</span>
                <span>Treningi</span>
            </div>
        </div>

		<div class="profile-details__register-date">
            Dołączył <span class="date">``$user["register_date"]``</span>
        </div>

        
	</div>
</div>
<div class="profile-heatmap">
    <h3>Dni treningowe:</h3>
    <div id="cal-heatmap"></div>
    <script type="text/javascript">
        // Get a date object for the current time
        var d = new Date();

        // Set it to one month ago
        d.setMonth(d.getMonth() - 2);

        var cal = new CalHeatMap();

        var data = <?php echo json_encode($gym_timeseries, JSON_UNESCAPED_UNICODE);  ?>;
        var timeseries = {};
        for (let key in data) {
            timeseries[moment(key).unix()] = parseInt(data[key]);
        }
        cal.init({
            itemSelector: '#cal-heatmap',
            domain: 'month',
            subDomain: 'day',
            range: 3,
            cellSize: 15,
            highlight: "now",
            start: d,
            data: timeseries,
            legend: [1, 2, 3],
            legendVerticalPosition: "center",
            legendOrientation: "vertical",
            legendMargin: [0, 10, 0, 0],
            legendColors: {
                min: "#e8b8b8",
                max: "#d32f2f",
                empty: "#d1d7d8",
                base: "#d1d7d8"
                // Will use the CSS for the missing keys
            },

            onComplete: () => {
                let box = document.querySelector("#cal-heatmap > svg");
                box.setAttribute("viewBox", `0 0 ${cal.graphDim.width} ${cal.graphDim.height}`);
            }
        });

    </script>
</div>
