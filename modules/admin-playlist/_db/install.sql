INSERT IGNORE INTO `user_perms` ( `name`, `group`, `role`, `about` ) VALUES
    ( 'read_playlist',   'Playlist', 'Management', 'Allow user to view playlist' ),
    ( 'update_playlist', 'Playlist', 'Management', 'Allow user to update exists playlist' ),
    ( 'remove_playlist', 'Playlist', 'Management', 'Allow user to remove exists playlist' ),
    ( 'create_playlist', 'Playlist', 'Management', 'Allow user to create new playlist' );

CREATE TABLE IF NOT EXISTS `playlist` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user` INTEGER NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `banner` VARCHAR(150),
    `link` VARCHAR(150),
    `about` TEXT,
    `placement` VARCHAR(100),
    `index` SMALLINT,
    `active` BOOLEAN DEFAULT FALSE,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `playlist_song` (
    `id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user` INTEGER NOT NULL,
    `playlist` INTEGER NOT NULL,
    `artist` VARCHAR(150) NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `duration` VARCHAR(10) NOT NULL,
    `target` VARCHAR(250) NOT NULL,
    `index` SMALLINT DEFAULT 0,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX `by_playlist` ON `playlist_song` ( `playlist` );
