<?php
/**
 * admin-playlist config file
 * @package admin-playlist
 * @version 0.0.1
 * @upgrade true
 */

return [
    '__name' => 'admin-playlist',
    '__version' => '0.0.1',
    '__git' => 'https://github.com/getphun/admin-playlist',
    '__files' => [
        'modules/admin-playlist' => [ 'install', 'remove', 'update' ],
        'theme/admin/component/playlist' => ['install', 'remove', 'update']
    ],
    '__dependencies' => [
        'admin'
    ],
    '_services' => [],
    '_autoload' => [
        'classes' => [
            'AdminPlaylist\\Controller\\PlaylistController'   => 'modules/admin-playlist/controller/PlaylistController.php',
            'AdminPlaylist\\Controller\\SongController'       => 'modules/admin-playlist/controller/SongController.php',
            'AdminPlaylist\\Model\\Playlist'                  => 'modules/admin-playlist/model/Playlist.php',
            'AdminPlaylist\\Model\\PlaylistSong'              => 'modules/admin-playlist/model/PlaylistSong.php'
        ],
        'files' => []
    ],
    
    '_routes' => [
        'admin' => [
            'adminPlaylist' => [
                'rule' => '/playlist',
                'handler' => 'AdminPlaylist\\Controller\\Playlist::index'
            ],
            'adminPlaylistEdit' => [
                'rule' => '/playlist/:id',
                'handler' => 'AdminPlaylist\\Controller\\Playlist::edit'
            ],
            'adminPlaylistRemove'   => [
                'rule' => '/playlist/:id/delete',
                'handler' => 'AdminPlaylist\\Controller\\Playlist::remove'
            ],
        
            'adminPlaylistSong' => [
                'rule' => '/playlist/:playlist/song',
                'handler' => 'AdminPlaylist\\Controller\\Song::index'
            ],
            'adminPlaylistSongEdit' => [
                'rule' => '/playlist/:playlist/song/:id',
                'handler' => 'AdminPlaylist\\Controller\\Song::edit'
            ],
            'adminPlaylistSongRemove' => [
                'rule' => '/playlist/:playlist/song/:id/remove',
                'handler' => 'AdminPlaylist\\Controller\\Song::remove'
            ]
        ]
    ],
    
    'admin' => [
        'menu' => [
            'component' => [
                'label'     => 'Component',
                'order'     => 1500,
                'icon'      => 'puzzle-piece',
                'submenu'   => [
                    'playlist'  => [
                        'label'     => 'Playlist',
                        'perms'     => 'read_playlist',
                        'target'    => 'adminPlaylist',
                        'order'     => 1000
                    ]
                ]
            ]
        ]
    ],
    
    'form' => [
        'admin-playlist' => [
            'name' => [
                'type'  => 'text',
                'label' => 'Name',
                'rules' => [
                    'required' => true
                ]
            ],
            'banner' => [
                'type'  => 'file',
                'label' => 'Banner',
                'rules' => [
                    'file' => 'image/*'
                ]
            ],
            'link'   => [
                'type'  => 'url',
                'label' => 'Banner Link',
                'rules' => [
                    'url' => true
                ]
            ],
            'about'   => [
                'type'  => 'textarea',
                'label' => 'About',
                'rules' => []
            ],
            'active'   => [
                'type'  => 'checkbox',
                'label' => 'Set as active',
                'rules' => []
            ],
            'placement' => [
                'type'  => 'text',
                'label' => 'Placement',
                'rules' => []
            ],
            'index' => [
                'type'  => 'number',
                'label' => 'Index',
                'rules' => [],
                'filters' => [
                    'number' => true
                ]
            ]
        ],
        
        'admin-playlist-song' => [
            'artist' => [
                'type'  => 'text',
                'label' => 'Artist',
                'rules' => [
                    'required' => true
                ]
            ],
            'title' => [
                'type'  => 'text',
                'label' => 'Title',
                'rules' => [
                    'required' => true
                ]
            ],
            'duration' => [
                'type'  => 'text',
                'label' => 'Duration',
                'rules' => [
                    'required' => true,
                    'regex'    => '!^[0-9]{1,2}:[0-9]{2}:[0-9]{2}$|^[0-9]{1,2}:[0-9]{2}$|^[0-9]{1,2}$!'
                ]
            ],
            'target' => [
                'type'  => 'url',
                'label' => 'Video URL',
                'rules' => [
                    'required' => true,
                    'url'   => true
                ]
            ],
            'index' => [
                'type'  => 'number',
                'label' => 'Order',
                'rules' => [
                    'numeric' => [
                        'min' => 1
                    ]
                ]
            ]
        ]
    ]
];