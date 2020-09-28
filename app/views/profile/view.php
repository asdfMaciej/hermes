<script>var data = <?php echo json_encode($gym_timeseries, JSON_UNESCAPED_UNICODE);  ?>; var USER = <?php echo json_encode($user, JSON_UNESCAPED_UNICODE);  ?>; </script>
<div id="profile">
<div class="profile-details">
    <div class="profile-details__container">
        <img class="profile-details__avatar avatar" src="``PATH_PREFIX``/``$user['avatar']``">
        <h2 class="profile-details__name">
            ``$user["name"]``
        </h2>

        <?php if ($user["user_id"] != $account["user_id"]): ?>
            <div class="profile-details__follow">
                <a v-if="user.following == '1'" @click.prevent='unfollow()' href="#">Odobserwuj</a>
                <a v-else @click.prevent='follow()' href="#" class="unfollowed">Zaobserwuj</a>
            </div>
        <?php else: ?>
            <a class="profile-details__settings" href="``PATH_PREFIX``/settings">Zmień swój avatar</a>
        <?php endif ?>

        <div class="profile-details__following">
            <div>
                <span>{{user.followers_count}}</span>
                <span>Obserwujący</span>
            </div>
            <div>
                <span>{{user.following_count}}</span>
                <span>Obserwuje</span>
            </div>
            <div>
                <span>{{user.workout_count}}</span>
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
</div>
</div>