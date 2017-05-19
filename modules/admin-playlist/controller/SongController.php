<?php
/**
 * Playlist song management
 * @package admin-playlist
 * @version 0.0.1
 * @upgrade true
 */

namespace AdminPlaylist\Controller;
use AdminPlaylist\Model\Playlist;
use AdminPlaylist\Model\PlaylistSong as PSong;

class SongController extends \AdminController
{
    private function _defaultParams(){
        return [
            'title'             => 'Playlist',
            'nav_title'         => 'Component',
            'active_menu'       => 'component',
            'active_submenu'    => 'playlist',
            'total'             => 0,
            'pagination'        => []
        ];
    }
    
    public function editAction(){
        if(!$this->user->login)
            return $this->show404();
        if(!$this->can_i->update_playlist)
            return $this->show404();
        
        $params = $this->_defaultParams();
        
        $playlist = $this->param->playlist;
        if(!$playlist)
            return $this->show404();
        $playlist = Playlist::get($playlist, false);
        if(!$playlist)
            return $this->show404();
        $params['playlist'] = $playlist;
        
        $id = $this->param->id;
        
        $old = null;
        $params['title'] = 'Create New Playlist Song';
        
        $ref = $this->req->getQuery('ref');
        if(!$ref)
            $ref = $this->router->to('adminPlaylistSong', ['playlist'=>$playlist->id]);
        
        $params['ref'] = $ref;
        
        if($id){
            $params['title'] = 'Edit Playlist Song';
            $object = PSong::get($id, false);
            if(!$object)
                return $this->show404();
            $old = $object;
        }else{
            $object = new \stdClass();
            $object->user = $this->user->id;
            $object->playlist = $playlist->id;
            
            // let predict next index
            $last = PSong::get([
                'playlist = :id',
                'bind' => ['id' => $playlist->id]
            ], false, false, '`index` DESC');
            
            $object->index = $last ? ( $last->index + 1 ) : 1;
        }
        
        if(false === ($form=$this->form->validate('admin-playlist-song', $object)))
            return $this->respond('component/playlist/song/edit', $params);
        
        $object = object_replace($object, $form);
        
        if($id)
            PSong::set($object, $id);
        else
            $id = PSong::create($object);
        
        $this->fire('playlist:updated', $object, $old);
        
        return $this->redirect($ref);
    }
    
    public function indexAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->read_playlist)
            return $this->show404();
        
        $params = $this->_defaultParams();
        $params['playlist'] = null;
        $params['songs'] = [];
        $params['ref'] = $this->req->getQuery('ref') ?? $this->router->to('adminPlaylist');
        
        $playlist = $this->param->playlist;
        if(!$playlist)
            return $this->show404();
        $playlist = Playlist::get($playlist, false);
        if(!$playlist)
            return $this->show404();
        $params['playlist'] = $playlist;
        
        $songs = PSong::get([
            'playlist = :id',
            'bind'  => ['id' => $playlist->id]
        ], true, false, '`index` ASC');
        
        $total = $songs ? count($songs) : 0;
        $params['total'] = $total;
        $params['songs'] = $songs;
        
        return $this->respond('component/playlist/song/index', $params);
    }
    
    public function removeAction(){
        if(!$this->user->login)
            return $this->show404();
        if(!$this->can_i->update_playlist)
            return $this->show404();
        
        $playlist = $this->param->playlist;
        if(!$playlist)
            return $this->show404();
        $playlist = Playlist::get($playlist, false);
        if(!$playlist)
            return $this->show404();
        
        $id = $this->param->id;
        $object = PSong::get($id, false);
        if(!$object || $object->playlist != $playlist->id)
            return $this->show404();
        
        PSong::remove($id);
        
        $this->fire('playlist:updated', $playlist, $playlist);
        
        $ref = $this->req->getQuery('ref');
        if(!$ref)
            $ref = $this->router->to('adminPlaylistSong', ['playlist'=>$playlist->id]);
        $this->redirect($ref);
    }
}