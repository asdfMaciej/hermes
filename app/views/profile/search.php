<script>var USERS = <?php echo json_encode($users, JSON_UNESCAPED_UNICODE);  ?>;</script>
<div id="profile-search">
   <h2>Wyniki wyszukiwania:</h2> 
   <profile-list :users="users"></profile-list>
</div>

<?php $this->nest("vue/profile-list.php"); ?>