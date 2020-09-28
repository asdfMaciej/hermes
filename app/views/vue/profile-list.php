<script type="text/x-template" id="profile-list-template">
    <div class="profile-list" id="profile-list">
        <a :href="'``PATH_PREFIX``/profile/' + user.user_id" v-for="user in users">
            <div class="profile-list-item">
                <img class="profile-list-item__avatar avatar" :src="'``PATH_PREFIX``/'+user.avatar">
                <div class="profile-list-item__user">
                    <h4>{{user.name}}</h4>
                    <div style="margin-top: -4px; margin-bottom: 4px" v-if="user.following == '1'">• Obserwujesz go</div>
                    <div style="font-size: 0.9em">
                        Dołączył <span class="date">{{time(user.register_date)}}</span>
                    </div>
                    <div style="font-size: 0.9em" v-if="user.frequency > 0">
                        Dodał {{user.frequency}}
                        {{user.frequency == 1 ? 'trening' : (user.frequency < 5 ? 'treningi' : 'treningów')}},
                        ostatni <span class="date">{{time(user.last_workout)}}</span>.
                    </div>
                    <div style="font-size: 0.9em" v-else>
                        Nie dodał żadnych treningów.
                    </div>

                </div>
            </div>
        </a>
        <h4 v-if="!users">
            Brak użytkowników.
        </h4>    
    </div>
</script>