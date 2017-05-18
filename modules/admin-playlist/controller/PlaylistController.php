<?php
/**
 * Playlist management
 * @package playlist
 * @version 0.0.1
 * @upgrade true
 */

namespace AdminPlaylist\Controller;
use AdminPlaylist\Model\Playlist;
use AdminPlaylist\Model\PlaylistSong as PSong;

class PlaylistController extends \AdminController
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
        
        $id = $this->param->id;
        if(!$id && !$this->can_i->create_playlist)
            return $this->show404();
        elseif($id && !$this->can_i->update_playlist)
            return $this->show404();
        
        $old = null;
        $params = $this->_defaultParams();
        $params['title'] = 'Create New Playlist';
        
        $next = $this->req->getQuery('ref');
        if($next)
            $next = $this->router->to('adminPlaylist');
        $params['next'] = $next;
        
        if($id){
            $params['title'] = 'Edit Playlist';
            $object = Playlist::get($id, false);
            if(!$object)
                return $this->show404();
            $old = $object;
        }else{
            $object = new \stdClass();
            $object->user = $this->user->id;
        }
        
        if(false === ($form=$this->form->validate('admin-playlist', $object)))
            return $this->respond('component/playlist/edit', $params);
        
        if(!isset($form->active))
            $form->active = 0;
        
        $object = object_replace($object, $form);
        
        $event = 'updated';
        
        if(!$id){
            $event = 'created';
            if(false === ($id = Playlist::create($object)))
                throw new \Exception(Playlist::lastError());
        }else{
            Playlist::set($object, $id);
        }
        
        $this->fire('playlist:'. $event, $object, $old);
        
        // remove active status of another playlist
        if($object->active){
            Playlist::set(['active' => false], [
                'active = 1 AND id != :id',
                'bind' => ['id' => $id]
            ]);
        }
        
        $this->redirect($next);
    }
    
    public function indexAction(){
        if(!$this->user->login)
            return $this->loginFirst('adminLogin');
        if(!$this->can_i->read_playlist)
            return $this->show404();
        
        $params = $this->_defaultParams();
        $params['playlists'] = [];
        
        $page = $this->req->getQuery('page');
        $rpp  = 20;
        if(!$page)
            $page = 1;
        
        $cond = [];
        if($this->req->getQuery('q'))
            $cond['q'] = $this->req->getQuery('q');
            
        $params['playlists'] = Playlist::get($cond, $rpp, $page, 'name ASC');
        
        $total = Playlist::count($cond);
        if($rpp < $total)
            $params['pagination'] = \calculate_pagination($page, $rpp, $total, 10, $cond);
        $params['total'] = $total;
        
        return $this->respond('component/playlist/index', $params);
    }
    
    public function removeAction(){
        if(!$this->user->login)
            return $this->show404();
        if(!$this->can_i->remove_playlist)
            return $this->show404();
        
        $id = $this->param->id;
        $object = Playlist::get($id, false);
        if(!$object)
            return $this->show404();
        
        PSong::remove(['playlist' => $object->id]);
        Playlist::remove($id);
        
        $this->fire('playlist:deleted', $object);
        
        $next = $this->req->getQuery('ref');
        if($next)
            return $this->redirect($next);
        
        return $this->redirectUrl('adminPlaylist');
    }
}