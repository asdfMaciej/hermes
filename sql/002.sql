ALTER TABLE `albums` ADD `title` VARCHAR(255) NOT NULL COLLATE 'utf8_polish_ci';
ALTER TABLE `workouts` ADD `album_id` INT(11) NULL DEFAULT NULL AFTER `gym_id`;
ALTER TABLE `workouts` ADD CONSTRAINT `FK_workouts_albums` FOREIGN KEY (`album_id`) REFERENCES `albums` (`album_id`) ON UPDATE CASCADE ON DELETE SET NULL;