<?php

class CalendarController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        
        $calendars = Calendar::find(array('order' => 'name'));
        $this->view->setVar("calendars", $calendars);
    }
    
    public function createAction()
    {
        
        $calendar = new Calendar();
        
        $calendar->name = $this->request->getPost("name", "striptags");
       
        if (!$calendar->create()) {
        
            //The store failed, the following messages were produced
            foreach ($calendar->getMessages() as $message) {
                $this->flash->error((string) $message);
            }
            
        
        } else {
            $this->flash->success("Calendar was created successfully");
           
        }
        return $this->dispatcher->forward(
            array(
                'controller' => 'calendar',
                'action' => 'index')
        );
    }

    
    public function updateAction()
    {
        $id = $this->request->getPost("id", "int");
        
        $calendar = Calendar::findFirstById($id);
        if (!$calendar) {
            $this->flash->error("calendar does not exist " . $id);
            
        }

        $calendar->name = $this->request->getPost('name', 'striptags');
        if ($calendar->save()){
            $this->flash->success("Calendar was updated successfully");
        }
       
        return $this->dispatcher->forward(
            array(
                'controller' => 'calendar', 
                'action' => 'index')
            );
    }

    public function removeAction()
    {
        $id = $this->request->get("id", "int");
        
        $calendar = Calendar::findFirstById($id);
        if (!$calendar) {
            $this->flash->error("calendar does not exist " . $id);
        
        }
        
        if ($calendar->delete()){
            $this->flash->success("Calendar was deleted successfully");
        }
    
        return $this->dispatcher->forward(
            array(
                'controller' => 'calendar',
                'action' => 'index')
        );
    }
    
}