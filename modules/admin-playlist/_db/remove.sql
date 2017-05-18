DROP TABLE IF EXISTS `playlist`;
DROP TABLE IF EXISTS `playlist_song`;

DELETE FROM `user_perms_chain` WHERE `user_perms` IN (
    SELECT `id` FROM `user_perms` WHERE `group` = 'Playlist'
);

DELETE FROM `user_perms` WHERE `group` = 'Playlist';